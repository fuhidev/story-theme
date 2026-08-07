#!/usr/bin/env python3
"""
Synchronizes project workspace agents/skills/knowledge base to the latest version.

Usage:
  python tools/sync-project.py <project-path>            # Sync (auto-updates fingerprint)
  python tools/sync-project.py <project-path> --check    # Check freshness only
  python tools/sync-project.py <project-path> --sync     # Force sync (same as default)

Check mode (--check) exit codes:
  0 = Already up to date
  1 = Updates available (or project missing fingerprint)
  2 = Invalid project path/project

Does NOT modify settings/, volumes/, chapters/, archives/, prompts/, or story.md.
"""

import hashlib
import subprocess
import sys
import os
import shutil
from pathlib import Path

for s in (sys.stdin, sys.stdout, sys.stderr):
    try:
        s.reconfigure(encoding="utf-8")
    except AttributeError:
        pass


SKILL_HOME = Path(__file__).parent.parent
AGENT_DIR = SKILL_HOME / "agents"
SKILL_DIR = SKILL_HOME / "skills"
KNOWLEDGE_DIR = SKILL_HOME / "knowledge"
FINGERPRINT_FILE = Path(".agent") / ".sync-fingerprint"
VERSION_FILE = Path(".agent") / ".sync-version"


def main():
    if "-h" in sys.argv or "--help" in sys.argv or len(sys.argv) < 2:
        print(__doc__.strip())
        return

    check_only = "--check" in sys.argv

    # Handle Windows paths from os.environ
    raw_arg = sys.argv[1]
    if raw_arg == "." and os.environ.get("PWD"):
        pwd = os.environ["PWD"]
        if os.path.exists(pwd):
            raw_arg = pwd
    project_path = Path(raw_arg).resolve()
    if not project_path.exists():
        print(f"Error: Path does not exist: {project_path}")
        sys.exit(2)

    status_file = project_path / ".agent" / "status.md"
    if not status_file.exists():
        print(f"Error: {project_path} is not a valid novel project (missing .agent/status.md)")
        sys.exit(2)

    if check_only:
        check_freshness(project_path)
        return

    do_sync(project_path)


# ============================================================
# Fingerprint Mechanism
# ============================================================

def get_latest_version() -> str | None:
    """Fetch latest skill version tag from git"""
    try:
        result = subprocess.run(
            ["git", "-C", str(SKILL_HOME), "describe", "--tags", "--abbrev=0"],
            capture_output=True, text=True, timeout=10,
        )
        if result.returncode == 0:
            return result.stdout.strip()
        return None
    except (FileNotFoundError, subprocess.TimeoutExpired):
        return None


def get_version_info() -> tuple[str | None, str | None]:
    """Returns (latest_tag, version_summary) for display"""
    tag = get_latest_version()
    if tag:
        return tag, tag
    return None, "unknown"


def compute_fingerprint() -> str:
    """Computes a SHA256 fingerprint of all agent/skill/knowledge source files"""
    files = []
    for base in [AGENT_DIR, SKILL_DIR, KNOWLEDGE_DIR]:
        if base.exists():
            for f in sorted(base.rglob("*")):
                if f.is_file() and f.name != ".gitkeep":
                    files.append(f)

    h = hashlib.sha256()
    for f in files:
        rel = f.relative_to(SKILL_HOME)
        h.update(str(rel).encode("utf-8"))
        h.update(f.read_bytes())
    return h.hexdigest()


def read_project_fingerprint(project: Path) -> tuple[str | None, str | None]:
    """Returns (fingerprint, version)"""
    fp = project / FINGERPRINT_FILE
    vp = project / VERSION_FILE
    finger = None
    version = None
    if fp.exists():
        finger = fp.read_text(encoding="utf-8").strip()
    if vp.exists():
        version = vp.read_text(encoding="utf-8").strip()
    return finger, version


def write_project_fingerprint(project: Path, fingerprint: str, version: str | None = None):
    fp = project / FINGERPRINT_FILE
    fp.parent.mkdir(parents=True, exist_ok=True)
    fp.write_text(fingerprint + "\n", encoding="utf-8")

    vp = project / VERSION_FILE
    if version:
        vp.parent.mkdir(parents=True, exist_ok=True)
        vp.write_text(version + "\n", encoding="utf-8")
    elif vp.exists():
        vp.unlink(missing_ok=True)


# ============================================================
# Freshness Check
# ============================================================

def check_freshness(project: Path):
    current = compute_fingerprint()
    stored, stored_ver = read_project_fingerprint(project)
    latest_ver, _ = get_version_info()

    if stored is None:
        print("Project missing sync fingerprint. Generated automatically upon running sync-project.py.")
        sys.exit(1)

    version_diff = latest_ver and stored_ver and stored_ver != latest_ver
    version_info = ""
    if version_diff:
        version_info = f"  [Version] Recorded: {stored_ver}  →  Latest: {latest_ver}"
    elif latest_ver and not stored_ver:
        version_info = f"  [Version] Latest: {latest_ver} (No version recorded)"

    if current == stored:
        if version_diff:
            print(f"Files are up to date. {version_info}")
            sys.exit(1)
        print("Already up to date.")
        sys.exit(0)
    else:
        changes = find_changes(project)
        lines = [f"Updates available ({len(changes)} files changed):"]
        for f in changes:
            lines.append(f"  - {f}")
        if version_info:
            lines.append(version_info)
        print("\n".join(lines))
        sys.exit(1)


def find_changes(project: Path) -> list[str]:
    """Returns list of changed files relative to source"""
    changed = []

    for name, src_dir in [("agents", AGENT_DIR), ("skills", SKILL_DIR), ("knowledge", KNOWLEDGE_DIR)]:
        if not src_dir.exists():
            continue
        for platform in [".gemini", ".claude", ".opencode"]:
            dst_dir = project / platform / name
            for item in sorted(src_dir.rglob("*.md")):
                if item.name == ".gitkeep":
                    continue
                rel = item.relative_to(src_dir)
                target = dst_dir / rel
                if not target.exists():
                    changed.append(f"{platform}/{name}/{rel}")
                else:
                    content = item.read_text(encoding="utf-8")
                    if name == "agents" and platform != ".claude":
                        content = content.replace(".claude/", f"{platform}/")
                    if target.read_text(encoding="utf-8") != content:
                        changed.append(f"{platform}/{name}/{rel}")

    return changed


# ============================================================
# Sync Execution
# ============================================================

def do_sync(project: Path):
    print(f"Project: {project}")
    print(f"Source: {SKILL_HOME}")

    latest_ver, _ = get_version_info()
    if latest_ver:
        print(f"Version: {latest_ver}")
    print()

    current_fp = compute_fingerprint()
    stored_fp, stored_ver = read_project_fingerprint(project)

    version_changed = latest_ver and stored_ver and stored_ver != latest_ver

    if stored_fp == current_fp and not version_changed:
        print("[i] Already up to date, no sync needed.")
        return

    changes = []
    changes.append(sync_agents(project))
    changes.append(sync_skills(project))
    changes.append(sync_knowledge(project))

    total = sum(c for c in changes if c > 0)

    if total > 0 or stored_fp != current_fp or version_changed:
        write_project_fingerprint(project, current_fp, latest_ver)

    print(f"\nCompleted. Synchronized {total} files. Version: {latest_ver or 'unknown'}")
    if total > 0:
        print("Notice: Takes effect in next writing session.")


def sync_agents(project_path: Path) -> int:
    """Sync agent definitions to destination platform directories (Antigravity, Claude Code, OpenCode)"""
    if not AGENT_DIR.exists():
        print("  [!] agents source directory does not exist, skipping")
        return 0
    total_count = 0
    agent_dirs = [
        (".gemini/agents", ".gemini"),
        (".claude/agents", ".claude"),
        (".opencode/agents", ".opencode"),
    ]
    for agent_dir, platform_prefix in agent_dirs:
        target = project_path / agent_dir
        target.mkdir(parents=True, exist_ok=True)
        count = 0
        for item in sorted(AGENT_DIR.rglob("*.md")):
            if item.name == ".gitkeep":
                continue
            rel = item.relative_to(AGENT_DIR)
            dst = target / rel
            dst.parent.mkdir(parents=True, exist_ok=True)
            content = item.read_text(encoding="utf-8")
            if platform_prefix != ".claude":
                content = content.replace(".claude/", f"{platform_prefix}/")

            if not dst.exists() or dst.read_text(encoding="utf-8") != content:
                dst.write_text(content, encoding="utf-8")
                count += 1

        total_count += count
        if count > 0:
            print(f"  [OK] Agent definitions: {count} files updated ({agent_dir})")
        else:
            print(f"  [i] Agent definitions: Already up to date ({agent_dir})")
    return total_count


def sync_skills(project_path: Path) -> int:
    if not SKILL_DIR.exists():
        print("  [!] skills source directory does not exist, skipping")
        return 0
    total_count = 0
    for platform in [".claude", ".gemini", ".opencode"]:
        target = project_path / platform / "skills"
        target.mkdir(parents=True, exist_ok=True)
        count = _sync_dir(SKILL_DIR, target, "*.md")
        total_count += count
        if count > 0:
            print(f"  [OK] Skill files ({platform}): {count} files updated")
        else:
            print(f"  [i] Skill files ({platform}): Already up to date")
    return total_count


def sync_knowledge(project_path: Path) -> int:
    if not KNOWLEDGE_DIR.exists():
        print("  [!] knowledge source directory does not exist, skipping")
        return 0
    total_count = 0
    for platform in [".claude", ".gemini", ".opencode"]:
        target = project_path / platform / "knowledge"
        target.mkdir(parents=True, exist_ok=True)
        count = 0
        for f in KNOWLEDGE_DIR.glob("*.md"):
            if _sync_file(f, target / f.name):
                count += 1
        for subdir in KNOWLEDGE_DIR.iterdir():
            if subdir.is_dir() and not subdir.name.startswith("."):
                sub_target = target / subdir.name
                sub_target.mkdir(parents=True, exist_ok=True)
                count += _sync_dir(subdir, sub_target, "*.md")
        total_count += count
        if count > 0:
            print(f"  [OK] Knowledge base ({platform}): {count} files updated")
        else:
            print(f"  [i] Knowledge base ({platform}): Already up to date")
    return total_count


def _sync_dir(src: Path, dst: Path, pattern: str) -> int:
    count = 0
    for item in sorted(src.rglob(pattern)):
        if item.name == ".gitkeep":
            continue
        rel = item.relative_to(src)
        target = dst / rel
        target.parent.mkdir(parents=True, exist_ok=True)
        if _sync_file(item, target):
            count += 1
    return count


def _sync_file(src: Path, dst: Path) -> bool:
    if dst.exists() and dst.read_bytes() == src.read_bytes():
        return False
    dst.parent.mkdir(parents=True, exist_ok=True)
    shutil.copy2(src, dst)
    return True


if __name__ == "__main__":
    main()
