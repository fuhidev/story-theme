# Prompt Production Standard Operating Procedure (SOP)

> This document defines the standard process for generating the final writing prompt for the AI to draft the chapter text. It ensures all context, outlines, and style rules are properly assembled into a single actionable prompt.

## The Assembly Line

The final prompt is an assembly of multiple components. Do not write the prompt from scratch; assemble it from existing files.

### Component Sources

1. **Global Context**: From `story.md` (Main Storyline, Volume Planning).
2. **Writing Style**: From `settings/writing-style.md` (Role, Principles, Mistakes, Techniques).
3. **World & Character**: Relevant snippets from `settings/world-setting.md` and `settings/character-setting/*.md`.
4. **Chapter Outline**: The entire content of `outlines/vol-{N}-ch-{M}.md`.
5. **Memory**: Applicable entries from `memory/` and `knowledge/permanent-memory.md`.

## Assembly Structure

The final prompt should be structured in XML-like tags to cleanly separate instructions from context.

```xml
<Role>
{Insert `role` from writing-style.md}
</Role>

<Context>
  <StoryArc>
  {Insert current volume's core conflict}
  </StoryArc>
  <PreviousEvents>
  {Brief summary of the previous chapter to maintain continuity}
  </PreviousEvents>
</Context>

<Characters>
{Insert relevant character info (Current state, micro-habits, cognitive layer) for characters appearing in this chapter}
</Characters>

<Style_Guidelines>
  <Core_Principles>
  {Insert `core_principles` from writing-style.md}
  </Core_Principles>
  <Avoid_Mistakes>
  {Insert `possible_mistakes` from writing-style.md}
  </Avoid_Mistakes>
  <Techniques>
  {Insert `depiction_techniques` from writing-style.md}
  </Techniques>
</Style_Guidelines>

<Writing_Memory>
{Insert relevant feedback/rules from previous chapters}
</Writing_Memory>

<Task>
Write Chapter {M} based on the following outline.

<Outline>
{Insert the entire chapter outline here}
</Outline>

<Constraints>
- Word count target: {Target}
- {Insert any hard constraints from the outline}
</Constraints>
</Task>
```

## Production Steps

### Step 1: Context Gathering
- Identify which characters appear in the chapter. Fetch their current state.
- Check the previous chapter's ending to ensure smooth transition.

### Step 2: Memory Retrieval
- Query the memory files for any rules related to the characters or the specific type of scene (e.g., action scene, dialogue scene). Include only relevant memory entries to save context window.

### Step 3: Assembly
- Map the gathered data into the XML structure above.

### Step 4: Verification
- Does the prompt include the `Must Resolve` and `Must Suppress` instructions?
- Is the emotional peak clearly communicated?
- Are the hard constraints visible?

## Output
Save the assembled prompt to `prompts/vol-{N}-ch-{M}-prompt.md`. This file will then be fed to the writing agent.