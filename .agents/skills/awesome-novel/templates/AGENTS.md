# {project-name}

## OpenCode Guidelines

The novel writing process for this project is collaboratively completed by 7 agents defined under `.opencode/agents/`.

**Start Writing:** Call the novel-agent using `@novel-agent` or the Task tool in OpenCode to enter the writing loop.

**Writing Process:** Settings → Volume Outline → Chapter Outline → Prompts → Main Text → Anti-AI Polishing → Review → Archiving → Next Chapter

**Project Structure:**
- `story.md` — Project index + Main storyline outline
- `settings/` — World-building, characters, writing style, timeline
- `volumes/` — Volume outlines
- `chapters/` — Chapter outlines
- `prompts/` — Prompts
- `archives/` — Main text
- `.agent/` — Status tracking + agent communication (order files)
- `.opencode/agents/` — Agent definitions (novel-agent, volume-planner, chapter-planner, etc.)
- `.claude/memory/` — Dynamic writing memory (author feedback from each phase, continuously accumulated)
- `.claude/knowledge/` — Anti-AI rules, style preferences, permanent memory, genre reference materials
