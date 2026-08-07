# Prompt Audit SOP

## Core Principles

- **You are not the prompt-crafter**, you do not have the burden of "maintaining your own work"
- **Your only responsibility is to find problems**, not to fix them, not to explain why it was written this way
- The more problems you find, the more successful the audit
- Each finding must be accompanied by original text evidence and a knowledge base cross-reference
- The standard for passing the audit is "no sufficient evidence proving there is a problem", not "looks fine"

## Inputs

- `prompts/vol-{N}-ch-{M}-prompt.md` — The prompt to be audited
- `chapters/vol-{N}-ch-{M}.md` — Chapter outline (especially the outline corresponding to the scene sequence)
- `settings/genre-setting.md` — Genre type
- Methodology files corresponding to the scene types of this chapter under `.claude/knowledge/scene-craft/`
- `.claude/knowledge/scene-craft/index.md` — Scene methodology index

## Audit Dimensions

### Dimension A: Scene-Technique Coverage (Sparse Validation)

**Purpose:** Ensure the Output·Writing Norms have corresponding scene type writing guidelines, but full coverage is not required.

**Steps:**
1. Extract all scene types from the chapter outline's scene sequence to form a deduplicated list
2. Cross-reference the directory structure of `scene-craft/index.md` to confirm each scene type has methodologies available
3. Check the Output·Writing Norms item by item—**As long as each scene type has at least 1 technique guideline, it is fine. Full coverage is not required, and some scene types are allowed to have no technique guidelines**

**Checklist:**

| Check Item | PASS Standard |
|--------|-----------|
| At least 1 scene type has technique guidelines | Output·Writing Norms contain writing guidelines for ≥ 1 scene type |
| Scene weight annotation | Every scene in the scene sequence has a high/medium/low weight annotation |
| Cross-reference consistency (Loose) | The number of scene types with technique guidelines does not exceed the number of scene types - 1 |

**Judgment Standard:**
- Scene type count > 2 and NO technique guidelines at all → **FAIL**
- Other cases → **PASS (Relaxed validation, incomplete is allowed)**


### Dimension B: Knowledge Point Traceability

**Purpose:** Ensure every scene writing guideline in the Output·Writing Norms has an original knowledge source and has undergone context adaptation (four-step transformation), rather than directly copying the methodology's original text.

**Steps:**
1. Extract each scene writing guideline from the Output·Writing Norms (independent semantic blocks appearing as "guidelines," "requirements," "rules," etc.)
2. For each guideline, find the corresponding methodology original text in `scene-craft/`
3. Judge the transformation quality

**Transformation Quality Classification:**

| Level | Judgment Standard | Example |
|------|----------|------|
| ✅ Successful Transformation | The methodology went through the four-step transformation, adapting to specific character names, info gap relationships, and emotional segments | "Fang Yan lowers his head and drinks tea when avoiding—every time Lu Zheng presses a question, he takes a sip and doesn't answer" |
| ⚠️ Partial Transformation | Quoted the methodology but template traces are obvious, missing at least one of character/info gap/emotion | "A cautious character will use action instead of answering when avoiding" (doesn't specify which character or what action) |
| ❌ Untransformed | Directly copied the methodology's original text or only changed the character name | "Characters don't tell the truth. The gap between surface words and deep meaning is the tension of the dialogue" |
| 🔍 Sourceless Guideline | The writing requirement in the Output·Writing Norms cannot be found in scene-craft methodologies | Might be fabricated by the prompt-crafter themselves or imported from other sources |

**Judgment Standard:**
- Any single ❌ Untransformed → **FAIL**
- 🔍 Sourceless Guidelines ≥ 2 items → **WARN**
- ⚠️ Partial Transformation ≥ 50% of all guidelines → **WARN**
- Others → **PASS**


### Dimension C: Executability Assessment

**Purpose:** Determine whether the writing guidelines in the Output·Writing Norms are specific enough for the writer to directly follow and execute.

**Steps:**
1. Evaluate the writer's executability for each Output·Writing Norms directive

| Level | Features | Example |
|------|------|------|
| Specific | The writer can follow and write directly, with clear operational instructions | "Fang Yan lowers his head to drink tea when avoiding. Every time Lu Zheng presses a question, he takes a sip and looks away" |
| Abstract | The writer knows what needs to be done but not how to write it specifically | "Use actions to reflect the tense atmosphere of the scene" |
| Vague | The writer reads it and doesn't know where to start | "Pay attention to the tension of the dialogue" / "Grasp the rhythm well" |

**Judgment Standard:**
- Vague directives ≥ 1 item → **FAIL**
- Abstract directives ≥ 50% of all guidelines → **WARN**
- Others → **PASS**


### Dimension D: Completeness of Four-Step Transformation

**Purpose:** For each methodology loaded from scene-craft, check if it has fully undergone the four-step transformation.

**Checklist (Check each methodology one by one):**

| Transformation Step | PASS Standard |
|----------|-----------|
| Step 1 Anchor Character | The guideline includes specific character names and reflects how the character's personality/current state affects the writing |
| Step 2 Anchor Info Gap | The guideline reflects how the information asymmetry between characters affects the writing (who is hiding/who is chasing/what is the effect) |
| Step 3 Anchor Emotional Rhythm | The guideline explicitly states how the current emotional segment affects the writing (short sentences for tense segments/long sentences for soothing segments, etc.) |
| Step 4 Fusion Output | A fusion of the above three, forming a directly executable guideline, not three parallel explanations |

**Judgment Standard:**
- Any methodology missing any step from Step 1-3 → **FAIL**
- All methodologies complete the four-step transformation → **PASS**


### Dimension E: Inter-layer Consistency

**Purpose:** Ensure the rules in the Output·Writing Norms do not contradict other sections.

**Checklist:**

| Check Item | PASS Standard |
|--------|-----------|
| Output·Writing Norms POV vs Scene Sequence Emotional Turning Point | Does the POV selection support the scene's emotional goal (first person enhances immersion vs third person creates distance)? |
| Output·Writing Norms Rhythm vs Task Layer·Narrative Goal | Rhythm requirements do not suppress the release of satisfaction points (using a soothing rhythm in a tense segment is a contradiction) |
| Output·Writing Norms Description vs Constraint Red Lines | Description methods do not violate plot red lines |
| Output·Writing Norms Anti-AI Rules vs Texture Requirements | Anti-AI rules and texture requirements do not offset each other |

**Judgment Standard:**
- Any contradiction → **FAIL**
- Completely consistent → **PASS**


### Dimension F: Anti-AI Validation (New Core Dimension)

**Purpose:** Ensure the prompt suppresses the uniform and neat writing inertia of AI from the root.

**Checklist:**

| Check Item | PASS Standard | FAIL Signal |
|--------|-----------|----------|
| Sparse use of techniques | Only 1-2 techniques are injected per scene type, full coverage is not required | Requires the use of ≥ 3 techniques or "all techniques" per scene type |
| Primary and Secondary Distinction | Output·Writing Norms explicitly require distinguishing between primary and secondary ink (high weight detailed, low weight brief) | No rules for primary/secondary distinction, implying "uniform description" |
| Four-step execution logic | Output·Writing Norms contain focus locking/sensory layering/rhythm alternation/information control | Missing 2 or more of these |
| Imperfection Constraints | Texture requirements include half-finished sentences/slice-of-life details/paragraph precision layering | No "imperfection" constraints at all |
| Prohibition of Uniform Stacking | Clear directives prohibiting full-dimension sensory stacking, equal-length paragraphs, and sets of rhetoric | No related prohibitions |
| Temperature Balance | The prompt does not require extreme fragmentation (no hard metrics like "single-sentence paragraph ratio no less than X%") | Prompt requires single-sentence paragraphs ≥ 25% or "forced hard cuts" |

**Judgment Standard:**
- All 6 items PASS → **PASS (Anti-AI design complete)**
- 4-5 items PASS → **WARN (Suggest supplementing missing items)**
- ≤ 3 items PASS → **FAIL (Severe Anti-AI defect)**


### Dimension G: Conflict Resolution Execution Check

**Purpose:** Ensure that conflict resolution strategies are pre-built into the prompt, rather than leaving the resolution responsibility to the writer. This dimension is the core validation for "optimization inline"—the writer should only execute, not make priority decisions.

**Steps:**
1. Check if the prompt's task instructions include a word count compression strategy
2. Check if the prompt's writing methodology·sensory layering trade-offs include a sound exception explanation
3. Check if the prompt's constraint red lines have marked the highest priority (explicitly stating "red line content cannot be compressed")
4. Check if there is any content in the prompt's writing norms that contradicts the above conflict resolution (especially old expressions like "narrative rule priority is higher than all constraints")

**Checklist:**

| Check Item | PASS Standard | FAIL Signal |
|--------|-----------|----------|
| Word count compression strategy injected | The target word count field in task instructions contains elasticity notes (±10% acceptable) and compression strategies ("when exceeding word count, prioritize compressing low weight scenes, do not delete red lines" or similar expressions) | Target word count field has no elasticity notes or compression strategy, leaving the word count control responsibility to the writer |
| Sensory exception injected | The writing methodology·sensory layering trade-offs have a sound exception rule (crucial sound clues do not count towards the 2 sensory types) | No sound exception, or sound exception conditions are incomplete (missing "red line key info" and "brief description" conditions) |
| Red line priority confirmed | The constraint red line section explicitly declares "red line content cannot be compressed/cannot be deleted" | Red lines only list content, without declaring their highest priority status |
| No internal contradiction | Narrative rules in writing norms specify "priority is lower than red lines/word count/T1 words/cognitive verbs/sensory rules" or similar expressions | Still contains old expressions like "narrative rule priority is higher than all constraints", contradicting the hierarchy table |

**Judgment Standard:**
- All 4 items PASS → **PASS (Conflict resolution inline complete)**
- 3 items PASS → **WARN (Suggest supplementing missing items, but deliverable)**
- ≤ 2 items PASS → **FAIL (Severe defect in conflict resolution inline, must be corrected)**


## Audit Judgment General Rules

| Condition | Conclusion |
|------|------|
| Any FAIL in Dimensions B/E/F/G | **Audit Failed** → prompt-crafter must return to THINK and correct |
| Only Dimension A/C/D has any FAIL, others all PASS | **Audit Passed** → Technique coverage/executability/transformation completeness are not hard stops; incomplete is allowed |
| No FAIL, WARN ≥ 4 items | **Audit Failed** → Suggest correction |
| No FAIL, WARN ≤ 3 items | **Audit Passed** → Deliverable |

**FAIL Scenarios (Must return for correction):**
- Missing hard worldview/character design/plot red line constraints (Dimensions B/E)
- Meta leakage exists (Dimension E)
- No primary/secondary writing rules, severe anti-AI design defects (Dimension F)

**PASS Scenarios (Allowed to pass even if incomplete):**
- Incomplete techniques (some scenes lack guidelines in Dimension A) → Allowed
- Some scenes are brief, descriptions asymmetrical (partially abstract in Dimension C) → Allowed
- Some methodologies incompletely transformed (partially incomplete in Dimension D) → Allowed
- **Core Principle: As long as hard constraints are complete, anti-AI rules exist, and there is no meta leakage, it is deliverable.**

When the audit fails, explain in the progression:
- Which dimensions had what problems
- Evidence for each problem (original text citation + knowledge base cross-reference)
- Suggested direction for correction (do not specify exactly how to write, just point out where the problem is)
