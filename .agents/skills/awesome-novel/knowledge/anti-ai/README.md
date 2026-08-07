# Anti-AI Writing Rules Repository

> Core reference for the anti-ai pipeline. Contains graded forbidden word lists, de-AI methodologies, and genre-specific good/bad examples.

## Directory

| File | Purpose |
|------|---------|
| `common-rules.md` | Graded forbidden words list, sentence templates, replacement strategies (shared across all genres) |
| `anti-ai-writing.md` | Complete guide to removing AI flavor (fingerprint identification/systematic modification/example library) |
| `urban.md` | Urban General |
| `urban-romance.md` | Urban Romance |
| `urban-daily.md` | Urban Slice-of-Life (Daily) |
| `urban-farming.md` | Urban Farming |
| `urban-brained.md` | Urban Brained/High Martial Arts/Cultivation |
| `war-god.md` | War God/Live-in Son-in-law |
| `xianxia.md` | Eastern Xianxia |
| `xuanhuan.md` | Xuanhuan (Eastern Fantasy) |
| `suspense-crime.md` | Suspense/Crime Investigation |
| `suspense-paranormal.md` | Suspense/Paranormal |
| `historical.md` | Historical Ancient |
| `ancient-politics.md` | Historical Politics/Ancient Court Intrigue |
| `western-fantasy.md` | Western Fantasy |
| `scifi-apocalypse.md` | Sci-Fi/Apocalyptic |
| `anti-japanese-war.md` | Anti-Japanese War/Espionage |
| `anime-derivative.md` | Anime/Game Derivative |
| `derivative.md` | Male-Oriented Derivative |
| `fanqie.md` | Fanqie Style |

## Usage Scenarios

```
When the anti-ai agent executes flavor removal:
    ↓
Read common-rules.md (graded forbidden list)
    ↓
Read anti-ai-writing.md (methodology guide)
    ↓
Read {genre}.md (genre good/bad examples)
    ↓
Execute replacements according to Phase 1-4 pipeline
```

### anti-ai Phase 3
Look up corresponding good/bad examples by genre for precise replacements.

### prompt-crafter
Inject genre rules as needed when generating prompts.

## Format

### common-rules.md — Common Rules

Shared across all genres. Contains:
- **Graded Forbidden Word List** — ★★★★★ Most toxic sentence structures / Level 1 / Level 2
- **Sentence Templates** — Metaphor/Structure/Punctuation rules
- **Replacement Strategy Quick Reference** — Externalizing emotions/Dialogue tags/Perspective control
- **Fatigue Word Thresholds** — Original threshold quick reference

### anti-ai-writing.md — Methodology Guide

Methodological basis for all Phases of anti-ai. Contains:
- **AI Writing Fingerprints** — High-frequency words/Chapter-end summaries/Stacked descriptions/Uniform distribution
- **Show Don't Tell** — Core formula + Five Senses Check
- **Detection of 7 AI Writing Patterns** — Signal/Features/Fix for each
- **Systematic De-AI Three-Pass Method** — De-generalization/De-formalization/Return to naturalness
- **Rewriting Example Library** — Emotion externalization/Scenes/Fighting/Endings/Rhythm

### {genre}.md — Genre Good/Bad Examples

Independent file for each genre. Contains:
- **High-Frequency AI Flawed Sentences: Good vs. Bad Examples** — ❌ AI flavor vs ✅ Human feel contrast
- **Writing Key Points** — Special considerations for that genre

## Formatting Standards

All genre files follow a unified format:

```markdown
# {Genre Name} Anti-AI Rules

> Applicable genres: {genre-id-1}, {genre-id-2}

## High-Frequency AI Flawed Sentences: Good vs. Bad Examples

### 1. {Problem Type}

❌ "AI-flavored writing"
✅ "Human-feeling writing"

### 2. {Problem Type}

...

## Writing Key Points

1. **{Point Title}** — Specific explanation
2. ...
```

## How to Contribute New Genres

### Method 1: Create a new genre file

1. Create `{genre-id}.md` under `knowledge/anti-ai/`
2. Copy the following template and fill it out:

```markdown
# {Genre Name} Anti-AI Rules

> Applicable genres: {genre-id}

## High-Frequency AI Flawed Sentences: Good vs. Bad Examples

### 1. {Problem Type}

❌ "AI-flavored writing"
✅ "Human-feeling writing"

### 2. {Problem Type}

❌ "..."
✅ "..."

## Writing Key Points

1. **{Key Point}** — Explanation
2. **{Key Point}** — Explanation
```

### Method 2: Expand an existing genre

When the writing key points of an existing file are insufficient:
1. Find the corresponding file
2. Add a new problem type under "High-Frequency AI Flawed Sentences: Good vs. Bad Examples"
3. Each problem type includes a pair of ❌/✅ contrast

## Contribution Guidelines

### Good/Bad Example Principles

1. **❌ Must be specific** — Give real AI-flavored sentences, not abstract descriptions.
2. **✅ Must be actionable** — Give human-feeling writing that can be replicated, not just "make it natural."
3. **Contrast must be obvious** — Same scene, same emotion, ❌→✅ conversion is clear.

### Writing Key Points Principles

1. **Specific, not abstract** — "Write hands trembling" instead of "Write nervousness."
2. **Actionable** — "Hands trembling = can't hold the cup" instead of "Must have details."
3. **Genre Characteristics** — The key points for each genre should reflect the uniqueness of that genre.

### Pre-submission Self-Check

- [ ] Is the ❌ in every example a real AI-flavored sentence?
- [ ] Can the ✅ in every example be directly copied into the text?
- [ ] Do the writing key points target specific problems of the genre?
- [ ] Has it avoided duplicated content already present in other files?

## Example

Suppose you want to add a new file for the "Game/Sports" genre:

```markdown
# Game/Sports Anti-AI Rules

> Applicable genres: game-sports

## High-Frequency AI Flawed Sentences: Good vs. Bad Examples

### 1. Winning the Match Description

❌ "He won the match, and the entire audience cheered joyfully."
✅ "The final whistle blew. He stood frozen in place; his teammates rushed over and picked him up. His legs were weak, and he couldn't stand steadily."

### 2. Parallel Structure Psychology

❌ "Perhaps the pressure was too great, perhaps the opponent was too strong, or perhaps he wasn't ready yet."
✅ "He took a deep breath. The referee was looking down at his watch. He rolled his ankle; the shoelaces were a bit loose."

## Writing Key Points

1. **Write the match through the body** — Panting, trembling, weak legs, thirst, not "nervousness."
2. **Write training through repetition** — What is practiced every day? Which mistake is this? Which bottle of water?
3. **Write victory through contrast** — What do they do after winning? Not cheering, but freezing, weak legs, looking for water.
```

---

*If you have any questions or suggestions, please submit an Issue or PR.*
