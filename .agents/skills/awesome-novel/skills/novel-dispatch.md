# novel-agent Dispatch SOP

## Responsibility Boundaries

The novel-agent **only does three things**:
1. Reads status.md to detect the current progress
2. Writes order files and dispatches sub-agents
3. Validates sub-agent outputs (checks if the order file has been cleaned up)

**Anything outside of this is not your job.** Do not write content, do not execute commands, do not change settings.

## Platform Dispatch Mechanisms

The `novel-agent` dispatches sub-agents based on the active runtime environment:

- **Google Antigravity (AGY):**
  1. Check if the target subagent (`volume-planner`, `chapter-planner`, `prompt-crafter`, `writer`, `reader`, `updater`, `trailer-crafter`) is defined in the current session.
  2. If not defined, read `agents/{type}.md` (or `.gemini/agents/{type}.md`) and call `define_subagent` (with `name`, `description`, `system_prompt`, `enable_write_tools: true`).
  3. Write order file to `.agent/task/{type}-order.md`.
  4. Call `invoke_subagent` with `TypeName: "{type}"`, `Role: "{Role}"`, and `Prompt: "..."`.
- **OpenCode:**
  Write order file to `.agent/task/{type}-order.md` -> Call subagent via OpenCode Task tool.
- **Claude Code:**
  Write order file to `.agent/task/{type}-order.md` -> Call subagent via Claude Agent tool.

## Dispatch Schedule by Phase

| Phase | Who Should Do It | Order File | Target Subagent |
|-------|------------------|------------|-----------------|
| setup | updater | `setting-update-order.md` | `@updater` |
| outline | volume-planner | `volume-plan-order.md` | `@volume-planner` |
| outline | chapter-planner | `chapter-plan-order.md` | `@chapter-planner` |
| draft | prompt-crafter | `prompt-craft-order.md` | `@prompt-crafter` |
| draft | writer | `writing-order.md` | `@writer` |
| anti-ai | anti-ai | `anti-ai-order.md` | `@anti-ai` |
| review | reader | `reader-review-order.md` | `@reader` |
| archive | updater | `archive-order.md` | `@updater` |
| trailer | trailer-crafter | `trailer-order.md` | `@trailer-crafter` |

## Rules for Writing Order Files

1. Order file path: `.agent/task/{type}-order.md`
2. Order files only contain: Input information/file paths + Output target paths. Do not include execution steps, rules, or methodologies.
3. The sub-agent's SKILL.md defines the execution SOP; the order does not cover specific steps.
4. In AGY, ensure the subagent is defined via `define_subagent`, write the order file, then invoke the sub-agent via `invoke_subagent` (Antigravity), Agent tool (Claude Code), or Task tool (OpenCode).
5. **MANDATORY 6-PHASE SEQUENCE FOR AGY:** In Google Antigravity, the `novel-agent` MUST execute all 6 phases sequentially per `.agent/status.md`: Phase 1 (`settings/`) ➔ Phase 2 (`volumes/`) ➔ Phase 3 (`chapters/`) ➔ Phase 4 (`prompts/`) ➔ Phase 5 (`archives/`) ➔ Phase 6 (`prompts/trailers/`). Jumping straight to `archives/` without creating volume, chapter, and prompt outline files is strictly prohibited.
6. **STRICT SINGLE-CHAPTER SEQUENTIAL RULE:** NEVER cram multiple chapters/episodes into a single order file or sub-agent invocation. Each chapter/episode MUST be dispatched via its own dedicated sub-agent invocation sequentially (e.g. `target: vol-1-ch-1` only). The subagent for Chapter N must read Chapter N-1's completed manuscript (`archives/vol-1-ch-{N-1}.md`) before writing to guarantee 100% narrative continuity.

## Completion Check Standards

- The order file no longer exists (cleaned up by the sub-agent)
- The corresponding output file exists and is not empty
- If it still fails after 2 retries, ask the author if manual intervention is needed

## Prohibited Actions

- ❌ Do not use Bash
- ❌ Do not write files other than the order file
- ❌ Do not write directly to settings/, chapters/, volumes/, prompts/, archives/, .claude/, .gemini/
- ❌ Do not call multiple sub-agents in one loop
- ❌ Do not do what the sub-agent should do (just write the order, invoke the sub-agent, and wait for results)
- ❌ Do not skip phases or generate chapter manuscripts without creating volume, chapter outline, and prompt files first.

