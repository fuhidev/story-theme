#!/usr/bin/env python3
"""
awesome-novel-skill Project Initialization Tool

Usage: python init.py [project-path] [--genre <ID>]

Copies agent definitions, genre knowledge, and memory rules from the skill repository
to the user project directory across supported platforms (.gemini, .claude, .opencode),
creating a complete novel writing project skeleton.

Defaults to initializing in the current directory when no parameters are provided.
"""

import sys
import os
import shutil
from pathlib import Path

# Force UTF-8 encoding
for s in (sys.stdin, sys.stdout, sys.stderr):
    try:
        s.reconfigure(encoding="utf-8")
    except AttributeError:
        pass


GENRES = [
    "xianxia", "xuanhuan", "urban", "urban-romance", "urban-daily",
    "urban-farming", "urban-brained", "western-fantasy", "ancient-politics",
    "historical", "anti-japanese-war", "scifi-apocalypse", "war-god",
    "suspense-crime", "suspense-paranormal", "anime-derivative",
    "derivative", "fanqie",
]

SKILL_HOME = Path(os.environ.get("NOVEL_SKILL_HOME", Path(__file__).parent.parent))

SOURCE_AGENTS = SKILL_HOME / "agents"
SOURCE_KNOWLEDGE = SKILL_HOME / "knowledge"
SOURCE_TEMPLATES = SKILL_HOME / "templates"
SOURCE_MEMORY = SKILL_HOME / "memory"  # no-op since anti-ai/writer-style moved to knowledge/
SOURCE_ANTI_AI = SKILL_HOME / "knowledge" / "anti-ai"
SOURCE_WRITER_STYLE = SKILL_HOME / "memory" / "writer-style"  # optional
SOURCE_GENRE_EXAMPLE = SKILL_HOME / "knowledge" / "genre-example"
SOURCE_FORMAT_SPECS = SKILL_HOME / "knowledge" / "format-specs"


def main():
    if "-h" in sys.argv or "--help" in sys.argv:
        print(__doc__.strip())
        return

    if len(sys.argv) >= 2 and not sys.argv[1].startswith("--"):
        project_path = Path(sys.argv[1]).resolve()
    else:
        project_path = Path.cwd()

    # Parse optional parameters
    genre = None
    if "--genre" in sys.argv:
        idx = sys.argv.index("--genre")
        if idx + 1 < len(sys.argv):
            try:
                genre = GENRES[int(sys.argv[idx + 1]) - 1]
            except (IndexError, ValueError):
                print(f"Invalid genre ID, selectable range: 1-{len(GENRES)}")
                sys.exit(1)

    if project_path.exists():
        print(f"Directory already exists. Missing files and directories will be created inside.")
    else:
        project_path.mkdir(parents=True)

    print(f"Initializing novel project: {project_path}")
    print(f"Skill repository: {SKILL_HOME}")

    # Step 1: Select Genre
    if genre is None:
        genre = select_genre()
    else:
        print(f"Genre: {genre}")

    # Step 2: Create Skeleton
    create_skeleton(project_path)

    # Step 3: Deploy agent definitions
    deploy_agents(project_path)

    # Step 4: Inherit memory by genre
    deploy_memory(project_path, genre)

    # Step 5: Inherit knowledge by genre
    deploy_knowledge(project_path, genre)

    # Step 6: Generate MEMORY.md index
    write_memory_index(project_path)

    # Step 7: Initialize writing memory files
    init_memory_files(project_path)

    # Step 8: Initialize status
    write_status(project_path)

    print(f"\nInitialization complete!")
    print(f"Project path: {project_path}")
    print(f"Type @novel-agent to start writing (Antigravity / Gemini / Claude Code / OpenCode)")


def select_genre() -> str:
    """Interactive genre selection"""
    print("\nSelectable genres:")
    for i, g in enumerate(GENRES, 1):
        print(f"  {i:2d}. {g}")

    while True:
        try:
            choice = input("\nSelect genre ID: ").strip()
            idx = int(choice) - 1
            if 0 <= idx < len(GENRES):
                return GENRES[idx]
        except ValueError:
            pass
        print("Invalid selection, please try again.")


def create_skeleton(project_path: Path):
    """Create project directory structure"""
    dirs = [
        "settings/character-setting",
        "volumes",
        "chapters",
        "prompts",
        "sandbox",
        "archives",
        ".agent/task",
    ]
    for platform in [".gemini", ".claude", ".opencode"]:
        dirs.extend([
            f"{platform}/memory",
            f"{platform}/knowledge",
        ])

    for d in dirs:
        (project_path / d).mkdir(parents=True, exist_ok=True)

    # Copy template files into project (skip migration/ — old project upgrade only)
    if SOURCE_TEMPLATES.exists():
        for item in SOURCE_TEMPLATES.rglob("*"):
            if item.is_file() and item.name != ".gitkeep":
                rel_path = item.relative_to(SOURCE_TEMPLATES)
                if rel_path.parts[0] == "migration":
                    continue
                target = project_path / rel_path
                shutil.copy2(item, target)
        print("  ✅ Copied project templates")


def deploy_agents(project_path: Path):
    """Copy agent definitions to destination directories across supported platforms (Antigravity, Claude Code, OpenCode)"""
    if not SOURCE_AGENTS.exists():
        print("  ⚠️ agent directory does not exist, skipping")
        return

    agent_dirs = [
        (".gemini/agents", ".gemini"),
        (".claude/agents", ".claude"),
        (".opencode/agents", ".opencode"),
    ]
    for agent_dir, platform_prefix in agent_dirs:
        target = project_path / agent_dir
        target.mkdir(parents=True, exist_ok=True)
        for item in SOURCE_AGENTS.rglob("*"):
            if item.is_file() and item.suffix == ".md":
                rel_path = item.relative_to(SOURCE_AGENTS)
                dest = target / rel_path
                dest.parent.mkdir(parents=True, exist_ok=True)
                content = item.read_text(encoding="utf-8")
                if platform_prefix != ".claude":
                    content = content.replace(".claude/", f"{platform_prefix}/")
                dest.write_text(content, encoding="utf-8")
        print(f"  ✅ Deployed agent definitions to {agent_dir}")


def deploy_memory(project_path: Path, genre: str):
    """Initialize memory directory (placeholder, anti-AI/writer-style moved to knowledge)"""
    pass


def deploy_knowledge(project_path: Path, genre: str):
    """Copy reference materials + Anti-AI/style rules to .gemini/knowledge/, .claude/knowledge/, .opencode/knowledge/ by genre"""
    for platform in [".gemini", ".claude", ".opencode"]:
        knowledge_dir = project_path / platform / "knowledge"
        knowledge_dir.mkdir(parents=True, exist_ok=True)
        count = 0

        # Copy format specs from knowledge/format-specs/
        if SOURCE_FORMAT_SPECS.exists():
            for f in SOURCE_FORMAT_SPECS.glob("*.md"):
                shutil.copy2(f, knowledge_dir / f.name)
                count += 1

        # Genre example
        genre_example_src = SOURCE_GENRE_EXAMPLE / f"{genre}.md"
        if genre_example_src.exists():
            shutil.copy2(genre_example_src, knowledge_dir / "genre-example.md")
            count += 1

        # Anti-AI rules: General + Genre
        anti_ai_content = []
        anti_ai_content.append("# Anti-AI Rules\n\n[community-defaults]\n")
        common_rules = SOURCE_ANTI_AI / "common-rules.md"
        if common_rules.exists():
            anti_ai_content.append(common_rules.read_text(encoding="utf-8"))

        genre_rules = SOURCE_ANTI_AI / f"{genre}.md"
        if genre_rules.exists():
            anti_ai_content.append(f"\n[community-defaults] Genre: {genre}\n")
            anti_ai_content.append(genre_rules.read_text(encoding="utf-8"))

        if anti_ai_content:
            (knowledge_dir / "anti-ai.md").write_text(
                "\n".join(anti_ai_content), encoding="utf-8"
            )
            count += 1

        # Writing Style Preferences
        style_dir = SOURCE_WRITER_STYLE / genre
        if style_dir.exists():
            style_content = []
            for sf in style_dir.glob("*.md"):
                style_content.append(sf.read_text(encoding="utf-8"))
            if style_content:
                (knowledge_dir / "writer-style.md").write_text(
                    f"# Writing Style Preferences\n\n[community-defaults] Genre: {genre}\n\n"
                    + "\n".join(style_content),
                    encoding="utf-8",
                )
                count += 1

        # Permanent memory placeholder file
        permanent_memory = knowledge_dir / "permanent-memory.md"
        if not permanent_memory.exists():
            permanent_memory.write_text(
                "# Permanent Memory\n\n> High-frequency entries promoted from memory/, "
                "maintained by updater during sweep cycles.\n\n"
                "---\n\n## Entry List\n",
                encoding="utf-8",
            )
            count += 1

        # Craft methodology directories (plot-craft / scene-craft / character-craft / title-craft)
        craft_dirs = ["plot-craft", "scene-craft", "character-craft", "title-craft"]
        for dir_name in craft_dirs:
            src = SOURCE_KNOWLEDGE / dir_name
            if src.exists() and src.is_dir():
                dst = knowledge_dir / dir_name
                shutil.copytree(src, dst, dirs_exist_ok=True)
                file_count = sum(1 for _ in src.rglob("*") if _.is_file())
                count += file_count

        print(f"  ✅ Inherited {count} knowledge files to {platform}/knowledge")


def write_status(project_path: Path):
    """Initialize .agent/status.md"""
    status = """# Project Status

- **skill_version:** 4.0
- **phase:** setup
- **current_volume:**
- **current_chapter:**
- **last_archived:**
- **next_task:** Fill basic settings (world-view/characters/writing-style)
"""
    (project_path / ".agent" / "status.md").write_text(status, encoding="utf-8")


def write_memory_index(project_path: Path):
    """Generate MEMORY.md placeholder index across platforms"""
    for platform in [".gemini", ".claude", ".opencode"]:
        memory_dir = project_path / platform / "memory"
        memory_dir.mkdir(parents=True, exist_ok=True)
        (memory_dir / "MEMORY.md").write_text("# Writing Memory Repository\n\n(No memory recorded yet)\n", encoding="utf-8")


MEMORY_FILES = {
    "volume-memory.md": (
        "# Volume Outline Memory\n\n> Records author feedback during volume outline planning "
        "(conflict design, pacing, chapter structure, etc.).\n\n**Related Agent:** volume-planner\n"
        "**Related Skill:** volume-arc, volume-direction, volume-writing\n\n"
        "---\n\n## Entry List\n"
    ),
    "chapter-memory.md": (
        "# Chapter Outline Memory\n\n> Records author feedback during chapter outline planning "
        "(scene design, emotional pacing, foreshadowing, etc.).\n\n**Related Agent:** chapter-planner\n"
        "**Related Skill:** chapter-reference, chapter-outline, chapter-verify\n\n"
        "---\n\n## Entry List\n"
    ),
    "prompt-memory.md": (
        "# Prompt Assembly Memory\n\n> Records author feedback during prompt assembly "
        "(layer structure, injection rules, instruction clarity, etc.).\n\n**Related Agent:** prompt-crafter\n"
        "**Related Skill:** prompt-crafting\n\n"
        "---\n\n## Entry List\n"
    ),
    "writing-memory.md": (
        "# Manuscript Writing Memory\n\n> Records author feedback during manuscript writing and reader review "
        "(writing style, payoffs, pacing, descriptions, etc.).\n\n**Related Agent:** writer, reader\n"
        "**Related Skill:** writing-execution, reader-review\n\n"
        "---\n\n## Entry List\n"
    ),
}


def init_memory_files(project_path: Path):
    """Initialize 4 writing memory files across platforms"""
    for platform in [".gemini", ".claude", ".opencode"]:
        memory_dir = project_path / platform / "memory"
        memory_dir.mkdir(parents=True, exist_ok=True)
        for filename, content in MEMORY_FILES.items():
            filepath = memory_dir / filename
            if not filepath.exists():
                filepath.write_text(content, encoding="utf-8")
        print(f"  ✅ Initialized 4 writing memory files in {platform}/memory")


if __name__ == "__main__":
    main()
