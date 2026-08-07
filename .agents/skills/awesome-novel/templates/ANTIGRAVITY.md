# {project-name}

## Google Antigravity (AGY) Guidelines

The novel writing process for this project is collaboratively completed by 7 agents defined under `.gemini/agents/` (and `.claude/agents/` / `.opencode/agents/`).

**Start Writing:** Call the novel-agent using `@novel-agent` or invoke it directly to enter the writing loop. `novel-agent` orchestrates chapter creation by registering sub-agents via `define_subagent` and spawning them via `invoke_subagent`.

**Writing Process:** Settings → Volume Outline → Chapter Outline → Prompts → Main Text → Anti-AI Polishing → Review → Archiving → Next Chapter

**Project Structure:**
- `story.md` — Project index + Main storyline outline
- `settings/` — World-building, characters, writing style, timeline
- `volumes/` — Volume outlines
- `chapters/` — Chapter outlines
- `prompts/` — Prompts
- `archives/` — Main text
- `.agent/` — Status tracking + inter-agent communication (`.agent/task/*-order.md`)
- `.gemini/agents/` — Agent definitions for Antigravity (`novel-agent`, `volume-planner`, `chapter-planner`, `writer`, etc.)
- `.claude/memory/` — Dynamic writing memory (author feedback from each phase, continuously accumulated)
- `.claude/knowledge/` — Anti-AI rules, style preferences, permanent memory, genre reference materials
