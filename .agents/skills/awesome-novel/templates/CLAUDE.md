# {project-name}

## AI Guidelines

The writing process for this project is collaboratively completed by 7 agents defined under `.claude/agents/`.

**Start Writing:** Enter `@novel-agent` to enter the writing loop.

**Writing Process:** Settings → Volume Outline → Chapter Outline → Prompts → Main Text → Review → Archiving → Next Chapter

**Project Structure:**
- `story.md` — Project index + Main storyline outline
- `settings/` — World-building, characters, writing style, timeline
- `volumes/` — Volume outlines
- `chapters/` — Chapter outlines
- `prompts/` — Prompts
- `archives/` — Main text
- `.agent/` — Status tracking + agent communication
- `.claude/memory/` — Dynamic writing memory (author feedback from each phase, continuously accumulated)
- `.claude/knowledge/` — Anti-AI rules, style preferences, permanent memory, genre reference materials
