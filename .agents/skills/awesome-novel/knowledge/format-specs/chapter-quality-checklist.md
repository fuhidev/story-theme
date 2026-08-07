# Chapter Quality Acceptance Checklist

> Used in Phase 4 Step 3 to review the generated chapter text against the chapter outline and writing style.

## Pre-requisite

The chapter text must be generated and provided by the author. The Agent must have access to:
1. `chapters/vol-{N}-ch-{M}.md` (The generated text)
2. `outlines/vol-{N}-ch-{M}.md` (The chapter outline)
3. `settings/writing-style.md` (The writing style guide)

---

## Part 1: Outline Fulfillment Check

Check if the generated text fulfilled the design in the chapter outline.

- [ ] **Plot Advancement**: Are all the key plot points (1, 2, 3...) in the outline covered in the text?
- [ ] **Information State**: Did the characters acquire the information they were supposed to get according to the outline?
- [ ] **Reader Expectation**: Does the chapter deliver on the "Reader's Expectation" (e.g., suspense, payoff) defined in the outline?
- [ ] **Ending Hook**: Is the chapter ending consistent with the designed hook in the outline?

## Part 2: Writing Style Compliance Check

Check if the generated text violates the established writing style.

- [ ] **Narrative Identity**: Does the text read like the defined narrative identity (e.g., close third-person, objective observer)?
- [ ] **Core Principles**: Are any of the hard rules defined in `core_principles` violated?
- [ ] **Common Mistakes**: Did the AI fall into the traps defined in `possible_mistakes` (e.g., overuse of AI-like phrases, too much inner monologue)?
- [ ] **Techniques Used**: Are the recommended `depiction_techniques` actively used in the text?

## Part 3: AI Flavor Detection

Common AI tropes that need to be eliminated:

- [ ] **Over-explaining**: Summarizing the emotional subtext after a dialogue (e.g., "He said, showing his deep concern for her well-being.")
- [ ] **Symmetrical sentence structures**: "On one hand... on the other hand..."
- [ ] **Vague transitions**: "Meanwhile", "Time flew by"
- [ ] **Cliché physiological reactions**: "Heart pounded like a drum", "A chill went down his spine"

---

## Action Output

If any check fails, point out the specific paragraphs that failed, explain why, and provide a revised version of that paragraph as an example.
DO NOT rewrite the entire chapter. Only provide targeted feedback and snippets.