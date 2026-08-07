# anti-ai skill — Anti-AI Pipeline

## Responsibilities

Read the writer's draft, detect and clear AI traces through the Phase 1-4 pipeline. **Do not change the plot, only change the expression.**

## Process Overview

```
Input: archives/*.draft.md (writer's original output)

Phase 1 Scan — Full text scan, categorize and mark AI flavor locations according to Gates A-F
Phase 2 Diagnosis — Score on 6 quantitative metrics, grade (Light/Medium/Heavy)
Phase 3 Itemized Clearance — Gate A-F systematic modification (multi-round convergence)
Phase 4 Report — Output modification report

Output: archives/*.anti-ai.md
```

## Reference Files

| File | Purpose |
|------|------|
| `knowledge/anti-ai/common-rules.md` | Graded banned word list, sentence templates, replacement strategies |
| `knowledge/anti-ai/anti-ai-writing.md` | Complete guide to removing AI flavor (fingerprints/patterns/example library) |
| `knowledge/anti-ai/boundary-cases.md` | False kill protection — Do-not-modify list (character/dialogue/punctuation exemptions) |
| `knowledge/anti-ai/{genre}.md` | Genre-specific anti-AI positive and negative examples |

---

## Phase 1: Scan

Read the full text, mark AI flavor locations sentence by sentence, and categorize them by Gates A-F.

### Gate A: Banned Words

Scan hits any vocabulary in the banned-words table → Mark location and entry with `[A]`.

Scan scope: Full text word level, independent of context.

### Gate B: Sentence Patterns

| ID | Pattern | Example |
|----|------|------|
| B1 | "Not A, (but) B" | "He was not angry, but disappointed" |
| B2 | ", with..." adverbial | "He looked out the window, with a hint of melancholy" |
| B3 | "As if / like / just like..." | "The voice came as if from far away" |
| B4 | "Let / make / cause" causal pattern | "This scene made him think of his childhood" |
| B5 | "When..." | "When he pushed the door open" |
| B6 | "Meanwhile" scene switch | "Meanwhile, in another place" |
| B7 | "Seems / as if + perception verb" | "He seemed to hear something" "He felt as if he saw" |

### Gate C: Psychological Description

| ID | Pattern | Example |
|----|------|------|
| C1 | "He felt / thought / realized" direct statement | "He felt a wave of unease" |
| C2 | Emotion word direct statement | "He was very nervous" "She was very sad" "He was angry" |
| C3 | "A surge of... welled up in the heart" | "A warm current welled up in his heart" |
| C4 | "He couldn't help but / couldn't resist" | "He couldn't help but shiver" |
| C5 | Inner debate | "A voice said... another voice said..." |
| C6 | Repetitive description (same info split across multiple paragraphs) | Same scene/emotion written repeatedly over multiple consecutive paragraphs |

### Gate D: Rhythm

| ID | Pattern | Description |
|----|------|------|
| D1 | Consecutive parallelism (≥3 sentences with same structure) | "Maybe... maybe... maybe..." "No... no... not even..." |
| D2 | Long sentence overload (single sentence > 40 words) | Long sentence without pauses |
| D3 | Paragraph overly dense (single paragraph > 60 words without line break) | Information piled up in one paragraph |
| D4 | Consecutive paragraphs of the same length (≥3 paragraphs) | Uniform paragraphs, no variation in length |
| D5 | Consecutive same subject (≥4 sentences) | Continuous arrangement starting with the same subject |
| D6 | Monotonous sentence structure (5 consecutive sentences of similar length) | Small fluctuation in sentence length |
| D7 | Em dash parenthetical redundancy (——description——) | "A——lengthy appositive——did B" → Write directly "A did B" |

### Gate E: Dialogue

| ID | Pattern | Example |
|----|------|------|
| E1 | Dialogue too complete | Every sentence is a complete written sentence |
| E2 | Excessive tags | "He said angrily" "She said softly" |
| E3 | Uniform tone for everyone | Everyone speaks in the same way |
| E4 | Template small talk | "Are you okay" "I'm fine" |
| E5 | Overly dense dialogue | ≥4 consecutive sentences of pure dialogue, without action/silence/environment/inner thoughts in between |

### Gate F: Ending

| ID | Pattern | Example |
|----|------|------|
| F1 | Sublimating ending | "He finally understood..." |
| F2 | Summarizing ending | "This is the meaning of..." |
| F3 | Teaser ending | "What he didn't know was..." |
| F4 | Exclamatory ending | "...!" High emotion conclusion |

### Scan Output

Scan results are temporarily recorded for Phase 2 quantification:

```
Gate A: Hit X places (entry list)
Gate B: Hit X places (categorized by B1-B7)
Gate C: Hit X places (categorized by C1-C6)
Gate D: Hit X places (categorized by D1-D6)
Gate E: Hit X places (categorized by E1-E5)
Gate F: Hit X places (categorized by F1-F4)
```

---

## Phase 2: Diagnosis

### 6 Quantitative Metrics

| Metric | Calculation Method | Light | Medium | Heavy |
|------|---------|----|----|----|
| Banned Word Density | Gate A hits / thousand words | <5 | 5-12 | >12 |
| Parallel Paragraph Count | Number of consecutive paragraphs with the same sentence structure | <2 | 2-4 | >4 |
| Psychological Word Ratio | Gate C1-C4 hits / total paragraphs | <15% | 15%-30% | >30% |
| Dialogue Tag Density | Gate E2 hits / number of dialogue sentences | <10% | 10%-25% | >25% |
| Average Sentences per Paragraph | Total sentences / total paragraphs | 2.5-4 | 4-5.5 | >5.5 or <2 |
| Repetitive Description Density | Gate C6 hits / thousand words | <1 | 1-3 | >3 |

### Grading Rules

Take the **highest tier** among the 6 metrics as the final grade:

| Grade | Processing Scope |
|------|---------|
| Light | Gate A + B (Banned words + Sentence patterns) |
| Medium | Gate A + B + C + D (Plus psychological + Rhythm) |
| Heavy | Gate A-F fully open (Includes dialogue + ending) |

> Grading information is passed to Phase 3 with the draft, determining which Gates participate in the modification.

---

## Phase 3: Itemized Clearance

### Pre-check (Before each round)

Read `knowledge/anti-ai/boundary-cases.md`, first perform an exemption check for each Gate hit:
- Is the hit in the boundary-cases list? → Yes → Skip (Mark `[SKIP: False kill protection]`), do not modify
- Uncertain → Keep original text, mark `[Doubt: Suspected false kill]` in Phase 4

### Convergence Rules

1. Execute modifications Gate by Gate according to the scope determined by the grade
2. **If the same paragraph has no changes for two consecutive rounds → Skip that paragraph**
3. **Maximum of 3 rounds for the full text** (passing all Gates per round)
4. **If there are still ≥10 places in the 3rd round** → Mark `[Needs Review]` and continue
5. Output an arbitration request each round, waiting for user confirmation on whether to continue

### Gate A: Banned Word Replacement

Supplementary Rules:
- Most toxic patterns (★★★★★): Replace upon appearance (see common-rules.md)
- Level 1 banned words: Replace upon appearance
- Level 2 banned words: Replace when exceeding threshold
- Metaphor check: "As if / like / just like / akin to" → Delete or use plain description

### Gate B: Sentence Pattern Breakdown

| Pattern | Processing |
|------|------|
| "Not A but B" | Write B directly |
| ", with..." | Break into short sentences or change to action |
| "As if / like... in general" | Delete or use plain description |
| "Let / make / cause" | Delete "let", subject acts directly |
| "When..." | Break into independent action sentences |
| "Meanwhile" | Start directly with a scene switch |
| "Seems / as if + perception verb" | Delete "seems / as if", write the perceived fact directly |

### Gate C: Externalizing Psychology

| Hit Pattern | Processing |
|---------|------|
| "He felt / thought / realized" | Delete the guide, write the perceived fact directly. **This rule is not triggered at the end of a dialogue sentence** ("So...?" He realized his voice was shaking a bit) |
| Emotion word direct statement (nervous / sad / angry) | Replace with physical reactions (hands shaking / staring blankly / bulging veins) |
| "A surge of... welled up in the heart" | Replace with action |
| "Couldn't help but / couldn't resist" | Write the action directly |
| Inner debate | Delete until only the conclusion remains |
| Repetitive description | Merge or delete redundant paragraphs |

Emotion word → Physical detail quick lookup:

| Emotion Word | Replace With |
|--------|--------|
| Nervous / Anxious | Hands shaking, sweaty palms, faster breathing, restlessness |
| Angry | Clenching fists, veins bulging, voice becoming softer instead |
| Fearful | Stepping back, holding breath, dilated pupils |
| Sad | Staring blankly, hands stopping, movements slowing down |
| Surprised | Hand freezing in mid-air, stunned, forgetting to breathe |
| Heartache / Heartbroken | Fingers digging into flesh without feeling pain |
| Aggrieved | Biting the lower lip, leaving a white mark |
| Despairing | Cigarette ash falling on a pant leg without being flicked away |

### Gate D: Breaking the Rhythm

| Check Item | Processing |
|--------|------|
| Consecutive parallelism | Keep the strongest one, scatter the rest |
| Long sentence overload (>40 words) | Break into two sentences at commas/action transitions |
| Overly dense paragraphs | Break paragraphs according to action/information changes; use single-sentence paragraphs for rhythm emphasis |
| Consecutive same length paragraphs | Break one paragraph into a single-sentence paragraph, or merge adjacent short paragraphs |
| Consecutive same subject (≥4 sentences) | Adjust subject positions, alternate long and short sentences |
| Monotonous sentence structure | Break one long sentence into short ones, or merge two short sentences into a long one |

### Gate E: Removing Dialogue Tone

| Check Item | Processing |
|--------|------|
| Dialogue too complete | Add incomplete sentences, filler words, repetitions |
| Excessive tags | Use actions/context to carry on, delete adverbs |
| Uniform tone for everyone | Differentiate tone according to character personality |
| Template small talk | Replace with blank space, hesitation to speak |
| Overly dense dialogue (≥4 consecutive pure dialogue sentences) | Insert actions/environment/silence/inner thoughts in between to interrupt |

### Gate F: Removing Ending Sublimation

| Check Item | Processing |
|--------|------|
| Sublimating ending | Delete, stop on the image/action |
| Summarizing ending | Delete, let the scene end naturally |
| Teaser ending | Delete or replace with a specific hook |
| Exclamatory ending | Change to a period or action |

### Word Count Control

Deletion amount limited by grade:

| Grade | Deletion Limit |
|------|---------|
| Light | ≤ 15% of original text |
| Medium | ≤ 25% of original text |
| Heavy | ≤ 35% of original text |

### Whitelist

If a `.anti-ai-whitelist` file exists in the same directory, the paragraphs listed within it skip all Gate checks.

---

## Phase 4: Report

Output a report after modifications are completed, containing the following:

### Word Count Changes

```
Original word count: XXXX
Word count after modification: XXXX
Increase/Decrease: +/- XXX (±X%)
```

### Modification Statistics

```
Gate A Replacements: X places
Gate B Breakdowns: X places
Gate C Externalizations: X places
Gate D Rhythm Breaks: X places
Gate E Fixes: X places
Gate F Modifications: X places
Total: X places modified
Grade: Light / Medium / Heavy
Convergence Rounds: X
```

### Before and After Comparison

Provide at least 1 typical modification comparison of ❌ AI flavor → ✅ Modified for each type of Gate.

---

## Acceptance Checklist

| Check Item | Standard |
|--------|------|
| Most toxic patterns (★★★★★) | 0 places |
| Level 1 banned words | 0 places |
| Level 2 banned words (high frequency) | ≤ 1 place per category |
| Ending sublimation | 0 places |
| Emotional mismatch | ≥ 1 place (not perfectly rational throughout) |
| Information density | At least 1 paragraph is sparse (idle writing / state / mind wandering) |
| Dialogue tags | No 3 consecutive identical tags |
| Dialogue density | No ≥4 consecutive pure dialogue sentences (must be interrupted by actions/environment in between) |
| Em dashes | ≤ 3 times / chapter, no multiple abuses within a single paragraph |
| Plot integrity | Consistent with the original text, no added/deleted plot points |
| Phase 4 Report | Complete output |
