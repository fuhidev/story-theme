# Chapter Setting Style Guide

> Guide for designing chapter-level outlines. Covers plot beats, emotional design, and conflict mapping.

## Output Format

The chapter outline is saved to `outlines/vol-{N}-ch-{M}.md`.

### Metadata

```markdown
Volume Number: {N}
Chapter Number: {M}
Title: {Chapter Title}
```

### Outline Beats

```markdown
── Outline ──

Summary: {One sentence summary of the chapter's core event}

Key Points:
1. [Tag] {Action sequence 1}. {Focus detail}
2. [Tag] {Action sequence 2}. {Focus detail}
...

Characters Appearing: {List of characters}
Scene Locations: {Main locations}
Time: {Time of day or relative time}
```

- **[Tag]**: Categorize the beat (e.g., [Advance Plot], [Build Suspense], [Dialogue]).
- **Focus detail**: What sensory or emotional detail the AI should focus on when writing this beat.
- Write beats as actionable sequences connected by `→`, not as narrative prose.

### Chapter Content Status

```markdown
── Chapter Content ──

Current Task: {What this chapter aims to accomplish in the larger volume}

Reader Expectation:
- Emotional State: {What the reader should feel}
- Strategy: {How to achieve it}
- Specifics: {Details of the strategy}

Fulfillment Plan:
- Must Resolve: {Any hanging threads from previous chapters to close}
- Must Suppress: {Information to keep hidden for now}
- Advance Without Closing: {New hooks or plot lines to introduce}

Transition Function:
- {How this chapter connects the previous to the next}

Key Choices:
- {Why the character makes a critical choice in this chapter}

Character Information State:
- {Character A}: {What they know} / {What they don't know}
- {Character B}: ...
- Information Gap: {A knows X, B doesn't know X}

Chapter End Changes:
- Information Change: {What new info is revealed}
- Threat Introduced: {New problems at the end}

Hard Constraints:
- {Rule 1 for the AI when writing}
- {Rule 2}
```

### Conflict and Emotion

```markdown
── Conflict Ladder ──

Volume Ladder Position: {e.g., Level 1 - Entry}
  → {Explanation of the chapter's conflict scale}

Small steps within the chapter:
  {Step 1} → {Step 2} → {Step 3}
  → {Chapter end point status}

── Emotional Design ──

Volume Direction Position: {Tone and atmosphere}
Micro-arc in the chapter: {Emotional progression, e.g., Calm → Alert → Suspense}
Intensity Peak: {Which beat is the peak}
Strength Level: {1-10}
Emotional Hook: {What keeps the reader engaged}

Readers Get:
- Type: {e.g., Information, Payoff}
- Position: {Where in the chapter it happens}
Word Count Goal: {e.g., 3000 words}
```

## Few-Shot Example Principles

- **Action Chains**: Use `→` to show the flow of action. (e.g., Opens door → Sees empty room → Heart sinks).
- **Sensory Anchors**: Don't write the description, just point to it. (e.g., "Sensory emphasis: smell of rust").
- **No Mind Reading**: Describe character emotions through visible actions, not internal monologues unless necessary.