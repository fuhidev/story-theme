# Volume Outline Discussion skill

> One volume has only one main plot; all chapters combined tell this main plot completely. Missing any single chapter makes the story incomplete.

## Process

Establish emotional direction → Determine core conflict → Set conflict ladder → Build info gap → Break down scene cards → Append new character/setting → Output and write → Acceptance

## 1. Determine Core Conflict

Inherit the volume's core conflict from story.md, ask the author, "Do you want to adjust anything?"

**Bottom Line (Failure to pass any prevents proceeding to chapter breakdown):**
1. **Core conflict can be stated in one sentence** — "Who + wants to do what + obstructed by what," no "meanwhile," "on the other hand" needed
2. **Opposing force is specific** — The obstacle must be a specific character/faction/situation, not vague words like "against fate," "against society"
3. **Both conflicting parties have non-negotiable reasons** — Deduce from Layer 3 Values of the character settings; if unable to deduce, ask the author to supplement.

> Ask the author about the non-negotiable reasons for both conflicting parties (only ask about plot):
> "The core of the conflict between the protagonist and {opponent} is {X}. What can neither party afford to lose? — What happens if {Character A} backs down? What happens if {Character B} backs down?"
>
> After the author answers, check against `.claude/knowledge/plot-craft/conflict-motivation.md` to judge if it's specific enough:
> - Both parties have concrete motives (survival threat/identity/obsession trauma, etc.) and are mutually exclusive → Pass
> - Either party has an invalid motive (inherently evil/plot demands/abstract concept) → Follow up: "If {Character} backs down, what exactly will they lose?"
> - Motives can be reconciled (a plan Z exists for both to step back) → Remind: "This conflict is currently reconcilable, should it be reinforced? Refer to reinforcement methods in conflict-motivation.md"
>
> If the character setting's Layer 3 Values are too abstract causing the author to be unable to answer, append a setting change notification reminding them to supplement.

## 2. Establish Emotional Direction

> Conflict defines "what happens," emotional direction defines "how the reader's emotions change from beginning to end of this volume."

After inheriting the core conflict from story.md, the agent should first autonomously design the emotional direction before rushing to break down chapters. Do not ask the author first.

### Method for Determining Emotional Direction

**Step 1: Determine Arc Pattern**

Based on the core conflict type and genre, the agent autonomously selects the most suitable emotional arc pattern. Selection basis:

| Conflict Type | Recommended Arc Pattern | Description |
|---------|-------------|------|
| Revenge/Counterattack | Suppress then satisfy | Suppress → Suppress → Elevate → Face-slap → Flex |
| Investigation/Puzzle | Layered approach | Tense → Approach → Shock → Soothe |
| Growth/Trial | Tension opening-closing | Tense → Soothe → Tense↑ → Soothe → Climax |
| Confrontation/War | Build-up to outburst | Suppressive build-up → Suppressive build-up → Outburst → Lingering |

If unsure, default to "Layered approach" (most universal for investigation-type narratives).

**Step 2: Allocate to Layers of the Conflict Ladder**

Each step of the arc corresponds to a layer in the conflict ladder:

```
Conflict Ladder                    Emotional Direction
Tier 1: Dilemma establishment  ←→   Suppression (Early volume oppression)
Tier 2: Oppression escalation  ←→   Suppression↑ (More uncomfortable)
Tier 3: Desperation/Turning    ←→   More suppressive/Shock (Lowest point)
Tier 4: Counterattack/Climax   ←→   Elevate→Face-slap→Flex (Release)
```

**Step 3: Verify Arc is Perceptible**

Agent autonomously checks: If the suppression tier is written as the protagonist having tea daily → Conflict design is flawed, go back and revise.
No need to ask the author — wait until the plan is displayed for the author to judge all at once.

### Bottom Line

- **Emotional direction must be an arc, not a point** — "Satisfaction (Shuang)" is not a direction, "Suppress → Suppress → Elevate → Face-slap → Flex" is
- **Each step of the arc corresponds to a layer of the conflict ladder** — Emotions cannot be designed independently of conflict
- **The arc must have progression** — "Suppress → Suppress → Suppress" is only quantitative change without qualitative change; a turning point is needed in the middle to shift emotion from suppression to expectation or anger

> **Conflict Escalation Reference:** When designing obstacles for each tier of the conflict ladder, refer to the conflict escalation methods (value dislocation/environmental pressure/target displacement/chain reaction) and plot reversal methods (logical misdirection/character displacement/multi-layered nested dolls) in `.claude/knowledge/plot-craft/index.md`, so the conflict type of each obstacle changes, rather than just "a stronger person arrives."

## 3. Set Conflict Ladder

Core conflict is "who fights whom"; the conflict ladder is "how to fight step by step."

### Ladder Design Method

Break one core conflict into 2-4 progressively harder tiers of obstacles:

```
Core conflict "Lu Zheng investigates old case — Someone inside police force blocks it"
  ↓
Tier 1: Info blocked — Files incomplete, Fang Yan afraid to speak      ← Entry
  ↓ Turn: Discovers old case was suppressed
Tier 2: External pressure — Followed/Warned                          ← Escalation
  ↓ Turn: Umbrella (protector) reveals true face
Tier 3: Internal loss — Witness missing, chain of evidence broken    ← Major Setback
  ↓ Turn: Protagonist forced to choose
Tier 4: Direct confrontation — Duel with mastermind                  ← Ultimate
```

Show to the author and confirm if the "difficulty progression" of each tier is reasonable.

### Mark Key Turning Points

Mark 2-4 turning points in the ladder where the "situation fundamentally changes":

| Turning Point Type | Example | Mark |
|---------|------|------|
| Info Turn | "Discovers old case was suppressed" | The goal changes afterwards |
| Relationship Turn | "Collaborator is actually enemy" | Faction changes afterwards |
| Status Turn | "Loser police support" | Methods change afterwards |
| Event Turn | "Witness killed" | Situation becomes irreversible afterwards |

**Bottom Line:** Every tier of obstacle is harder than the previous one — if Tier 2 is similar in difficulty to Tier 1, merge or delete.

## 4. Build Information Gap

Conflict drives the plot forward; information gaps drive the reader forward. Information gaps are the fundamental source of "why the reader wants to turn the page."

### Information Gap Establishment Method

**Step 1: Identify Who Holds Information**

Confirm category by category with the author — the key is "who knows what, who doesn't know what":

| Relationship | Ask Author | Example |
|------|--------|------|
| **Protagonist knows ↦ Others don't** | "In this volume, what key info does the protagonist know that villains/supporting characters don't?" | Protagonist has a cheat, protagonist learned of the conspiracy early |
| **Villain/Supporting knows ↦ Protagonist doesn't** | "What secret/trap do villains or supporting characters have that the protagonist doesn't know?" | Villain set an ambush, supporting character has a double identity |
| **Reader knows ↦ Protagonist doesn't** | "What does the reader know through the narrative that the protagonist is still kept in the dark about?" | Reader knows it's a misunderstanding but protagonist is angry, reader knows who the traitor is |

**Step 2: Set Volume-level Start → End**

The volume doesn't define info gap details for every chapter — it only defines **the info gap at the start of this volume** and **the info gap at the end of this volume**. The changes in between are deduced by the chapter outline.

> "At the beginning of this volume, what does the protagonist know, what does the villain know? At the end of this volume, what new things did the protagonist learn?"

```
Start of volume:
  Protagonist knows: Su Tang missing, police ruled it voluntary departure
  Villain knows: Inside story of suppressed case 3 years ago, where Su Tang is
  Reader knows: Same as protagonist
  
End of volume:
  Protagonist newly knows: Old case suppressed, umbrella exists
  Villain newly knows: Protagonist investigating, very troublesome
  Reader knows: Same as protagonist
  
Arc direction: Info gap changes from "completely asymmetrical" to "partially balanced",
          Protagonist caught up a step but not enough → Supports next volume
```

**Step 3: Determine Dominant Driving Force**

Decide what to use to drive the reader in this volume based on the start → end arc direction:

| Arc Direction | → Driving Force | Chapter Rhythm |
|---------|---------|---------|
| Info gradually revealed to protagonist | Suspense driven | Leave hooks every chapter, release one layer of new info |
| Villain's trap gradually surfaces | Threat driven | Safe period gets shorter and shorter |
| Protagonist's advantage gradually shown | Goal driven | Phased result → new obstacle, cyclical advance |
| Reader knows but character doesn't | Relationship / Info gap driven | Accumulate tension until revelation |

**Step 4: Mark to Each Chapter**

When breaking down chapters, annotate the info gap for each chapter — each chapter is a dynamic change point on the info gap arc:

```
### 1-4: Trap
- **Conflict Event**: Lu Zheng goes to warehouse, intel is fake — opponent set ambush
- **Emotional Anchor**: Tense
- **Info Gap**: Villain knows it's a trap ↦ Protagonist doesn't ⬅ This chapter's info gap change: From "Protagonist in the open" to "Protagonist steps into trap"

### 1-5: Counter-kill
- **Conflict Event**: Lu Zheng plays along, reverse-baits the person behind the informant
- **Emotional Anchor**: Satisfaction (Shuang)
- **Info Gap**: Protagonist has backup plan ↦ Villain doesn't ✅ Info gap reversed: From "Passive" to "Active"
```

### Bottom Line

- **Volume-level sets start → end, doesn't dictate intermediate details** — info gap changes per chapter are left to the chapter outline to deduce
- **End info gap cannot equal start** — if the protagonist knows the same at the end of the volume as at the start, the volume was written for nothing
- **End info gap cannot be completely balanced** — if the protagonist "knows everything" at the end of the volume, the next volume lacks information driving force

## 5. Break Down Scene Cards

> Volume Outline → Chapter (Emotional anchor + Conflict event + Info gap + Suspense hook)
> Chapter Outline → Scene Card (What protagonist wants to do + What stops them + What suspense keeps reader going)

Using the various stages of the conflict ladder confirmed in the previous step as a skeleton, populate chapter by chapter. For the "What suspense makes the reader want to read on" of each chapter, refer to the three hook techniques in `.claude/knowledge/plot-craft/hook-techniques.md`.

> **First Volume First Chapter Special Handling:** If it's the first volume's first chapter, the agent autonomously selects the most appropriate opening method based on genre and style (refer to `.claude/knowledge/plot-craft/opening-hooks.md`), drafts an opening scene card, and shows it to the author for confirmation. Do not present a list of methods for the author to choose from.

Each chapter contains three dimensions:

| Field | Standard | Unqualified Example |
|------|------|-----------|
| id | "Volume-Chapter" (e.g., 1-1), continuous without skipping | 1-1, 1-3 (Skipped 1-2) |
| title | Has info value, can guess core event of chapter | "Chapter 1", "New Beginning", "Turning Point" |
| Conflict Event | **Three elements: Who does what + conflict event + what changed at the end** | "Protagonist continues investigating" (Nothing changed) |
| Emotional Anchor | Core mood of reader for this chapter (corresponds to current phase of volume-level emotional arc) | None |
| Info Gap | {Who knows what} ↦ {Who doesn't know what} | None |

### Scene Card Refinement (Used in Chapter Outline Phase)

One chapter consists of 2-5 scene cards, each with three pieces of information:

| Field | Meaning | Note |
|------|------|------|
| **What protagonist wants to do** | Goal — what the protagonist wants to achieve in this scene | Even passive scenes have goals ("survive", "figure it out") |
| **What is stopping them** | Obstacle — what the obstruction is | Person vs Person, Person vs Event, Inner vs Inner |
| **What suspense makes reader read on** | Hook — why the reader cares about this segment | Derived from emotional anchor + info gap |

**Example:**
```
### Scene 1: Fang Yan's Office
- **What protagonist wants to do**: Lu Zheng wants inside info on the suppressed case from 3 years ago from Fang Yan
- **What is stopping them**: Fang Yan is afraid to speak — there's someone else in the office, implying "someone is watching"
- **What suspense makes reader read on**: Reader is curious what Fang Yan knows and why he's afraid to speak
```

### 4 Types of Unqualified Chapter Outlines

| Type | Typical Sentence | Correction Direction |
|------|---------|---------|
| **Theme-based** | "This chapter is about trust" (No event) | Turn concept into specific event |
| **Function-based** | "Establish character relationships" (Function ≠ Advancement) | Fold function into conflict event |
| **Synopsis-based** | "Protagonist experiences a day of adventure" (No conflict point) | Identify the single most core event |
| **Result-based** | "Protagonist wins match" (Only result, no process) | Write out the obstacles and confrontation process |

**There must be a causal chain between chapters:** The end-of-chapter change of the previous chapter is the starting point or motive of the next chapter.

## 6. Append New Character and Setting

New characters/settings added during volume outline discussions should be appended as a setting change notification block at the end of volume-{N}.md, scheduled for the updater to execute by the novel-agent before the writing loop:

```
## Setting Change Notification

- **Target:** settings/character-setting/{id}.md
- **Type:** New character
- **Details:** {Basic character settings}


- **Target:** settings/world-setting.md
- **Type:** Worldview update
- **Details:** {Change description}
```

Consistency Check → New settings must not contradict archived chapters.

## 7. Output and Write

Write to `volumes/volume-{N}.md` according to format:

```markdown
# Volume N: {Title}

## Volume Information

- **Emotional Direction**: Reader's emotional change arc in this volume (Suppress→Suppress→Elevate→Face-slap→Flex)
- **Core Conflict**: Who + does what + obstructed by what
- **Dominant Driving Force**: Suspense / Threat / Goal / Relationship / Info gap driven
- **Conflict Ladder**: Tier 1 (Entry) → Tier 2 (Escalation) → Tier 3 (Turn) → Tier 4 (Ultimate)
- **Estimated Chapters**: N

## Chapter List

### N-1: {Title with info value}

- **Conflict Event**: Who does what + conflict event + what changed at the end
- **Emotional Anchor**: Reader's mood for this chapter
- **Info Gap**: {Who knows what} ↦ {Who doesn't know what}
```

## 8. Acceptance

**Three-Dimensional Acceptance:**

**Dimension 1: Emotional Direction Acceptance**
- [ ] **Emotional direction established** — is an arc (Suppress→Elevate→Face-slap), not a single emotion word
- [ ] **Emotional direction has progression** — arc has start, turn, end, with emotion changing at each step
- [ ] **Emotional anchor marked for each chapter** — matches current phase of emotional direction

**Dimension 2: Conflict Ladder Acceptance**
- [ ] **Both conflicting parties have non-negotiable reasons** — Derivable from character setting Layer 3 Values, motives are mutually exclusive. If unable to deduce, settings are flawed or conflict architecture is weak
- [ ] **Each layer is harder than the last** — Not repeating the same kind of confrontation
- [ ] **Clear turning points between layers** — Situation fundamentally changes
- [ ] **2-4 turning points** — Too few is flat, too many leaves no room to breathe
- [ ] **Info gap marked for each chapter** — Clearly states "Who knows what ↦ Who doesn't know what"
- [ ] **Info gap has complete "Set→Use→Reveal" cycle within volume**

**Dimension 3: Chapter Structure Acceptance (Original Volume-level 4 Validations)**
1. **Chapters combined = whole volume told?** — Read all summaries chapter by chapter, do they combine into a complete story?
2. **Every chapter traceable to volume conflict?** — Pointing to the core conflict, ask for each chapter "Which step of the conflict does this chapter advance?"
3. **Does volume story advance after each chapter?** — Does the chapter-end change affect what follows?
4. **Title matches story?** — Can you guess what happens in the chapter just by reading the title?

**15-Second Quick Sniff (Passing all three ≈ Qualified):**
1. Can you state the core conflict in one sentence without looking at the files?
2. Point randomly to a chapter, can you say "What changed after this chapter ended?"
3. Point randomly to two chapters, can you say "Why does the second chapter come after the first, instead of jumping straight to the third?"

**Volume N+1 Addition:** Verify whether the character states from the character voicing (refer to volume-direction.md) are consistent with the archived chapters—if not, it indicates omissions during status update at archiving, needs fixing.

## 9. AI Flavor Self-check and Removal

Scan the full text of the volume outline, if any rule is hit → check again after modification; it only passes when confirmed that all are cleared.

**Pattern 1: Hollow Conflict Descriptions**
Core conflict or chapter summary uses adjectives to express attitude but writes no specific events.
- ❌ "Launch a wonderful duel" → ✅ "Lu Zheng confronts the man in the gray short sleeves at the abandoned factory, opponent pulls out old case files to threaten"
- ❌ "Conflict escalates fiercely" → ✅ "Fang Yan is suspended by superiors, Lu Zheng loses police internal info source"
- Quick lookup: Search full text for "wonderful", "fierce", "thrilling" — If yes → Hit

**Pattern 2: "Universal Advancing Sentence Patterns"**
Universal sentences that don't advance specific plot, only fill space.
- ❌ "As the investigation deepens, the truth gradually surfaces"
- ❌ "In this process, the protagonist encountered new challenges"
- ❌ "The story begins to develop in an unpredictable direction"
- Quick lookup: Search full text for "As...", "In this process", "Meanwhile" — If yes → Hit

**Pattern 3: Trailer-style Language**
Sketches atmosphere like a movie trailer but gives no events.
- ❌ "A greater conspiracy is brewing" → Who is brewing it? Brewing what? Is there action?
- ❌ "The gears of fate begin to turn" → What event triggered what?
- Quick lookup: Search full text for "brewing", "curtain rises", "gears of fate" — If yes → Hit

**Pattern 4: Hollow Rhythm Filler Words**
Rhythm words used to pad length without corresponding events later.
- ❌ "Undercurrents surging" (appears in outline but doesn't write what is surging) → Must explicitly write who is confronting whom
- ❌ "Treacherous and deceitful" (same as above)
- Quick lookup: Search full text for "undercurrent", "treacherous", "bewildering" — If yes → Hit

**Pattern 5: AI Self-referential Meta**
- ❌ "After a series of adventures"
- ❌ "The core conflict of the story lies in"
- ❌ "What we are going to tell next is"
- Quick lookup: Search full text for "After a series of", "The core conflict lies in", "What we are going to tell" — If yes → Hit
