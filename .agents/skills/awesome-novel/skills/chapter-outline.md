# Generate Chapter Outline skill

Calibrated reference materials → An executable chapter outline. Refer to `.claude/knowledge/chapter-setting-style.md` for formatting standards.

## Process

Establish emotional anchor → Set conflict ladder → Build information gap → Break down scene cards → Prohibition list → Memo population → Hooks → Design change notification (Optional) → Output

## Refine Chapter Outline

Populate item by item according to the formatting standards of `chapter-setting-style.md`. The following is the execution sequence; refer to the standard for specific formats and writing methods. The first three steps (emotional anchor / conflict ladder / information gap) are consistent with the volume outline methodology, just shifting the granularity from "volume" to "chapter."

**a. Establish emotional anchor** — Split into two layers: **Read the position of this chapter in the volume's emotional arc from the volume outline** + **Discuss the internal emotional arc of this chapter with the author**. If it's the first chapter, the agent autonomously selects an opening hook technique based on the genre and writing style (refer to `.claude/knowledge/plot-craft/opening-hooks.md`), drafts an opening scene card, and shows it to the author for confirmation. Do not present a list of techniques for the author to choose from.

> Read from the volume's emotional arc: Where is this chapter at? — "The middle of the suppression phase" or "At the release phase of face-slapping"?

The emotional main tone of this chapter is determined by its position on the volume's arc. Write a suppressive tone in the suppression phase, and a satisfying (shuang) tone in the release phase.

After determining the position, ask the author about the emotional arc of this chapter (only ask about the plot, not techniques):

> "The volume's direction has reached the {suppression/release/turning} phase. How do you envision the reader's emotions flowing in this chapter? — For example, what emotion to start with, what event causes a turn in the middle, and what emotion to conclude with?"

After the author answers, the agent judges if it is specific enough:
- Enough (≥3 steps, has causal anchors, the chapter ending point supports the volume's next step) → Proceed to the next step
- Not enough (only "suppress first, then raise" two nodes, or the chapter ending contradicts the volume's direction) → Follow up for specific events or turning points

Technique selection (arc pattern, intensity distribution) is matched autonomously by the agent and is not shown to the author to pick. Once the emotional arc is determined, record it in mood_progression.

Part of the volume's direction → Intra-chapter micro-arc (Calm → Unease → Confirming dilemma → Suppression), the chapter ending point must support the next step in the volume's direction.

**b. Set conflict ladder** — Split into two layers: **Read this chapter's tier in the volume's conflict ladder from the volume outline** + **Discuss intra-chapter obstacle progression with the author**.

> Read from the volume's conflict ladder: What tier has this chapter reached? — "Tier 1: Entry" or "Tier 4: Ultimate"?

The obstacle intensity of this chapter is determined by the volume's tier. Write information blocking at the entry tier, and direct confrontation at the ultimate tier.

After determining the tier, ask the author about the obstacle design of this chapter (only ask about the plot, not techniques):

> "This chapter is at the {tier} phase. What obstacles do you envision the protagonist encountering in this chapter? — How many scenes, and where does each scene get stuck?"

After the author answers, the agent judges if it is specific enough:
- Enough (2-3 steps of obstacles, each step harder than the previous one, with turning points between tiers) → Proceed to the next step
- Not enough (only one obstacle mentioned, or no progression between obstacles) → Follow up: "What happens after the first obstacle? What did the protagonist do to change the situation?"

Technique selection (conflict escalation methods: environmental pressure/target displacement/chain reaction, etc.) is matched autonomously by the agent from plot-craft, and is not shown to the author to pick.

A certain tier of the volume → Intra-chapter small ladder (Probing → Encounter → Escalation), each step is harder than the last:

```
Volume conflict ladder: Tier 1 Entry → Tier 2 Escalation → Tier 3 Turning → Tier 4 Ultimate
                                      ↑
This chapter's position: Tier 2·Escalation

Intra-chapter small ladder:
  Scene 1: Probing — Protagonist tries to make contact, finds the other party guarded       ← Entry level
  Scene 2: Encounter — Protagonist gets warned/stopped, confirms resistance exists          ← Escalation
  Scene 3: Conflict Escalation — Protagonist insists on investigating, other party adds pressure ← Intra-chapter climax
  Ending: Protagonist is in a more dangerous situation → Supports the next step of the volume (Tier 3 Turning)
```

**Bottom line:** The intra-chapter ladder is not repeating the same thing — if all three scenes are "Protagonist looks up data → gets stopped → changes a way to look up data," this is flat, not a ladder.

**c. Build information gap** — The definition of information gap is at the volume level (start → end), the chapter level is where the information gap **dynamically changes**.

> Read from the volume's information gap: This chapter inherits the current position of the volume's information gap arc — has the volume reached the "villain has advantage" phase or the "protagonist counter-tracking" phase?

After determining the position, ask the author about the information gap design of this chapter (only ask about the plot, not techniques):

> "The volume's arc has reached the {phase}, and the characters appearing in this chapter are {list}. How do you preset the information flow in this chapter? — Who doesn't know what at the start, what is revealed in the middle, and what new questions are left at the end?"

After the author answers, the agent judges if it is specific enough:
- Enough (changes from start ↔ middle ↔ end, new information gap drives the next chapter) → Proceed to the next step
- Not enough (only the starting state without changes) → Follow up: "Who found out what in the middle? What suspense is left for the reader at the end?"

A section of the volume's arc → Intra-chapter information gap change (Set → Use → Reveal → New), each revelation creates a new information gap:

```
Volume info gap arc: Protagonist doesn't know ↦ Gradually revealed → Information gradually approaches protagonist
                                           ↑
This chapter's position: Protagonist begins to notice contradictions, but villain's advantage remains

Intra-chapter info gap change:
  Start: Protagonist doesn't know what scene clues mean (reader same as protagonist)
  Middle: Discovers contradictory details → Protagonist starts to feel something is wrong (info gap narrowing)
  End: Gets tracked → Protagonist knows someone is watching (New info gap: Villain knows ↦ Protagonist doesn't know who it is)
```

Clarify who knows what ↦ who doesn't know what among the appearing characters in this chapter, and simultaneously mark the change process:

```markdown
Info gap relationship (Start → End):
· Start: Protagonist doesn't know where Su Tang went ↦ Reader same as protagonist
· Middle: Protagonist discovers contradiction ↦ Info gap narrows (Protagonist caught up one step)
· End: Villain knows they are tracking ↦ Protagonist doesn't know opponent's identity ⬅ New info gap, drives next chapter
```

Information gap changes determine the driving force rhythm of this chapter (suspense/threat/goal/relationship/info gap driven).

**d. Determine POV strategy** — Confirm whose POV is used for the narrative in this chapter.

> "From whose POV is this chapter written? Default is Protagonist Limited POV. If you want to switch POVs — for example, writing the first two paragraphs from the villain's POV — how do you switch, and where do you switch back?"

Record this in the outline's POV field (`outline.pov`).

| Strategy | Description | Example |
|------|------|------|
| Single Protagonist Limited POV | Locked on one person throughout, only write what they perceive | `Lu Zheng (Single Protagonist Limited POV)` |
| Multi-segment Switching POV | Switch POV character by paragraph | `First 2 paragraphs Villain POV, then switch back to Lu Zheng` |

After POV is determined, proceed to break down scene cards (e). The three-segment anchors of the scene card are based on the perception of this POV character. If it is multi-segment switching, each segment is based on the corresponding POV character.

**e. Break down scene cards** — Break down key points using the three-segment anchor method from §Two (From Direction to Outline Points). Each key point corresponds to a scene card, and the underlying logic is consistent with the volume outline. If this chapter involves tragedy/abusive plots, refer to `.claude/knowledge/plot-craft/tragedy-techniques.md` to design emotional rhythm; for emotional pull, refer to `.claude/knowledge/plot-craft/emotional-pull.md`:

```
Three elements of scene card → Embodiment in key points
  What does the protagonist want to do → Action anchor (what the character specifically does)
  What is stopping them → Judgment anchor (what obstacle the character encounters/what contradiction is found)
  What suspense makes the reader want to read on → Full text emotional hook + information gap
```

Break down 8-12 `key_points`, write 2-3 sentences for each using the three-segment anchor method (sense + action + judgment). When presenting the complete plan, ask the author to confirm.

**f. Prohibition list** — Ask and record `prohibitions` according to §(What not to do — Hard constraint red lines).

**g. Chapter Memo population** — Populate paragraph by paragraph according to the 7 sections of §Three (Chapter Outline Content Checklist) (Emotional anchor / Info gap / Conflict ladder completed in a-c, populate remaining fields here):
1. `current_task`
2. `reader_expectation`
3. `payoff_plan`
4. `downtime_functions`
5. `key_choices`
6. `knowledge_state` (Character info state — info gap relationship completed in c, add list of what each character knows/doesn't know here)
7. `required_changes` (Chapter-end change — consistent with "what changed at the end" in b's conflict ladder)

Show complete memo for author's confirmation after filling.

**h. Emotional Design (Refinement)** — Populate emotional details according to §Four (Emotional Design): `mood_progression` (completed in a), `intensity_peak`, `intensity_level`, `emotional_hook`, `micro_payoffs`.

**i. Hooks Operation** — Write down hook operations for this chapter (plant / advance / resolve). Refer to the three hook design methods (cognitive dislocation / information gap / countdown) in `.claude/knowledge/plot-craft/hook-techniques.md`. Do not read or write the global `hooks.md` — the source of truth is the `hooks` field in each `chapter.md`.

**j. Design change notification (Optional)** — Setting change requirements discovered during planning/calibration. Used to notify the novel-agent to dispatch the updater. Append to the end of the chapter outline as needed:

```
## Setting Change Notification

- **Target:** settings/character-setting/{id}.md
- **Type:** State update / New character / Worldview update
- **Reason:** {Why this change is needed}
- **Details:** {Specific change description}
```

Only append when there is a change, otherwise omit.

## Output

Write to `chapters/vol-{N}-ch-{M}.md`, `status` → `outline`
