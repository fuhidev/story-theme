---
name: awesome-novel
description: An AI-collaborative novel writing workflow system. 7 agents collaborate across a complete process from setup to archival. Entry detection -> Initialization/Migration -> Handover to novel-agent. Suitable for starting new novels from scratch or importing existing ones.
---

# Novel — Novel Writing Workflow

Write novels together with AI. This skill manages project status detection, new project initialization, and legacy project auto-migration. Once completed, control is transferred to `novel-agent`.

> [!CRITICAL]
> **MANDATORY SUBAGENT INVOCATION DIRECTIVE FOR GOOGLE ANTIGRAVITY (AGY):**
> Whenever executing `/awesome-novel` or handling novel writing workflows in Google Antigravity (AGY):
> You **MUST FIRST register subagents using `define_subagent`** and then **call `invoke_subagent`** for each step:
> 1. Read agent definition file `agents/{subagent}.md` (or `.gemini/agents/{subagent}.md`).
> 2. Call `define_subagent` (with `name`, `description`, `system_prompt`, `enable_write_tools: true`).
> 3. Write order file to `.agent/task/{type}-order.md`.
> 4. Call `invoke_subagent` to spawn the subagent:
>    - `@updater` (Phase 1 / Setup / Lore-keeping)
>    - `@volume-planner` (Phase 2 / Volume outlines under `volumes/`)
>    - `@chapter-planner` (Phase 3 / Chapter outlines under `chapters/`)
>    - `@prompt-crafter` (Phase 4 / Prompts under `prompts/`)
>    - `@writer` (Phase 5 / Manuscripts under `archives/`, sequential chapter-by-chapter)
>    - `@reader` (Phase 5 / Beta reading & QA review)
>    - `@trailer-crafter` (Phase 6 / Global AI Video Trailer Prompt under `prompts/trailers/`)
>    - `@social-crafter` (Phase 7 / Viral Facebook Story Hook Post under `prompts/social-hook.md`)
> **DO NOT write chapter manuscripts, outlines, volume outlines, trailer prompts, or social posts directly with `write_to_file` in your main conversation context. ALWAYS define and invoke subagents.**

## Multi-Platform Integration (Antigravity / OpenCode / Claude Code)

This skill supports Google Antigravity, OpenCode, and Claude Code:
- **Google Antigravity (AGY):** The primary agent reads agent prompts from `agents/` (or `.gemini/agents/`), registers each subagent via `define_subagent`, and spawns child sub-agents via `invoke_subagent`.
- **OpenCode:** Deploys agent definitions to `.opencode/agents/`. `novel-agent` dispatches sub-agents via the OpenCode Task tool.
- **Claude Code:** Deploys agent definitions to `.claude/agents/`. `novel-agent` dispatches sub-agents via the Claude Agent tool.

**Dispatch Mechanism:** `novel-agent` writes an order file to `.agent/task/{type}-order.md` -> Registers subagent (`define_subagent` in AGY) -> Invokes target subagent (`invoke_subagent` / Task tool / Agent tool) -> Sub-agent reads order and executes -> Sub-agent cleans up order file -> `novel-agent` confirms completion.

## Detection Workflow — Execute strictly, DO NOT skip

```
Detect Project Status
├─ story.yaml exists → Legacy 2.x → Execute auto-migration (see below)
├─ story.md does not exist → Ask author if initialization is needed → If yes, run init.py
│   └─ python tools/init.py [project-path] [--genre <ID>] → Upon completion, handover to @novel-agent
└─ story.md exists → Existing project
    ├─ Check sync freshness
    │   ├─ python tools/sync-project.py . --check → exit 0 → Already up to date, skip
    │   ├─ python tools/sync-project.py . --check → exit 1 → Updates available
    │   │   └─ Display changed files, ask author whether to sync
    │   │       ├─ Confirm → Run python tools/sync-project.py .
    │   │       └─ Skip → Continue
    │   └─ .agent/.sync-fingerprint does not exist (First time)
    │       └─ Silently run python tools/sync-project.py . → Write fingerprint
    └─ → @novel-agent Continue writing
```

**Mandatory Rules:**
- If `story.md` does not exist, **ask the author first** whether to create a novel project in this directory. Run `init.py` only after confirmation.
- Do NOT run `init.py` directly without author confirmation.
- Once confirmed, `init.py` MUST be executed. Manually creating directory structures instead is prohibited.
- **DO NOT run `init.py` inside the skill installation directory (including paths containing `skills/awesome-novel`)** — this directory is a skill repository, not a novel project.
- If current directory is the skill installation directory, prompt the author to switch to the target directory before executing.
- After `init.py` finishes, confirm `.agent/status.md` and `.opencode/agents/` have been generated before entering `@novel-agent`.
- If `init.py` reports an error, fix the issue and re-execute. Bypass is not allowed.

## Initialization — Ask first, confirm before execution, non-skippable

For brand new projects, ask the author whether to initialize first. Upon confirmation, run `init.py` (project path optional, default current directory):
```
python tools/init.py [project-path] [--genre <ID>]
```

**DO NOT skip init.py for any reason:** Manually creating directories, copying templates, or calling agents directly are violations. `init.py` is the entry point for initialization and must run to completion.

`init.py` will:
1. Select genre
2. Create project skeleton (`settings/`, `volumes/`, `chapters/`, `prompts/`, `archives/`)
3. Deploy agent definitions to `.opencode/agents/` (OpenCode) or `.claude/agents/` (Claude Code)
4. Inherit anti-AI rules and writing style preferences by genre into `.claude/knowledge/`
5. Inherit formatting specifications and genre examples into `.claude/knowledge/`
6. Create empty writing memory files (`.claude/memory/*.md`)
7. Create permanent memory placeholder file (`.claude/knowledge/permanent-memory.md`)
8. Generate `CLAUDE.md` (`AGENTS.md` under OpenCode)
9. Initialize status file `.agent/status.md`

All 9 steps above are completed automatically by `init.py`; AI should not intervene manually.

**Check:** Confirm `.agent/status.md` exists and content is correct after running before entering `@novel-agent`.

## Setting Discussion — Discussed by novel-agent & author, written by updater

Enter `@novel-agent` after `init.py`. Currently `phase=setup`. Follow this workflow:

1. **novel-agent detects setup phase**, discusses settings item-by-item with author (world-view/characters/style/genre). If author needs title suggestions, refer to methodology in `knowledge/title-craft/index.md`.
2. After discussion, novel-agent **writes order file** `.agent/task/setting-update-order.md`.
3. novel-agent **calls updater via Agent tool**.
4. **updater reads order**, writes setting files such as `settings/world-setting.md`, `settings/genre-setting.md`, `settings/character-setting/*.md`.
5. updater cleans up order file and finishes.
6. **novel-agent confirms order is cleaned**, advances phase → outline, enters volume planning.

**Permission Rules:** `novel-agent` must not directly edit files under `settings/`. Setting updates must be completed via updater's setting-update mode.

## Auto Migration (2.x → 3.0)

When `story.yaml` is detected, execute auto-migration following this process:

### Step 1: Display Migration Plan

Scan project directory and display three checklists for the author:

**File Checklist:**
- Settings files: `story.yaml` + all files under `settings/`
- Character files: all files under `settings/character-setting/`
- Volume outlines: all files under `volumes/`
- Manuscripts: count of `.md` files under `archives/`
- Chapter outlines (Archived): count of chapters with `status: archived` under `chapters/`
- Chapter outlines (Skipped): list of chapters with `status != archived` under `chapters/`
- Prompts: count of files under `prompts/`

**Deprecated Cleanup (Discard directly):**
- `author-intent.md`, `current-focus.md`
- `drafts/`, `drifts/`, `tmp/`, `temp-*.txt`
- `manuscripts/`, `.vscode/`

**Proceed after author confirmation.**

### Step 2: Backup Legacy Files

```bash
mkdir -p old
mv story.yaml settings/ volumes/ chapters/ archives/ prompts/ old/
rm -rf drafts/ drifts/ tmp/ manuscripts/ .vscode/ author-intent.md current-focus.md
```

### Step 3: Initialize New Skeleton

```bash
python tools/init.py [project-path] [--genre <ID>]
```

`init.py` creates directory structure + empty templates + agent definitions + memory/knowledge base. Subsequent migration steps handle data population.

### Step 4: Migrate Settings (File-by-file mapping per templates/migration/)

Convert files in order of priority per the field mapping table in `templates/migration/migration-spec.md`:

| Priority | Legacy File → New File | Template Reference |
|----------|------------------------|--------------------|
| P0 | `old/settings/character-setting/*.yaml` → `settings/character-setting/*.md` | `templates/migration/character.md.template` |
| P1 | `old/story.yaml` + `old/volumes/*.yaml` → `story.md` | `templates/migration/story.md.template` |
| P2 | `old/volumes/*.yaml` → `volumes/volume-{N}.md` | `templates/migration/volume.md.template` |
| P3 | `old/chapters/*.yaml` (archived) → `chapters/vol-{N}-ch-{M}.md` | `templates/migration/chapter.md.template` |
| P4 | `old/settings/world-setting.yaml` → `settings/world-setting.md` | `templates/migration/world-setting.md.template` |
| P5 | `old/settings/writing-style.yaml` → `settings/writing-style.md` | `templates/migration/writing-style.md.template` |
| P6 | `old/settings/anti-ai.yaml` → `settings/anti-ai.md` | `templates/migration/anti-ai.md.template` |
| P7 | `old/settings/hooks.yaml` → `settings/foreshadowing.md` | `templates/migration/foreshadowing.md.template` |
| P8 | No legacy source → `settings/genre-setting.md` | `templates/migration/genre-setting.md.template` |

Detailed field mappings are fully defined in `templates/migration/migration-spec.md`.

### Step 5: Copy Archived Manuscripts + Prompts

Copy finalized manuscripts only (excluding `.draft.md`), copy all prompt files:

```bash
# Manuscripts: copy finalized only (skip drafts)
for f in old/archives/*.md; do
  [ -f "$f" ] || continue
  case "$f" in *.draft.md) ;; *) cp "$f" archives/ ;; esac
done
cp old/prompts/*.md prompts/ 2>/dev/null
cp old/prompts/*.txt prompts/ 2>/dev/null
```

Manuscript content is preserved without modification.

### Step 6: Acceptance

- [ ] `story.md` exists, `skill_version = 4.0`
- [ ] `settings/world-setting.md` exists and is populated
- [ ] `settings/writing-style.md` exists and is populated
- [ ] `settings/genre-setting.md` exists
- [ ] `settings/anti-ai.md` exists
- [ ] `settings/foreshadowing.md` exists
- [ ] Character count in `settings/character-setting/` matches legacy version
- [ ] Volume count in `volumes/` matches legacy version
- [ ] All archived chapters in `chapters/` migrated
- [ ] All manuscripts in `archives/` copied
- [ ] All prompt files in `prompts/` copied
- [ ] Legacy `.yaml` files moved to `old/` (no residual files)
- [ ] Deprecated files cleaned up

### Step 7: Handover to novel-agent for Evaluation and Completion

Upon migration completion, dispatch `@novel-agent` to execute:

1. **Project Space Assessment** — Scan all migrated files against acceptance checklist to identify missing items.
2. **Completion Decision** — Determine which agent handles missing items:
   - Missing settings (world-view/characters/style/genre, etc.) → Dispatch `updater` (setting-update mode)
   - Others → Ask author directly
3. **Step-by-Step Guided Completion** — Dispatch one agent at a time to complete missing items, then evaluate next item until project is ready.
4. **Report Readiness** — When fully ready, display migration + completion results to author, then enter writing loop. Author can manually delete `old/` directory after verification.

## Edge Cases

| Scenario | Handling |
|----------|----------|
| `story.yaml` exists → `story.md` does not exist | Legacy 2.x → Execute auto-migration |
| `story.md` exists but `skill_version` < 4.0 | Pending upgrade → Execute auto-migration |
| `story.md` exists and version matches | Existing project → `@novel-agent` |
| Neither exists | Brand new project → `init.py` → `@novel-agent` |
| `init.py` unavailable | Manually create directory structure + copy files from `templates/` |
| Uncommitted git changes detected | Prompt author to commit or stash first |

## Project Directory Structure

```
{project-name}/
├── story.md              # ★ Project Index
├── settings/
│   ├── world-setting.md  # World-view
│   ├── writing-style.md  # Writing Style
│   ├── genre-setting.md  # Genre Setting
│   └── character-setting/
│       └── <id>.md       # One file per character
├── volumes/
│   └── volume-{N}.md     # Volume Outlines
├── chapters/
│   └── vol-{N}-ch-{M}.md # ★ Chapter Outlines (status: outline → draft → archived)
├── prompts/
│   └── vol-{N}-ch-{M}-prompt.md  # Prompts
├── sandbox/
│   └── vol-{N}-ch-{M}/    # Story simulation records (optional)
├── archives/
│   ├── *.draft.md        # Drafts
│   └── *.md              # Final Manuscripts
├── .agent/
│   ├── status.md         # Progress Tracking
│   └── task/             # Inter-agent order files
├── .gemini/
│   ├── agents/           # Agent Definitions (for Antigravity)
│   ├── knowledge/        # Anti-AI rules, style preferences, permanent memory, format specs
│   └── memory/           # Dynamic writing memory
├── .opencode/
│   ├── agents/           # Agent Definitions (for OpenCode)
│   ├── knowledge/        # Anti-AI rules, style preferences, permanent memory, format specs
│   └── memory/           # Dynamic writing memory
└── .claude/
    ├── agents/           # Agent Definitions (for Claude Code)
    ├── knowledge/        # Anti-AI rules, style preferences, permanent memory, format specs
    └── memory/           # Dynamic writing memory
```

## Agent Collaboration Architecture & Strict Subagent Dispatch

```
novel-agent (Master Orchestrator / Main AGY Agent)
  ├─ Phase 1: Setup → Dispatches updater (define_subagent -> invoke_subagent)
  ├─ Phase 2: Volume Outline → Dispatches volume-planner (define_subagent -> invoke_subagent)
  ├─ Phase 3: Chapter Outline → Dispatches chapter-planner (define_subagent -> invoke_subagent)
  ├─ Phase 4: Scene Prompt → Dispatches prompt-crafter (define_subagent -> invoke_subagent)
  ├─ Phase 5: Sequential Writing → Dispatches writer (define_subagent -> invoke_subagent per chapter)
  ├─ Review (Optional) → Dispatches reader (define_subagent -> invoke_subagent)
  ├─ Archiving / Lore-keep → Dispatches updater (define_subagent -> invoke_subagent)
  └─ Phase 6: Global Trailer Prompt → Dispatches trailer-crafter (define_subagent -> invoke_subagent, mode: global)
```

Agent definitions are stored in `agents/` (or `.gemini/agents/` in Google Antigravity, `.opencode/agents/` in OpenCode, `.claude/agents/` in Claude Code).

### Google Antigravity (AGY) Two-Step Subagent Protocol

In Google Antigravity, custom subagents must be dynamically registered before invocation:

1. **Step 1: Subagent Definition (`define_subagent`)**
   Read system prompt from `agents/{agent_name}.md` (or `.gemini/agents/{agent_name}.md`). If the subagent is not yet registered in the active session, call:
   ```json
   {
     "name": "{agent_name}",
     "description": "{brief description of agent role}",
     "system_prompt": "{content of agents/{agent_name}.md}",
     "enable_write_tools": true,
     "enable_mcp_tools": false,
     "enable_subagent_tools": false
   }
   ```
2. **Step 2: Subagent Invocation (`invoke_subagent`)**
   Write the task order file `.agent/task/{type}-order.md`, then launch the subagent:
   ```json
   {
     "Subagents": [
       {
         "TypeName": "{agent_name}",
         "Role": "{Role description}",
         "Prompt": "Read order file .agent/task/{type}-order.md and execute the task.",
         "Model": "inherit"
       }
     ]
   }
   ```

**MANDATORY 6-PHASE WORKFLOW FOR ANTIGRAVITY (AGY):**
AGY must execute ALL 6 phases in strict order without skipping any phase:

- **PHASE 1: Project Setup**
  - Run `python .agents/skills/awesome-novel/tools/init.py "<project-folder>"`.
  - Call `define_subagent` for `updater` (using `agents/updater.md`).
  - Write `.agent/task/setting-update-order.md` and call `invoke_subagent` (`TypeName: "updater"`).
  - Creates `story.md` and setting files under `settings/world-setting.md` and `settings/character-setting/`.

- **PHASE 2: Volume Outline Generation (`volumes/`)**
  - Call `define_subagent` for `volume-planner` (using `agents/volume-planner.md`).
  - Write `.agent/task/volume-plan-order.md` and call `invoke_subagent` (`TypeName: "volume-planner"`).
  - Generates volume outlines in `volumes/vol-1.md`, `volumes/vol-2.md`, etc.

- **PHASE 3: Chapter Outline Generation (`chapters/`)**
  - Call `define_subagent` for `chapter-planner` (using `agents/chapter-planner.md`).
  - Write `.agent/task/chapter-plan-order.md` and call `invoke_subagent` (`TypeName: "chapter-planner"`).
  - Generates chapter outline files under `chapters/vol-1-ch-1.md` through `chapters/vol-1-ch-{N}.md`.

- **PHASE 4: Scene Prompt Generation (`prompts/`)**
  - Call `define_subagent` for `prompt-crafter` (using `agents/prompt-crafter.md`).
  - Write `.agent/task/prompt-craft-order.md` and call `invoke_subagent` (`TypeName: "prompt-crafter"`).
  - Generates prompt files under `prompts/vol-1-ch-1.md` through `prompts/vol-1-ch-{N}.md`.

- **PHASE 5: Sequential Chapter Manuscript Writing (`archives/`)**
  - Call `define_subagent` for `writer` (using `agents/writer.md`).
  - For each chapter N sequentially: Write `.agent/task/writing-order.md` (target: `vol-1-ch-N`), then call `invoke_subagent` (`TypeName: "writer"`).
  - Subagent for Chapter N reads Chapter N-1's manuscript (`archives/vol-1-ch-{N-1}.md`) for continuity.
  - Every chapter manuscript in `archives/` MUST reach 1,800 to 2,500 words.
  - Optional QA: Call `define_subagent` for `reader` (using `agents/reader.md`) and call `invoke_subagent` (`TypeName: "reader"`).

- **PHASE 6: Global Trailer Prompt Generation (`prompts/trailers/`)**
  - Upon completion of Phase 5 (all manuscripts written and archived), call `define_subagent` for `trailer-crafter` (using `agents/trailer-crafter.md`).
  - Write `.agent/task/trailer-order.md` specifying `mode: global` and call `invoke_subagent` (`TypeName: "trailer-crafter"`).
  - Generates global concept trailer JSON specification in `prompts/trailers/global-trailer-prompt.md` (and `.json`).

**Optional Tool:** Story Simulation Sandbox (`skills/roleplay-sandbox.md`) is an independent interactive tool outside the agent dispatch chain. Authors invoke it when stuck to generate simulation records (`sandbox/`) for reference during chapter outline writing.

## Tool Contract

| Tool | Purpose | Primary User |
|------|---------|--------------|
| **Bash / run_command** | Execute `init.py`; migration backup/copy commands; version checks | Skill entry (non-agent) |
| **Read / view_file** | Detect project files, read settings/status | All agents |
| **Write / write_to_file** | Write order files (`novel-agent`); write settings/memory/knowledge (sub-agents) | Per agent permissions |
| **Define Agent / define_subagent** | Register custom subagent types (`volume-planner`, `writer`, etc.) in AGY runtime | `novel-agent` / AGY Main Agent |
| **Agent / invoke_subagent** | `novel-agent` dispatches registered sub-agents in AGY | `novel-agent` exclusive |
| **Edit / replace_file_content** | Edit content files under `settings/`, `.claude/`, `.gemini/` | Sub-agents (non `novel-agent`) |
| **Glob / list_dir** | Scan files | All agents |
| **Grep / grep_search** | Search content | All agents |
