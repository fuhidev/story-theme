# Role Deduction Record Format Specification

> The deduction record is the product of the character deduction sandbox, recording the behavior/lines/expressions and scene state changes of each character in each round. It serves as optional reference material for the prompt-crafter to read.

## File Path

`sandbox/vol-{N}-ch-{M}/deduction-{serial_number}.md`

## Overall Structure

```markdown
# Role Deduction Record

## Metadata
...

## Initial Scene State
...

## Deduction Process
### Round 1
#### Round 1 · Character: {Character name}
...
### Round 2
...

## Deduction Results
...
```

## Field Specification

### Metadata

```markdown
## Metadata
- **Roll Number**: 1
- **Chapter Number**: 3
- **Trigger Scenario**: {One sentence describing the starting point scenario of the deduction, extracted from chapters_summary or provided by the author in Step 2}
- **Characters**: {Character 1, Character 2, Character 3}
- **Deduction Round**: {N}
- **Deduction Goal**: {The deduction goal set by the author in Step 2}
```

- `Trigger Scenario` is quoted from `chapters_summary` of `volumes/vol-{N}.md`, or provided by the author when setting up the stage in Step 2.
- The `Deduction Goal` is set by the author when setting up the stage in Step 2 and serves as the basis for judging the achievement of the deduction.

### Initial Scene State

```markdown
## Initial Scene State

### Scene Setting
- **Location**: {Specific location}
- **Time**: {Specific time}
- **Ambience**: {Ambience description}

### Initial State of Character
#### {Character name}
- **Location**: {Physical location}
- **Mood**: {Current Mood}
- **Known**: {Information known to the character}
- **Unknown**: {Information that the character does not know}
- **Current Goal**: {The goal the character wants to achieve in this scene}
- **Micro Behavioral Patterns**: {Habitual small actions when tense/relaxed}

### Active Hook / Foreshadowing
- {Hook 1}
- {Hook 2}
```

- `Initial State of Character` must be extracted from the following fields of `settings/character-setting/{id}.md`:
  - Cognitive 6-layer model → deduce possible response patterns of characters
  - Status history → current mood and location
  - Emotional arc → recent triggering event and intensity
- `Micro Behavioral Patterns` is extracted from the language characteristics and habit descriptions in the character settings (not a hard field, if it does not exist, write "No information").
- `Active Hook` is extracted from `settings/foreshadowing.md`.

### Round Output (Core)

Each round of output includes two parts: **"AI deduction output"** and **"Author intervention record"**:

```markdown
### Round {N}

#### Round {N} · Role: {Character name}

**Line / Action / Appearance:**
> {What words the character said, what actions they took, and what expressions they had}

**Scene Status Changes:**
- {Dimension of change}: {Specific changes}
```

#### Output Requirements

Only three items are output, without the additional reasoning process:
1. **Lines / Actions / Appearance** — What the character said, did, and expressed, summarized after a sentence.
2. **Scene State Change** — What changes in the character's behavior.
3. Remove internal reasoning content such as cognitive level analysis, behavioral derivation, and conclusions.

- Text content must maintain **strict character-based information boundaries** - information unknown to the character does not appear in their behavior.
- Do not write inner monologues or unexpressed hidden motivations of the characters.

#### Scene State Change Rules

Each character's behavior must change at least one dimension of the scene state:

| Dimension of Change | Description | Example |
|---------|------|------|
| **Atmosphere** | Overall atmosphere change | "From small talk to mutual testing" |
| **Relationship** | Changes in the power/closeness relationship between characters | "Fang Yan takes the initiative to draw clear boundaries" |
| **Information** | Changes in information gap | "Lu Zheng confirmed that Fang Yan knew the inside story" |
| **Position** | Changes in the character's physical position | "Wang Wu moved his stool toward the door" |
| **Item** | Scene object state changes | "The tea cup was rubbed repeatedly, and the rim of the cup was stained" |
| **Conflict** | Conflict escalation/de-escalation | "The confrontation between the two sides has formed" |

#### Cognitive Layer Reference Rules

Each output is accompanied by a reference to the cognitive layer, in the format of `{Role name} Layer {N} ({Layer name}): {Derivation}`:

```markdown
**Involving the cognitive layer:**
- Lu Zheng Layer 4 (Ability): Observed Fang Yan’s scanning movements and judged that he was on guard.
- Lu Zheng Layer 2 (Self-positioning): "I am a patient person" - chose not to ask directly and let the other person relax first.
```

- The layer number (Layer 1-Layer 6) and layer name (Worldview / Self-positioning / Values / Ability / Skills / Environment) must be consistent with the cognitive 6-layer model of `character-setting-style.md`.
- Can reference multiple layers.
- If a layer is empty in the character settings, it will not be referenced.

#### Author Intervention Record

After each author intervention, the intervention record is appended below the round output:

```markdown
---

**Author Intervention:** Modify / Restart / Jump in line / Add information / Terminate

{Description of intervention content}

**Intervention Impact:**
- {Changes in scene status after modification}
```

- `Modify`: The author has changed some of the output of the current character → affecting the deduction of subsequent characters in this round.
- `Restart`: The author overturns the current character's output and regenerates it → keeps the previous state unchanged.
- `Jump in line`: The author requires a certain character to appear first → adjust the order and continue.
- `Add information`: The author adds information that the current character does not know but should know → Update the character's knowledge status.
- `Terminate`: The deduction is over and the results are sorted.

### Deduction Results

```markdown
## Deduction Results

### Final State of the Scene
- **Location**: {Same as above or changed}
- **Time**: {Time after the deduction is advanced}
- **Ambience**: {Final Ambience}

### Final State of Character
#### {Character name}
- **Emotion**: {Emotion at the end}
- **New Knowledge**: {New information obtained during the deduction}
- **Next Step**: {The character’s subsequent action tendency under the current goal}
- **Behavioral Consistency**: {Conform to character / Deviate from character + explanation}

### Writing Materials that can be Refined
1. **{Material type}** — {Specific description}
2. **{Material type}** — {Specific description}

### Compare with Deduction Target
- **Goal Completion**: {Part / Full / Excess}
- **Deviation Description**: {The difference between the deduction results and the expected goal}
```

- `Final State of Character` is extracted from the last scene state during the deduction process.
- `Writing Materials that can be Refined` are for the prompt-crafter to use: dialogue fragments, micro-behavior patterns, scene atmosphere words, character relationship change nodes.
- `Compare with Deduction Target` to verify whether the deduction has achieved the goals set in Step 2.

## Validation Rules

- Does not contain inner monologue or hidden motives (only visible behaviors are recorded in the deduction).
- The output of each round includes: lines / actions / expressions + scene state changes.
- Internal reasoning processes such as cognitive layer analysis and behavioral derivation do not appear in the output.
- Before the first deduction, the initial scene status must be confirmed by the author.