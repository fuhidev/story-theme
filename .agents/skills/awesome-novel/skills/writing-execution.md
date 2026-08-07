# writer skill


## Process Overview

```text
Step 1: Preparation (Confirm volume/chapter number / prompt completeness)
Step 2: Clean context (Reduce interference)
Step 3: Writing (sub-agent execution)
Step 4: Verify output (File exists + word count met)
Step 5: Narrative rules self-check (Go through 7 positive rules one by one)
Step 6: Save AI original snapshot
```

## Step 1: Preparation

1. Confirm Volume number `{N}` and Chapter number `{M}`
2. Read `prompts/vol-{N}-ch-{M}-prompt.md`, confirm the 4 layers are complete. Word count and driving force are obtained from the task layer, narrative POV from Output·Writing Norms
3. Create `.agent/` directory (if not exists), record AI original snapshot path: `{chapter}-draft-ai.md`

## Step 2: Clean Context

Before calling the sub-agent, clean the main agent's reading history (genre-example, anti-ai, and other files are no longer needed). Reduce interference context for the sub-agent.

## Step 3: Writing (sub-agent execution)

Launch the sub-agent (recommend flash model), pass in the following complete instructions:

```markdown
## Role
Full chapter main text writing. Only read the prompt file, write the complete chapter main text in one go. Chapter outline constraints have all been injected into the prompt.

## Scope
- Do: Read the prompt, write the whole chapter in the order of narrative paragraphs
- Don't: Do not read volume outline/chapter outline/archives, do not modify the prompt, do not write other chapters, do not write any files under settings/

## Inputs
- `prompts/vol-{N}-ch-{M}-prompt.md` — Main input (4-layer prompt)
- `settings/writing-style.md` — Writing style methodology
- `settings/genre-setting.md` — Genre settings

## Outputs
- `archives/vol-{N}-ch-{M}-{slug}.draft.md` — Full chapter main text draft

## Writing Rules
- Write in the 1→N order of the prompt narrative paragraphs, transition smoothly between paragraphs
- The writing guidelines for each paragraph must be fulfilled (scene/emotion/character state/ending imagery)
- The ending stops at the imagery or state specified by `ends_with` in the final paragraph
- The main text must not contain explanations, descriptions, or guide words (do not write "he felt", "he realized")
- The word count must not be less than 80% of the target word count in the prompt task layer

## Prohibited (Violation means rewrite)
- Do not add character names, details, or descriptions not appearing in the prompt on your own
- Do not fabricate information not requested by the chapter outline/prompt out of thin air
- Extra naming: If the prompt doesn't give a name, don't write a name, use generic references like "those few people", "another person"
- Format violation: The main text is prohibited from using `---` separators, Markdown headers (`#`, `##`, etc.), and other Markdown tags

**Principle: Plot, dialogue, and character behavior not written in the prompt should not be added independently; reasonable detail filling and atmosphere building are allowed — if the prompt says "he is waiting for someone in a cafe", you can write the environmental atmosphere, micro-actions, and thoughts, but you cannot write "he waited and his enemy arrived".**
```

After the sub-agent finishes executing, it returns. The main Agent checks if the output file exists.

## Step 4: Verify Output

| Check Item | Action |
|--------|------|
| Output file exists? | Does `archives/vol-{N}-ch-{M}-*.draft.md` exist? If not → retry 1 time |
| Word count met? | ≥ 80% of chapter outline word count? If lacking → mark deficit, ask author if acceptable |
| File location correct? | Written to `archives/` directory and not elsewhere? |

## Step 5: Narrative Rules Self-check

Read through the newly written draft, do a round of quick checks against the narrative rules injected into the prompt:

| Rule | Self-check |
|------|------|
| Rule 1 (Perception signals first) | Is there a "Protagonist + Perception verb" structure at the beginning of paragraphs? (He saw/He heard/He discovered) → If yes, change to throw perception facts first |
| Rule 2 (Cognitive verb restraint) | Are words like "He discovered / He felt / He noticed" too dense? → Prioritize using action replacement, keep ≤ 2 times/chapter at key nodes |
| Rule 3 (Sort by depth of impression) | Are there continuous "first→then→next→finally" structures? → Resort by perception intensity |
| Rule 4 (Use specific experiences) | Are there tags like "various / one after another / a series of"? → Replace with specific sensory details |
| Rule 5 (Natural causality) | Are there too many causal explanations (because/so/therefore continuously)? → Delete redundant ones, keep necessary ones |
| Rule 6 (Dialogue like human speech) | Is dialogue too complete (subject/verb/object intact), lacking colloquial flavor? → Add pronouns/filler words/verbal tics |
| Rule 7 (Narrative naturally warm) | Is there excessive rhetoric or deliberate pretentiousness? → Change to natural narrative, allow moderate narrative warmth. Do not use extreme plain sketching for the sake of "not telling" |

If you find problems, fix them directly, **do not leave contraband for the anti-ai pipeline**. The anti-ai's job is to sweep up stragglers, not to wipe your ass.

## Step 6: Save AI Original Snapshot

After verification passes:

1. Read the newly generated draft `archives/vol-{N}-ch-{M}-{slug}.draft.md`
2. Copy one to `.agent/{chapter}-draft-ai.md`

This snapshot is used for subsequent diff comparison during archiving, preserving the original version before the writer's modifications.
