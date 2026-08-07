# Roleplay Sandbox

> Interactive character deduction tool. When the author is stuck on the plot, set up the "stage" and "actors" and give trigger conditions. AI sequentially deduces each character's actions/dialogue/demeanor. The author can modify each round, pushing forward multiple rounds until the plot goal is reached. The deduction record serves as a reference for the author when writing the chapter outline.

## Positioning

**When to use:** The author actively requests plot deduction, or gets stuck during the writing of a new chapter—has a core story but doesn't know how to expand it into specific plots. The deduction record provides character behavior material, helping you write the chapter outline.

**When not to use:** The author is already clear about the plot direction and character behaviors and does not need extra material.

**Relationship with existing process:** The deduction sandbox is an **interactive tool actively invoked by the author**, not in the automatic dispatch process of the novel-agent. It does not generate order files and does not change `.agent/status.md`.

## Process at a Glance

```
Step 1: Confirm Input
   → Read volume outline chapter direction + Character settings (6-layer cognitive model) + Previous chapter ending scene + Hooks
   → Output directory: sandbox/vol-{N}-ch-{M}/

Step 2: Set the Stage (Scene Initialization)
   → Ask the author for scene settings: Location / Time / Atmosphere / Character positions / Trigger event
   → Assemble initial scene state → Confirm with author

Step 3: Determine Appearance Order
   → Author determines the chronological order of character deductions

Step 4: Sequential Round Deduction
   ┌→ for each round:
   │    for each character:
   │      ① AI generates character output (Action + Dialogue + Demeanor + Scene change)
   │      ② Show to author
   │      ③ Author chooses: Accept / Modify / Redo / Cut in line / Add info / Terminate
   │      ④ Update scene state → Next character
   │    End of round → Check termination conditions
   └→ Until terminated

Step 5: Termination Judgment → Goal achieved / Scene converges / Author calls a halt

Step 6: Output Deduction Record
   → Write to sandbox/vol-{N}-ch-{M}/Deduction{number}.md
```

## Step 1: Confirm Input

### Files that MUST be read

| File | Purpose |
|------|------|
| `volumes/vol-{N}.md` | Volume outline: This chapter's `chapters_summary` (Who + did what + conflict + what changed) |
| `settings/character-setting/{character_id}.md` | Each appearing character's 6-layer cognitive model + status history |

### Files that MAY be read

| File | Read if it exists |
|------|---------|
| `settings/foreshadowing.md` | List of active hooks |
| Previous chapter `archives/vol-{N}-ch-{M-1}.md` ending scene | Scene initialization reference |

### Key Information Extracted from Volume Outline

- `chapters_summary` → One-sentence plot direction of this chapter, acting as the overall direction for the deduction
- `Emotional Direction` → This chapter's position on the volume's emotional arc, serving as the initial reference for scene atmosphere
- `Conflict Ladder` → Current conflict tier, serving as a boundary reference for conflict intensity in the deduction

### Key Information Extracted from Character Settings

- `6-layer cognitive model` → Detailed descriptions down to each layer, used to deduce character behaviors
- `Status History` → State changes in recent chapters, determining the character's current position and emotion
- `Emotional Arc` → Recent trigger events and intensity
- `hooks` field → Unresolved hooks associated with this character

## Step 2: Set the Stage (Scene Initialization)

### Ask the Author

After finishing reading inputs, ask the author for each scene:

```
We are preparing to deduce {Scene Name} of Chapter {N}.

Please tell me:
1. **Scene Settings** — Where does it take place? What time? What's the atmosphere?
2. **Appearing Characters** — Who is in this scene? Where are they positioned? What are they doing at the start?
3. **Trigger Event** — What event kicks off this scene? (e.g., "Lu Zheng pushes the door open and finds Fang Yan already waiting for him")
4. **Any extra information gaps?** — Is there any special information asymmetry between the characters currently?
5. **What goal do you want to achieve in this round of deduction?** — e.g., "Verify if Fang Yan's reaction fits his persona" or "Test original dialogue rhythm"

If you are unsure, you can just say "Follow the volume outline," and I will extract it from the volume's chapter direction.
```

### Assemble Initial Scene State

Based on the author's answers + the read files, assemble the initial scene state:

```markdown
### Scene Settings
- **Location**: {Extracted or provided by author}
- **Time**: {Extracted or provided by author}
- **Atmosphere**: {Extracted or provided by author}

### Character Initial State
#### {Character Name}
- **Position**: {Physical location, may include posture}
- **Emotion**: {Current emotion, extracted from character status history}
- **Knows**: {Deduced from character status history + previous chapter ending}
- **Doesn't Know**: {Deduced from information gap}
- **Current Goal**: {Deduced from character setting + volume chapter direction}
- **Micro-behavior Patterns**: {Habitual small movements extracted from character setting}
```

### Confirm with Author

Show the initial scene state, using these words:

```
This is the initial scene state. Take a look and see if anything needs to be adjusted?
→ No problem → Proceed to Step 3
→ Modify XX → Update and then proceed to Step 3
```

## Step 3: Determine Appearance Order

```
How should we arrange the appearance order of the characters? Default is by their importance in the current scene:
1. {Character A} — {One-sentence explanation of why they appear first, e.g., "He is the one initiating the dialogue"}
2. {Character B} — {Explanation}
3. {Character C} — {Explanation}

Do you want to change the order, or have a certain character not speak first and intervene later?
```

- Order is ultimately decided by the author
- Once determined, it does not change within this round (unless the author requests a cut-in during deduction)
- The order should remain consistent in each round (unless the author explicitly requests an order change)

## Step 4: Sequential Round Deduction

### Logic for AI Generating Character Output

Perform the following inference steps for each character:

```
Inputs:
- Current scene state (Initial state + Cumulative changes from all previous character outputs)
- Target character's 6-layer cognitive model
- Target character's known state, emotions, goals
- Current scene's conflicts/info gaps

Inference:
1. What does the character "see/hear/perceive" in the current scene? (Based on info boundaries)
2. How does the character's Cognitive Layer 1 (Worldview) interpret the current situation?
3. How does the character's Cognitive Layer 2 (Self-positioning) demand they react?
4. What boundaries are set by the character's Cognitive Layer 3 (Values)?
5. What details can the character's Cognitive Layer 4 (Capability) observe?
6. What can the character's Cognitive Layer 5 (Skills) do?
7. Does the character's Cognitive Layer 6 (Environment) affect their manner of acting?
8. What is the character's current goal? How do the above factors affect their choices?

Output (Only output the result, do not show the inference process):
- Dialogue / Action / Demeanor: What the character said, what they did, what facial expression
- Scene state changes: What the character's behavior changed
```

### Output Format

```markdown
#### Round {N} · Character: {Character Name}

**Dialogue/Action/Demeanor:**
> {What the character said, what action they took, what expression/demeanor}

**Scene State Changes:**
- {Dimension}: {Specific change}
```

### Processing Author Feedback

After each character's output in a round, pause and wait for the author's feedback. Use these words:

```
The deduction for {Character Name} is as above. You can:
- ✅ Accept — Continue to the next character
- ✏️ Modify — Tell me what to change
- 🔄 Redo — Give me a direction, I will rewrite this part
- ⏭ Cut in line — Let another character appear first
- ℹ️ Add info — Supplement background the current character doesn't know but should
- 🛑 Terminate — Stop the deduction here
```

#### Processing Rules for Each Feedback Type

| Feedback | AI Action | Subsequent Impact |
|------|---------|---------|
| **Accept** | Scene state applies current changes, proceed to the next character | Normal |
| **Modify** | Modify output per author's request → Update scene state → Proceed to next character | Subsequent characters in this round see the modified state |
| **Redo** | Revert scene state to the state before this character started → Add author's direction → Regenerate | Doesn't affect outputs of previous characters |
| **Cut in line** | Record current character state → Deduce cut-in character first → Return to current character after finishing | Cut-in character's output becomes the current character's new input |
| **Add info** | Update the "Knows" field in the scene state for this character → Rededuce current character | All subsequent rounds see the updated info boundaries |
| **Terminate** | Scene state freezes → Proceed to Step 5 | — |

### Deduction Iron Rules

1. **Based on Cognitive Layers** — Character behaviors must be derivable from their 6-layer cognitive model. Cognitive layers determine behaviors; you cannot fabricate based on feeling.
2. **No OOC** — Characters cannot do things their Layer 3 (Values) rejects, nor can they have self-cognition unrecognized by Layer 2 (Self-positioning).
3. **Info Gap Transparency** — AI only uses information the character currently knows. Information the character doesn't know cannot appear in their behaviors or dialogue.
4. **Observable** — Output only includes behaviors other characters can see/hear. Do not write inner monologues.
5. **Causal Chain** — Each character's behavior must change at least one dimension of the scene state.
6. **Unidirectional Evolution** — Scene state only evolves forward. Author modifications create new branches, without rolling back other character behaviors that have already happened.

## Step 5: Termination Judgment

### Termination Conditions (Stop if any is met)

1. **Goal Achieved** — The deduction goal proposed by the author has been demonstrated
2. **Scene Converges** — All characters are silent/leave, no new behaviors to deduce
3. **Stagnation Over Limit** — 3 consecutive rounds with no substantive progress; at this point, ask the author, "Should we continue the deduction or stop?"
4. **Author Calls a Halt** — The author chooses "Terminate" at any feedback point

### Post-Termination Processing

```
The deduction has ended, totaling {N} rounds.

To summarize the deduction results:
- Final scene state: {Brief description}
- Key behavior nodes: {Behavior 1 → Behavior 2 → ...}
- Character relationship changes: {Brief description of changes}
- Materials usable for the main text: {Dialogue snippets, micro-behaviors, atmospheric words, etc.}

Should I write the deduction record to a file?
```

## Step 6: Output Deduction Record

According to the format in `knowledge/format-specs/roleplay-sandbox-style.md`, write the complete deduction process into:

`sandbox/vol-{N}-ch-{M}/Deduction{number}.md`

### File Naming

- First run: `sandbox/vol-{N}-ch-{M}/Deduction01.md`
- Running again for the same chapter: `Deduction02.md`, `Deduction03.md`... incrementally increasing
- Ask the author to confirm the file name before writing

### Writing Rules

- Show to the author for confirmation before writing: "This is the deduction record to be written. Does anything need to be adjusted?"
- Write after the author confirms
- File content includes: Metadata, initial scene state, complete deduction process (all rounds + author interventions), deduction results

### File Status

Deduction records are stored in the `sandbox/` directory, at the same level as `chapters/` and `volumes/`. It does not affect the project status and does not appear in `.agent/status.md` or any order file paths.

## Next Steps

After the deduction record is written, tell the author the follow-up paths:

```
The deduction record has been written to sandbox/vol-{N}-ch-{M}/Deduction{number}.md.
You can write the chapter outline based on the character behaviors and dialogue materials in the deduction record, and you will also have specific behavioral references when writing the main text.

Do you need to continue writing the chapter outline? Or do you want to modify the deduction record some more?
```

## Boundaries and Constraints

### What the Sandbox Does NOT Do

- ✗ Does not write order files
- ✗ Does not modify `.agent/status.md`
- ✗ Does not modify character setting files or foreshadowing.md
- ✗ Does not generate main text (writer's responsibility)
- ✗ Does not automatically schedule subsequent steps (decided by the author or novel-agent)

### Usage Boundaries for Sandbox Outputs

Deduction records are reference materials for the author, not mandatory system inputs:
- The author writes the chapter outline and main text based on the character behaviors and dialogues in the deduction record
- Deduction records are not automatically injected into any subsequent processes
