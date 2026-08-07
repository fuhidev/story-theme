# Volume Direction Determination skill

## Process

Initial: Display story structure template → Author confirms skeleton
Volume N+1: Character voices → 4-step check → Determine volume direction

## 1. Volume N+1: Let the Characters Speak

If there are previous volume archives (`archives/` is not empty) → **Start directly with character-driven analysis**, do not push templates.

1. Read unresolved hooks from the previous volume and the protagonist's current state
2. Read the latest status history of all active characters
3. For each active character (including antagonist/major supporting characters), generate character "voicing":

```text
[{Character Name}'s Situation] End of volume position — who they are with, what information they possess, what situation they are in currently
[{Character Name}'s Unfinished Business] Obsession/goal/fear left over from the previous volume — what hasn't been resolved
[{Character Name}'s Thoughts Between Volumes] What they are repeatedly thinking about in the gap between volumes
[{Character Name}'s Current Desires] What they are most likely to push for in the next volume based on their current situation
```

4. Display to the author. The conflicting wills of several characters will naturally form the main plot.
5. **From voicing to chapter expansion (4-step check):**
   - Check if the main plot surfaces — do the "current desires" of all characters point to the same point of contradiction?
   - Deduce chapter count — how many chapters does each character's "current desire" roughly take to complete? Add them up for volume length
   - Check chapter outline direction — can you break down the advancement directions of each phase from the main plot?
   - Template optional — if the main plot is clear, the template acts as an auxiliary skeleton
6. The author determines the volume direction based on character voices + the results of the 4-step check.

**Fallback:** If the author still has no direction after character voices → Return to templates for structural assistance.

## 2. Volume 1: Display Story Structure Template

Initial volume outline planning (`archives/` is empty) → Use structural templates to provide a skeleton.

The Agent directly outputs proposals (do not ask "which one do you want"), inferring the most suitable template based on the genre:

| Template | Suitable for | Default Volumes |
|------|------|---------|
| **Three-Act Structure** (setup→confrontation→resolution) | Conflict-driven, has clear opponent or goal | setup 3-4 ch / confrontation 4-5 ch / resolution 2-3 ch |
| **Kishōtenketsu** (Introduction→Development→Twist→Conclusion) | Eastern narrative structure, slow burn, delicate | Intro 2-3 ch / Dev 3-4 ch / Twist 2-3 ch / Conc 1-2 ch |
| **Suspense Escalation** (Question→Clue→Reversal→Answer→Aftertaste) | Puzzle-solving oriented, has information gaps | Question 1-2 ch / Clue 2-3 ch / Reversal 2 ch / Answer 1 ch / Aftertaste 1 ch |
| **Character Arc** (Routine→Turning Point→Struggle→Transformation→New Normal) | Character-driven, growth oriented | Routine 2 ch / Turn 2 ch / Struggle 2-3 ch / Trans 1-2 ch / New Normal 1 ch |
