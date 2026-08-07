# Fetch Reference skill

Prepare reference materials for chapter outline planning. Not a chapter outline, not a synopsis — but materials to present to the author to finalize ideas.

## Process

Input Collection → Character Expectation Simulation → Character Collision → Output Reference Materials → Multi-dimensional Advice

## I. Input Collection

You must read all four sets of information below before starting. If one is missing, stop; do not make things up from memory.

### 1. Volume Outline Positioning

Read the summary of this chapter in `volumes/volume-{N}.md#chapters_summary`. This is the skeleton of the story — how far this chapter needs to advance in the volume.

**Record:** A single sentence. Do not add interpretation, do not add speculation.

### 2. Character Cognition Reconstruction

You must read the following three items before you can say, "I understand this character's state at this moment":

- **Character setting files** (settings/character-setting/): Core personality, motives, capability boundaries
- **Previous chapters** (At least the preceding 3 chapters, except for the first chapter): What this character recently experienced, what they know
- **knowledge_state tracking**: The `role_information_state` at the end of each chapter records the character's known/unknown state at that time. Read backwards from the most recent chapter to accumulate the character's knowledge inventory "up to this chapter"

**Summarize the state of each appearing character according to the following template:**

```
{Character Name}
  Just experienced: {Summary of events related to them in previous chapters}
  Currently knows: {Knowledge inventory accumulated based on knowledge_state, list everything regardless of importance}
  Doesn't know: {Information they haven't encountered yet based on knowledge_state}
  Current motive: {What they most want to push forward right now / The conflict they care about most / What they most want to avoid}
```

### 3. Context of Previous Chapters

Read the chapter outlines and main text of previous chapters (at least the preceding 3 chapters, except for the first chapter), focusing on extracting:

- **required_changes**: What changed at the end of the previous chapter — this is the starting point of this chapter
- **emotional_hook**: The emotional gap at the end of the previous chapter — what emotions the reader carries into this chapter
- **reader_expectation**: If the reader expectations from the previous chapter are not answered in this chapter, what will the reader be waiting for

### 4. Hooks Ledger

The source of truth for hooks lies in the `payoff_plan` of each chapter outline and the hooks field in character setting files. Do not rely on memory; you must read chapter by chapter.

Inventory according to the following classification:

```
Resolvable (must_resolve candidates):
  Old hooks that have reached the time to be resolved. These hooks usually appear in the character's
  pending section and have undergone enough buildup.
  Judgment standard: The reader has expected a follow-up on this matter for more than 1-2 chapters.
  · {Hook description} → Plant location: vol-{N}-ch-{M}.payoff_plan.partial_advance

Can be advanced (partial_advance candidates):
  Can be pushed a bit but not fully resolved. After pushing, the state goes from "suspended" to "more suspended"
  or "has a new direction".
  · {Hook description} → Current state: {pending / partial_advance}

Absolutely untouchable (must_hold candidates):
  Hooks that reveal the bottom cards if touched. Once touched, subsequent chapter designs are ruined.
  These hooks are usually tied to the core suspense of the story or a character's ultimate motive.
  · {Hook description} → Reason: {Why it cannot be touched}
```

## II. Character Expectation Simulation

After completing input collection, answer the following questions one by one for each character.

**Rules:**
- Must be done for every appearing character, including characters who haven't officially debuted but might be active in the background
- Every answer must cite the basis (from which step of the input collection)
- "Intuition" without basis doesn't count; go back and check the files

### Question Template

```
From {Character Name}'s perspective:

1. What do they think the current situation is?
   Basis: {Cite the result of "Character Cognition Reconstruction"}
   → Their understanding of the current situation (Note: this understanding may differ from reality;
      things the character doesn't know do not exist to them)

2. What do they want to happen in this chapter?
   Basis: {Cite "Current motive"}
   → The specific thing they most want to push forward. Not "investigate the case," but "go to the rental house
     and search it thoroughly" or "find Fang Yan to inquire about inside information."

3. How will they act?
   Basis: {Cite capability preferences/behavioral habits in the character setting}
   → What to do first step, what to do second step. The action chain should be as specific as possible.
      Lu Zheng is an ex-cop → He will look for trace evidence on the scene first, not check surveillance footage first.
      Su Mo is an ordinary person's sister → She will find someone she trusts to help, not investigate on her own.

4. What is their bottom line for this chapter (things that absolutely must not happen)?
   Basis: {Cite fears/avoidance tendencies in their cognitive model}
   → "If X happens in this chapter, they will feel something is wrong."
      Lu Zheng doesn't want Fang Yan to know he is still investigating the old case.
      Su Mo doesn't want her parents to know her sister is missing.

5. If what they expect doesn't happen in this chapter, how will they react?
   Basis: {Cite personality elasticity in the character setting}
   → Will they take a step back and change plans? Or will they stubbornly persist? What trail will it leave for subsequent chapters?
```

### Deduction Requirements

Character expectations are not extracted from a single sentence in the character setting file; they require "deduction":

```
Character setting file says "Lu Zheng has strong observational skills"
→ Cannot directly output "Lu Zheng will observe in this chapter"
→ Needs to deduce to specifics:
   "Last chapter he touched the charger plug and found residual heat (Source: ch-2 key_point 3),
    in this chapter, when entering the room, he will first touch the cup/bedsheets to confirm how long the person has been gone"
```

**Mark areas that cannot be deduced as "TBD (To Be Determined)"**, do not fabricate, leave them for the author to fill in.

### Character Collision

After simulating all character expectations, perform a cross-reference:

- **Points of Agreement**: Two characters both want something to happen in the same scene → This is the anchor point of the chapter
- **Points of Conflict**: One character wants X, another doesn't want X to happen → This is the dramatic fuel of the chapter
- **Blind Spots**: Things no character noticed → New hooks might be planted here

### Special Handling — First Chapter / New Character Debut

- **First Chapter**: No previous chapter knowledge inventory, but the character setting files will specify their life state when the story begins. Deduce starting from "the day before this moment": How does the protagonist normally spend this day? What broke the routine?
- **New Character Debut**: No accumulation from previous chapters, but the character setting file will specify "what they are doing at this time." Cut in from their world line: What are they busy with right now? What information will pull them into the main plot?

## III. Output Reference Materials

Organize the analysis results above into the following format and show it to the author.

### Format

```
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
  Volume {N} Chapter {M} —— Reference Materials
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Volume Outline Positioning:
{chapters_summary one sentence, no interpretation added}

───────────────────────────────────────────────────
Character Expectations
───────────────────────────────────────────────────

{Character Name}
  Just experienced: {Event summary}
  Currently knows: {Knowledge inventory}
  Doesn't know: {Information blind spots}
  Current motive: {What they most want to push forward}

  What they want to happen in this chapter:
  · {Specific expectation 1} ← Basis: {Source citation}
  · {Specific expectation 2} ← Basis: {Source citation}

  Their bottom line:
  · {Things that absolutely must not happen} ← Basis: {Source citation}

  How they will act (Action chain):
  · {First step}
  · {Second step}
  · {Third step}

{Next character —— Same format as above}

───────────────────────────────────────────────────
Inter-Character Relationships
───────────────────────────────────────────────────

Points of Agreement:
· {Scene/Event} — {Character A} and {Character B}'s goals converge here

Points of Conflict:
· {Conflict event} — {Character A} wants {X}, {Character B} wants {Y}

Blind Spots:
· {Things all characters missed} — New hooks might be planted here

───────────────────────────────────────────────────
Touchable Hooks in this Chapter
───────────────────────────────────────────────────

Resolvable:
· {Hook name} → Planted: vol-{N}-ch-{M} | Enough buildup, can be resolved in this chapter

Can be advanced:
· {Hook name} → Current state: {partial_advance} | Push it a bit

Absolutely untouchable:
· {Hook name} → Reason: {Reveals bottom cards/Breaks suspense/Character shouldn't know yet}

───────────────────────────────────────────────────
Author Feedback
───────────────────────────────────────────────────

□ Is the volume outline positioning accurate?        Describe/Modify:
□ Are Character A's expectations right?       Describe/Modify:
□ Are Character B's expectations right?       Describe/Modify:
□ Are the hook judgments reasonable?        Describe/Modify:
□ Are there any missed characters or hooks?   Describe/Modify:

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
```

### Presentation Considerations

**Do not dump all the information at once.** If there are many characters and a lot of information, present it in batches:

1. First, show the volume outline positioning and the hooks ledger (these are objective information, the author doesn't need to judge if they are "right")
2. Next, show one character's expectations → Wait for author feedback → Then the next one
3. Finally, show inter-character relationships and the complete hook classification

**During the author's calibration process**, every time an item is modified, synchronously update the citations of related items. For example, if the author says "Lu Zheng shouldn't find out who the sticky note belongs to in this chapter" → At the same time, move "sticky note ownership" in the hooks ledger from "Resolvable" to "Absolutely untouchable."

**If the author says "pretty much", "up to you", or "whatever works"**: Do not let it slide. Probe for specifics: "Does 'pretty much' mean all character expectations are correct or all incorrect? Which character is the most accurate? Which one is uncertain?"

## IV. Multi-dimensional Advice

After producing the reference materials, before presenting them to the author for calibration, first do a round of multi-dimensional analysis. The purpose is to help the author quickly pinpoint problems.

### Analysis Dimensions

- **Plot Connectivity:** Previous chapter ending (required_changes) → Does it connect directly to the starting point of this chapter? What transition is missing? Are there any skipped steps?
- **Character Configuration:** Are the existing characters enough to support this chapter? Is a key character missing to drive the plot? Should a certain character not appear in this chapter?
- **Rhythm Anticipation:** Is the rhythm of the scene sequence deduced from the reference materials reasonable? Is the climax placed too early or too late? Is a buffer transition needed?
- **Information Gaps:** How much of the reader's emotions from the previous chapter (emotional_hook) did this chapter respond to? What else are the readers waiting for that hasn't been covered?

### Output Format

One sentence judgment for each dimension + one specific piece of advice (1-2 points), no forced push. Present it to the author along with the reference materials.

## V. Calibration Standards

The judgment standard for reference materials passing calibration: **The author can point to each character's expectations and say "Yes, they would indeed act like this" or "No, they wouldn't think this way"**.

Not a single character adjusted → Danger signal. It means the materials are not specific enough for the author to judge.
All characters receive a "pretty much" → Equates to not passing. Probe for a very specific point.
At least one character is adjusted → Normal. It shows the materials are specific enough to elicit a reaction from the author.
Multiple characters are adjusted → Good. The calibrated materials are the true starting point.
