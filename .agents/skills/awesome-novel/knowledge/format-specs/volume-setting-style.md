# Volume Setup Guide

> Provide a format standard for the breakdown of the volume level outline. Discuss the volume direction first (Step 1), and then break down the chapters one by one (Step 2). After completion, output the complete document to `volumes/vol-{N}.md`.
> **Only one volume is prepared at a time.** Only when returning from Phase 4 to prepare the next volume, can you write a new file.

---

## Output Standard

A complete volume outline document must strictly follow the following format.

### File Header and Main Target

```markdown
# Volume {N}: {Volume Title}

> {A sentence summarizing the tone and core events of this volume}

## 1. Core Direction of the Volume

- **Volume Position**: {e.g., Level 1 · Entry Level - the first stage of the depression stage}
- **Tone & Atmosphere**: {e.g., Establishing predicament, creating suspense. Ambience: Curiosity/Uneasiness}
- **Core Conflict**: {e.g., The protagonist investigates a missing person case but discovers a three-year-old suppressed case, and is obstructed by unknown forces}
- **Volume End State**: {The final state of the protagonist at the end of this volume}
```

### Main Clue/Plot Thread Breakdown (Only discuss if there are more than 2)

```markdown
## 2. Main Clues

| Clue Name | Core Event | Final Unveiling |
|-----------|------------|-----------------|
| {Main Clue 1} | {What is the core event driving this clue} | {What is the final answer/result at the end of the volume} |
| {Side Clue 1} | {Secondary event or relationship development} | {Result at the end of the volume} |
```

> **Note**: If the genre is simple and there is only a single main line (such as a single case, a single tournament), this section can be omitted directly.

### Chapter Breakdown List (Core)

```markdown
## 3. Chapter Breakdown

> Format description:
> - **[Action]**: What the protagonist is doing
> - **(Emotion/Information)**: What the reader feels / what information is revealed
> - **→ Result**: The change leading to the next chapter

1. **Chapter {N}: {Tentative Title}**
   - **Scenario**: {One sentence describing the starting scene}
   - **[Action]**: {Protagonist's action/choice}
   - **(Emotion/Information)**: {Emotional experience or new information for the reader}
   - **→ Result**: {The hook or state change ending the chapter}

2. **Chapter {N+1}: {Tentative Title}**
   - **Scenario**: {One sentence describing the starting scene}
   - ...
```

---

## Discussion Process Guidelines

### Step 1: Discuss Volume Direction

First, read the `Core Conflict` of this volume from `story.md`. Then ask the author:

1. "What is the tone and atmosphere of this volume? Is it depressing, exhilarating, mysterious, or lighthearted?"
2. "At the end of this volume, what state should the protagonist be in? (e.g., discovering the truth, getting a new weapon, establishing a new enemy)"

Use the answers to fill in the **1. Core Direction of the Volume**.

### Step 2: Break Down the Chapters

Don't let the author write the whole thing at once. Break it down logically.
- If it's 10 chapters, divide it into 3 acts (Beginning 1-3, Middle 4-7, End 8-10).
- Ask act by act: "For the first three chapters, the protagonist needs to enter the situation. How do they discover the problem? What is their first action?"
- After the author answers, condense it into the Chapter Breakdown format and confirm.
- Then ask the next act.

### Acceptance Standard (Check before output)

- **Continuity**: Does Chapter N's result naturally lead to Chapter N+1's scenario?
- **Pacing**: Are there too many chapters of just talking/preparing without action? (If so, suggest merging).
- **Goal Alignment**: Does the final chapter's result match the `Volume End State` set in Step 1?

If all pass, write to `volumes/vol-{N}.md`.