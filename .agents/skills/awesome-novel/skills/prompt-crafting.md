# prompt-crafter skill


## Process Overview

```text
Step 1: Read input sources (5 types of files)
Step 2: Populate according to 6-element Prompt structure: Character / Task Instructions / Background Info / Examples / Input / Output
Step 3: Conflict detection
Step 4: Acceptance self-check
```

## Step 1: Read Input Sources

1. **writing-style.md** → Extract four fields (core_principles, possible_mistakes, depiction_techniques) + genre + model
2. **volume.md** → Extract the previous chapter's title, ending scene, and emotional endpoint
3. **chapter.md** → Extract memo, emotional_design, outline (scene breakdown), payoff_plan
4. **Character setting files** → Read involved characters, extract relevant status for this chapter
5. **genre-example** → Get the prompt injection segment for the corresponding genre (used for Output·Writing Norms)
6. **Anti-AI rule files** (`anti-ai/common-rules.md` + `anti-ai/{genre}.md`) → Inject fatigue word thresholds, sentence rules, meta-narrative prohibitions, genre anti-AI, etc. into Output·Writing Norms
**Note: For preceding context, only read the summary of the previous chapter in the volume outline, do not read the full text of the previous chapter.**

## Step 1.5: Global Conflict Resolution Pre-load

Before starting to populate the 6 elements, load the following global conflict resolution table first. During all subsequent population steps, when encountering rule conflicts (e.g., word count deficit vs. sensory refinement, keeping red lines vs. word count compression), resolve them according to this priority. This step is for internal reference and is not outputted to the prompt text.

### Priority Hierarchy (from high to low)

| Priority | Rule | Description | Execution Requirements |
|--------|------|------|---------|
| 1 (Highest) | Constraint Red Lines | Plot red lines, character taboo zones, boundary prohibitions | Never delete or alter red line content at any time. Word count compression cannot result in the loss of red lines. Sensory exceptions are limited only to sound clues that serve as key red line information |
| 2 | Word Count | Target word count hard constraint (e.g., 2000-2500 words) | Enable compression strategy when exceeded — prioritize compressing low weight scenes (Scene 1/2 transitions / medium weight paragraphs), recommend compressing to within 100 words (keep 1 sentence of environment switch + 1 sentence of character action + 1 sentence of emotion/atmosphere blank); compression must not touch red lines. Core conflict paragraphs in high weight scenes remain untouched |
| 3 | T1 Words (Modifiers) | Suddenly, surprisingly, silently, slightly, etc. | When threshold is exceeded, prioritize keeping those with narrative function, delete useless fluff words. Be lenient when red line paragraphs appear (keep rhythm functions) |
| 4 | Cognitive Verbs | Realize, discover, understand, feel, think, believe | Must be replaced with external actions or direct sensory descriptions, do not yield due to other rules |
| 5 | Sensory / X-ed for a moment | Max 2 sensory refinements per scene; "X-ed for a moment" structure | Sound clues can be uncounted if they are key red line information. Trim other senses when threshold is exceeded. "X-ed for a moment" can be kept if it has a rhythm function, delete redundant ones |
| 6 (Lowest) | Writing Norms | All other writing norms and technique guidelines (including narrative rules) | Automatically yield when conflicting with items 1-5 above. The original "narrative rule priority is higher than all constraints" is corrected to "narrative rule priority is higher than same-level rules in writing norms, lower than levels 1-5" |

### Word Count Compression Details (Priority 2 Execution Rules)

```
1. Absolutely do not delete: Plot red line content (2-4 items), character key turning points, emotional inflection paragraphs
2. Priority compression: Low weight scenes (prioritize the transition paragraph of the 1st low weight scene), non-core paragraphs of medium weight scenes
3. Secondary priority compression: Secondary elements of high weight scenes (shrink environment descriptions from 3 sentences to 1, streamline bystander descriptions)
4. Do not touch: Core conflict paragraphs of high weight scenes
```

### Sensory Conflict Details (Priority 5 Execution Rules)

```
1. Limit to 2 types of sensory refinement per scene, briefly sketch the remaining senses
2. Exception: Sound clues (character's auditory judgments, key environmental sounds) can be kept as brief sketches if they are key information of this chapter's plot red lines, without counting towards the 2 sensory quota
3. Exception trigger condition: The sound clue must be part of the red line content (e.g., gunshot = key combat signal, approaching footsteps = danger sign), rather than environmental decorative sound
4. When the "X-ed for a moment" structure in the scene exceeds the threshold, keep the ones with rhythm functions (e.g., "paused for a moment" provides a sense of pause), delete redundant items
```

### Ambiguity Condition Coverage (Boundary Examples for Priority 5)

The following examples help determine whether a rule is triggered:

| Rule | Applicable When Condition Met | Inapplicable When Condition Not Met |
|------|-----------------|-------------------|
| Word Count Compression·Low Weight Scene Compression | Scene weight is marked as "low", and content doesn't belong to red line key info | Scene weight is "low" but carries red line key info (e.g., a key foreshadowing appears in a low weight scene) |
| Sensory Exception·Sound Clues | Sound is red line key info and presented as a brief sketch (e.g., "The door opened") | Sound is a long environmental description (200 words of wind/footstep details) or non-red line info |
| Cognitive Verb Replacement | Cognitive verbs can be replaced with external actions or direct perception ("He felt scared" → "His fingers dug hard into his palm") | The cognitive verb carries irreplaceable plot info, and replacing it causes the word count to be exceeded, squeezing out red line content |

## Step 2: Populate according to 6-element Prompt structure

Population order: Character → Task Instructions → Background Info (Preceding Context + Character Initial State) → Input (Scene Raw Materials + Scene Type Identification) → Scene Methodology Loading and Transformation → Examples → Output (Constraint Red Lines + Writing Norms + Texture Requirements)

### Character

Extract the genre from genre-setting.md, copy directly without expanding.

### Task Instructions

Extract from chapter.md memo + volume.md + writing-style:

- Chapter number, role in the whole volume, this chapter's driving force, information release rhythm
  **Word count and compression strategy (Independent section, injected under the "Word Count and Compression Strategy" subsection of the Task Instructions paragraph):** Format see below.
- **Narrative Goals** (Merged original Goals):
  - Core suspense (in question format, not a plot summary) + suspense state (resolved/deepened/transferred)
  - Reader emotion (exit feeling, an emotion word or image)
  - Satisfaction point design (Type + Buildup scene + Release scene)

### Background Info

#### Preceding Context

Extract from volume.md previous chapter summary (only read the summary, not the full text):
- Previous chapter ending scene, emotional residue, reader information gap

When the previous chapter is ch-1: Fix as "No preceding chapters, opening cuts directly to character's current action", prohibit long world background introductions.

#### Character Initial State

Extract from chapter.md character state + character files:
- Each character's starting point → turning point → ending point + micro habits

### Input

#### Scene Raw Materials

Extract from chapter.md **scene cards** + information gap relationships:
- Split scenes according to boundary signals (location change/time skip/emotional turn) (2-4 scenes), corresponding one-to-one with the chapter outline scene cards
- Fill in four fields for each scene: Core event / **Information Gap** / Emotional turning point + Scene weight

**Core Event Population:**
- Input source: The **three elements of the scene card** from the chapter outline (What the protagonist wants to do / What is stopping them / What suspense exists), do not extract from outline.key_points
- Format: External event chain, connected by arrows ("Action A → Action B → Action C"), do not write as a narrative paragraph
- Prohibited: Character inner thoughts, cognitive verbs (realize/discover/understand), emotional conclusions

The information gap is taken from the corresponding relationship for this scene in the "Information Gap Relationship" of the chapter outline's knowledge_state.

**Scene Type Identification:** Analyze the core event of each scene and identify the scene type (Optional values: dialogue / fight / environment / inner-mono / transition / group-scene). Record as a list to be used in the next step for loading scene methodologies and selecting narrative rules.

#### Narrative Rule Selection

Based on the identified list of scene types, select the injected narrative rules according to the following mapping. When rules are written into the "Writing Norms" section of the prompt, they need to be accompanied by character setting associations—deduce behavioral features from specific fields of the character setting files and inject them, not just write the character name.

**Use the character setting data already read in Step 1** (Step 1.4 already read the character files involved in this chapter), and extract the fields used for rule association deduction from it:

| Required Field | Source | Purpose |
|---------|------|------|
| Basic Info·Language Features | Catchphrases, profanity, speaking habits | Rule 6 Dialogue Style |
| Layer 2 Self-positioning | How the character views themselves | Rule 3 Narrative sorting, Rule 6 Expression posture |
| Layer 3 Values | What cannot be compromised | Rule 3 Perception priority, Rule 6 Dialogue at critical moments |
| Layer 4 Capability | Combat experience, perception precision | Rule 1 Perception signals, Rule 3 Combat perception |
| Layer 5 Skills | What they are good at, what they know | Rule 1 Perception type, Rule 4 Scope of examples |
| Layer 6 Environment | Origin, growth experience | Rule 1 Perception habits, Rule 4 Life experience, Rule 6 Register |

**Character Association Deduction Table (Agent deduces specific behaviors from field values according to this):**

| Rule | Read Field | Deduction Logic | Inject into Rule Description |
|------|---------|---------|---------------|
| Rule 1 (Perception signals first) | Layer 6 + Layer 4 + Layer 5 | Origin determines perception familiarity (Hunter→Animal sounds/footprints, Docker→Human voices/ship sounds), capability determines precision, skill determines type | "This chapter's POV character {Name}'s origin is {Layer 6 keyword}, good at {Layer 5 skill}, the perception signal they notice first is {deduced type}" |
| Rule 2 (No cognitive verbs) | Layer 6 + Layer 5 + Layer 4 + Layer 1/2/3 (Aux) | Perceptions within the character's experience scope are written directly, those outside the scope do not appear in the narrative. Blind spots are deduced from the most informative layer among the 6 cognitive layers—Layer 1 (Unknown world rules), Layer 2 (Knowledge inconsistent with positioning), Layer 3 (Unrecognized behavioral logic), Layer 4 (Techniques beyond capability), Layer 5 (Unmastered skill domains), Layer 6 (Uncontacted environmental rules) | "Delete 'he felt', start directly from {Character Name}'s experience—they are good at {skill} so will notice {related perception}, but don't understand {blind spot} (deduced from Cognitive Layer {No.})" |
| Rule 3 (Sort by perception intensity) | Layer 3 + Layer 2 + Layer 4 | Layer 3 determines info priority (Brother's safety > Mission), Layer 2 determines narrative posture, Layer 4 determines amount of details capturable | "Narrative sorting is based on what is most important in {Character Name}'s heart: Layer 3 Values {item} dictates {item A} precedes {item B}" |
| Rule 4 (Use specific experiences) | Layer 6 + Layer 5 + Language features + Layer 1 | Examples are drawn from character's personal experience, language features determine word choice, worldview determines description scale | "Examples come from {Layer 6 Origin}, language has the flavor of {Language features}, do not use metaphors the character hasn't encountered" |
| Rule 6 (Dialogue fits character) | Language features + Layer 6 + Layer 2 + Layer 3 | Language features determine catchphrases/profanity, Layer 6 determines register, Layer 2 determines expression posture, Layer 3 determines dialogue at critical moments | "{Character A}'s language feature {catchphrase}, origin {Layer 6} habits {deduced register}, it's not like a person talking, it's like THIS person talking" |

**Universal Rules — Web Novel Style Baseline (Must inject for any chapter):**
The following 3 rules are the web novel universal baseline, applicable to all genres. The special writing style requirements of genre example files (genre-example) are superimposed on this baseline, not replacing it—i.e., the genre injection segment can add POV/rhythm/atmosphere requirements, but cannot overwrite the "warmth" positioning of the baseline.
- Rule 2 — Restrained use of cognitive verbs. Prioritize substituting with actions or direct perception ("He felt scared" → "His fingers dug hard into his palm"), but can be used briefly at key emotional nodes (≤2 times/chapter)
- Rule 5 — Natural presentation of causality. Delete redundant explanatory conjunctions (because/so/therefore), but keep necessary causal transitions—only write when the causal relationship needs to be explicitly given to the reader
- Rule 7 — Narrative is naturally warm. Allow a moderate sense of narrative presence—natural atmosphere buildup, psychological summaries, and sensory descriptions are fine as long as they are not stacked. Prohibit extreme plain sketching that loses reading warmth just for the sake of "showing, not telling"

**Appended by Scene Type:**
| Scene Type | Appended Rules | Character Association Focus |
|---------|---------|-------------|
| dialogue | Rule 6 | Language features → Catchphrases/profanity; Layer 6 → Register; Layer 2 → Expression posture; Layer 3 → Dialogue at critical moments |
| environment | Rule 1 + Rule 4 | Layer 6 → Perception priority classification; Layer 5 → Source of examples; Layer 1 → Description scale |
| inner-mono | Strengthen Rule 2 | Layer 3 → Current conflict trigger point; Layer 6 + Layer 2 → Character's likely physical reactions at the moment |
| fight | Rule 3 | Layer 4 → Combat experience level; Layer 3 → Priority judgments in combat; Layer 2 → Attack/Defense/Roaming style |
| transition | Rule 4 + Rule 7 | Layer 6 → What they might zone out about at this moment; Layer 2 → How they zone out |
| group-scene | Rule 6 | Language features of multiple characters + Layer 6 + Layer 2 (Deduced independently for each character) |

Requirements for examples of each rule:
- Positive and negative examples should be written with a real person's feel, "talking with impurities", not purified "standard human speech"
- Allow vague generalizations ("Anyway, it's just like that", "Noisy as hell")
- Allow filler words and half-finished sentences
- Allow uneven information density—not every sentence has to advance the plot

**Output Example** (The following is the complete style injected into the Narrative Rules segment of the Writing Norms):

```
Universal Rules (Must Inject):
· Rule 2 — Don't use cognitive verbs. ❌ "He found himself walking into a noisy tavern"
  ✅ "The moment the door opened, it was noisy as hell. People drinking, playing finger-guessing games. Anyway, that kind of place."
  Character Association: Lu Zheng's background as an ex-cop (Layer 6) and reconnaissance experience (Layer 5) makes him notice people first,
  not the alcohol—sweeping the seating layout, where the back door is, if anyone is watching him.
· Rule 5 — Only state action results. ❌ "He accidentally brushed against the person next to him"
  ✅ "The aisle was just that narrow; two steps in and he brushed against three people."
· Rule 7 — Narrative is naturally warm.
  ❌ "He squinted at the doorway, taking a long while to figure out the general idea" → Over-characterized, dragging rhythm
  ✓ "The doorway was packed. He stood there for a good while, feeling a bit irritated—nothing but heads, couldn't see what was going on inside at all." → Warm version
  ✅ "He stood at the doorway and looked for a good while. All people, so crowded there was no place to stand." → Minimalist version, suitable for fast-paced segments
  Note: Both versions can be used, choose according to paragraph rhythm. Do not use the minimalist version for the entire text.

Scene Type Appendices (dialogue + environment):
· Rule 6 — Dialogue fits the character. Lu Zheng speaks directly, in short questions (Language feature·Ex-cop);
  Fang Yan avoids conflicts, when nervous he lowers his head to drink tea and doesn't reply (Layer 3·Doesn't want trouble).
· Rule 1 — Perception signals first. When Lu Zheng enters a door, he sweeps the people first (Layer 5·Reconnaissance habit),
  not looking at the decor/menu first.
· Rule 4 — Use specific experiences. Examples are drawn from his cop career (Layer 6), don't use metaphors the character hasn't seen.
```

→ Inject into **Output·Writing Norms**

**Scene Focus + Weight Annotation (Written at the end of each scene):**
```
This scene's focus: Core conflict / Character emotion / Information gap (Choose one of three)
This scene's weight: High / Medium / Low
```
The focus replaces the general description of "Focus Locking" in the writing methodology—each scene declares its own focus type, so the writer doesn't need to look back across paragraphs. High weight scenes' focus must be applied simultaneously with "Sensory layering trade-offs".
- High Weight = Refine actions, micro-expressions, psychology; occupies over 70% of the chapter's ink
- Medium Weight = Normal narrative, not deliberately simplified nor extravagant
- Low Weight = Fast transition, briefly sketched over, no extra details added
- Every chapter must have 1 high weight scene, and at least 1 low weight scene
- The first scene is forcefully marked as high weight (the first scene of vol-x-ch-1 must cut directly into action, prohibiting long worldview narrations)

#### Paragraph Breakdown (Write paragraph structure guidelines)

Extract paragraph structure guidelines from chapter.md **outline.key_points**. Do not rewrite the narrative—the core event already states "what happens", the paragraph breakdown only answers "how to write this paragraph".

**Breakdown Rules:**
- Correspond one-to-one according to the division of key_points, each key_point corresponds to a paragraph
- Write structural guidelines (not narrative descriptions) for each paragraph, including:
  - Paragraph function (Advance / Transition / Build suspense / Dialogue)
  - Rhythm emphasis (Short sentences fast / Medium / Soothing)
  - Camera cue (Close-up / Medium shot / Long shot)
  - Notes ("No inner thoughts", "Purely external actions" and other constraints)
- Can add 1 sentence of tip on "how to distinguish the rhythm of this paragraph from others"

**Format (Written at the end of Input·Scene Raw Materials):**

```
¶1 [Function] | [Rhythm Emphasis] | Notes
¶2 [Function] | [Rhythm Emphasis] | Notes
...
```

Each ¶ corresponds to a key_point. This list makes the writer's paragraph structure and rhythm dictated by the key_points, **different for every chapter**.

#### Writing Methodology

```
Before writing, execute in the following order:
1. Focus Locking: For each scene, identify its unique "Core Conflict" or "Character Emotion" or "Information Gap" (choose one of three, no overlap). 70% of the full text's ink should be concentrated on this focus object; secondary environments, bystanders, and backgrounds should all be briefly sketched. Prohibit characterizing all elements with equal length. High weight scenes are refined down to the action and perception levels, and must simultaneously apply the "Sensory Layering Trade-offs" rule (see next item); low weight scenes allow fast transitions, but must not violate the focus locking or sensory layering trade-off rules.
2. Sensory Layering Trade-offs: Refine primarily using 2 types of senses, increase to 3 if atmosphere allows, briefly sketch the remaining senses. Avoid full-dimension uniform stacking. **Sound Exception: Only when sound clues are explicitly marked as "narrative line extension" (e.g., the pause and continuation of medicine grinding sound, the interval of dew drops falling), can they serve as narrative line extensions without counting towards the sensory quota; other sound descriptions must still adhere to the 2-3 sensory upper limit. Exception is limited to red line key information, excluding environmental decorative sounds.**
3. Narrative Rhythm Alternation: A segment of dense action/dialogue + a short psychological blank segment + a light environmental transition sentence loop. Prohibit continuous long descriptions, continuous dialogue, and equal-length paragraphs throughout.
4. Information Release Control: Key foreshadowing and character secrets are broken into fragments and brought out, not fully explained all at once. Allow blanks, do not write out all settings and psychology completely.
```

→ Inject into **Task Instructions** (Writing Methodology segment)

#### Scene Methodology Loading (Scene Type → Examples + Output·Writing Norms)

Dynamically load the corresponding scene writing methodology based on the list of scene types identified in the Scene Raw Materials. After the loaded methodology goes through the four-step transformation: **short examples are written into Examples, detailed guidelines are written into Output·Writing Norms**.

**Core Principle: Extract sparsely, prohibit full coverage.** Select only 1-2 techniques for each scene type to write into the prompt, the rest do not appear. Strictly prohibit applying everything completely.

**Loading Mechanism:**

1. **Deduplicate** from the scene type list to get the non-repeating scene types involved in this chapter (e.g., ["dialogue", "environment"])
2. For each type, read in the following order:
   a. Universal methodology: `.claude/knowledge/scene-craft/{type}/universal.md` (must exist)
   b. Genre specialization: `.claude/knowledge/scene-craft/{type}/{current_genre}.md` (read if exists, otherwise skip)
3. Universal methodology + Genre specialization merged as the methodology source for this scene type
4. **Filter by tags**: Read the `## Tag Selection Guide` section of `.claude/knowledge/scene-craft/{type}/universal.md`, match appropriate tags based on the scene's core event. Randomly extract 1-2 tag-matching methodologies from the merged methodologies. If a scene matches no tags, randomly extract 1-2 most universal ones from the universal methodology
5. **Special Check 1: Character Appearance Description** — Additionally check if each scene's core event triggers appearance description conditions (new character debut / character appearance change / scene involves appearance perception). If triggered, load `.claude/knowledge/scene-craft/appearance/universal.md` + `.claude/knowledge/scene-craft/appearance/{genre}.md` (if exists), append to Output·Writing Norms after four-step transformation
6. **Special Check 2: Psychological Activity/Inner Monologue** — Additionally check if each scene's core event + emotional design + key choices trigger psychological description conditions (character encounters major event / violent emotional fluctuation / faces major choice / face-slapping reversal / inner conflict). If triggered, load `.claude/knowledge/scene-craft/inner-mono/universal.md` + `.claude/knowledge/scene-craft/inner-mono/{genre}.md` (if exists), append to Output·Writing Norms after four-step transformation
7. **Special Check 3: Death/Sacrifice Scenes** — Additionally check if each scene's core event involves a character's death/sacrifice/going offline (keywords: KIA, fallen, sacrificed, killed). If triggered, load `.claude/knowledge/scene-craft/death-scene/universal.md`, append to Output·Writing Norms after four-step transformation
8. **Always Load (Always load but inject sparsely)** — Regardless of what scenes this chapter contains, always load the following files, but only inject 1-2 concise guidelines for each, do not expand all techniques:
   - `.claude/knowledge/scene-craft/prose/universal.md` (always select 1 most relevant: Abstract concretization / Camera rhythm / Verbs replacing adjectives / Sensory overlay - choose one of four)
   - `.claude/knowledge/scene-craft/pov/universal.md` (always select 1 most relevant: POV boundary / Physical transition / Info gap dislocation - choose one of three)
9. If the directory of a certain type doesn't exist (like `group-scene/` `transition/` temporarily being empty frameworks), skip that type

**Context Filtering (Crucial Step):**

After reading the methodology, it cannot be directly copied to the Output·Writing Norms. It must undergo the **Four-Step Transformation Method** to turn the universal methodology into specific scene writing guidelines.

### Four-Step Transformation Method: Universal Methodology → Scene Writing

Taking the "Action Interspersion" methodology in `dialogue/universal.md` as an example to demonstrate the four-step transformation process:

```
Methodology original text received:
  "Dialogue requires action interspersion—pure dialogue over 6-8 sentences, readers lose the sense of the scene."

Read from character settings (characters involved in the current chapter):
  Fang Yan: Cautious personality, avoids conflict, lowers head to drink tea/looks away when nervous
  Lu Zheng: Ex-cop, direct, leans forward/taps fingers on table when pressing questions

Read from information gap:
  Fang Yan knows the inside story of the suppressed case from three years ago ↦ Lu Zheng doesn't know

This chapter's emotional rhythm:
  Tense segment (Lu Zheng is pressing questions)
```

#### Step 1: Anchor the Character

Do not apply the methodology to "Character A" or "Character B", apply it to specific people.

> **Ask: What is this character's personality? What is their state in this scene?**

- A cautious person's way of avoiding vs. A direct person's way of pressing questions
- An arrogant person's way of being impatient vs. An inferior person's way of dodging
- The same scene, same methodology, written out completely differently when swapped to another character

#### Step 2: Anchor the Information Gap

Determine the **information asymmetry** between characters—this decides the direction and manner of "not telling the truth".

> **Ask: Who is hiding it? Hiding what? Why hide it?**

| Info Gap Type | Impact on Dialogue Writing |
|-----------|----------------|
| Fang Yan knows ↦ Lu Zheng doesn't | Fang Yan is avoiding, defending; Lu Zheng is probing, approaching |
| Lu Zheng knows ↦ Fang Yan doesn't | Lu Zheng is guiding, laying out; Fang Yan is passively responding |
| Reader knows ↦ Character doesn't | Reader anxiously waits for character to expose it, plant "audience can see but character can't" foreshadowing in dialogue |

#### Step 3: Anchor Emotion and Rhythm

The specific writing of the methodology needs to match this chapter's emotional direction.

> **Ask: Is the emotion of this scene tense, soothing, or turning?**

| Emotional Segment | Dialogue Rhythm | Action Interspersion Frequency | Sentence Length |
|--------|---------|-------------|---------|
| Tense / Confrontation | Fast, interrupting each other | Low (actions interfere with tension) | Short sentence fragments |
| Suppressive / Concealing | Slow, long pauses | High (using actions instead of answers) | Sentences cut halfway |
| Soothing / Reconciliation | Natural, with pauses | Medium (intermittent actions) | Complete sentences |

#### Step 4: Fusion Output

Combine the conclusions of the previous three steps into a **scene writing guideline that can be directly written into the Output·Writing Norms**.

```
❌ Incorrect Example (Universal methodology, no transformation):
  "Dialogue must have action interspersion, insert an action every 6-8 sentences."
  → The dialogues in all chapters are written according to one template, with no character distinction.

✅ Correct Example (After four-step transformation):
  "The information gap of this dialogue is that Fang Yan knows the inside story ↦ Lu Zheng doesn't.
  Fang Yan is cautious and lowers his head to drink tea when avoiding—Lu Zheng asks a sentence, he takes a sip, doesn't reply.
  Lu Zheng's ex-cop way of pressing questions is leaning forward, closing the distance—moving forward a bit with every question.
  The emotion is a tense segment, dialogue rhythm should be fast—Lu Zheng presses with short sentences, Fang Yan avoids with actions, no long sentences to explain."
  → The methodology lands on a specific character, specific information gap, specific emotion, each character is written differently.
```

**Four-Step Transformation Method Flowchart:**

```
scene-craft Methodology
     ↓
Step 1: Anchor Character (Extract personality, micro-habits from character settings)
     ↓
Step 2: Anchor Info Gap (Extract who is hiding from whom from knowledge_state)
     ↓
Step 3: Anchor Emotion Rhythm (Extract current emotional segment from emotional_design)
     ↓
Step 4: Fusion Output → Write into Output·Writing Norms
```

**Bottom Line:**
- ❌ Rote application of each methodology, dialogue writing for all characters is the same
- ✅ Through Step 1-4 transformation, the same methodology behaves differently on different characters
- ❌ Copying the content of the scene-craft file whole-paragraph
- ✅ Randomly extract 1-2 most relevant items from the methodology, refine and output after four-step transformation
- ❌ Skip transformation and write general guidelines like "Character A says... Character B says..." directly
- ✅ The transformed guidelines allow the writer to directly write dialogue that fits the character's personality
- ✅ Sparse use of techniques, max 2 items per type, other techniques do not appear in the prompt

> If this chapter only has dialogue scenes, select 1-2 methodologies from `dialogue/universal.md` + `dialogue/{genre}.md` (if it exists), transform each through four steps and inject into Output·Writing Norms.
> If there are both dialogue and combat, select 1-2 methodologies (including genre specialization) from both scene directories respectively, transform them combining their respective scene's characters and conflicts, then inject.
> If a character corresponding to a certain methodology is not participating in the current scene, skip that item.

### Transformation Example Library (few-shot)

The following demonstrates the complete process of two methodologies from "Universal → Four-Step Transformation → Output·Writing Norms Injection". Your transformation output should achieve the same degree of specificity and context awareness.

#### Example 1: Dialogue Scene · Fang Yan and Lu Zheng (Suspense · Concealment and Probing)

```
[scene-craft Methodology Original Text]
"Characters don't tell the truth. The tension of the dialogue is the gap between surface words and deep meaning."

[Current Context]
Characters:
  Fang Yan: Police insider, knows the inside story of the suppressed case from 3 years ago, cautious personality, avoids conflict
  Lu Zheng: Ex-cop, currently investigating the case, direct, sharp, leans forward when pressing questions
Information Gap:
  Fang Yan knows inside story ↦ Lu Zheng doesn't (Fang Yan is hiding, Lu Zheng is chasing)
Emotional Rhythm:
  Tense segment — Lu Zheng is pressuring, Fang Yan is defending

[Four-Step Transformation Process]
Step 1 Anchor Character:
  Fang Yan cautious → Avoidance method: lower head to drink tea, look out window, don't reply
  Lu Zheng direct → Pressing method: lean forward, close distance, stare at opponent
Step 2 Anchor Info Gap:
  Fang Yan knows but can't say → He is defending, uses "I don't know about this case" to push back
  Lu Zheng doesn't know but wants to confirm → He is probing, uses specific details to force a reaction
Step 3 Anchor Emotional Rhythm:
  Tense segment → Short sentences, fast rhythm, few action interspersions (but actions at critical moments should be magnified)
Step 4 Fusion Output:
  Fang Yan knows what happened 3 years ago but can't say (Info gap), Lu Zheng is pressing questions.
  Lu Zheng leans forward, pressing with short sentences; Fang Yan lowers head to drink tea, doesn't reply, uses avoidance actions instead of answering.
  Tense segment fast rhythm — no long sentence explanations, only alternation of pressing questions and silence.

[Final Injected Text]
"Information Gap: Fang Yan knows inside story ↦ Lu Zheng doesn't. Lu Zheng presses with short sentences, leans forward;
Fang Yan avoids, lowers head to drink tea, doesn't reply. Tense segment fast rhythm, no long sentence explanations,
only alternation of pressing questions and silence — Fang Yan's avoidance isn't silent speechlessness, it's every time Lu Zheng asks something crucial
he uses an action to change the subject (drink tea/look out window/ask back 'how far have you investigated')."
```

#### Example 2: Combat Scene · Ye Qiu and Wang Hu (Cultivation · Power Suppression)

```
[scene-craft Methodology Original Text]
"Result > Process. Readers care about who won and what the cost was, not the details of every move.
Visualize the cost — don't write 'the protagonist was injured', write the consequences of the wound."

[Current Context]
Characters:
  Ye Qiu: Qi Condensation Level 3, just broke through recently, unfamiliar with new power yet
  Wang Hu: Qi Condensation Level 2, thug boss, relies on cultivation to bully people
Information Gap:
  Ye Qiu just broke through ↦ Wang Hu doesn't know (Wang Hu thinks Ye Qiu is still at the cultivation level from their last meeting)
Emotional Rhythm:
  Satisfaction (Shuang) segment — readers expect Ye Qiu to show his strength

[Four-Step Transformation Process]
Step 1 Anchor Character:
  Ye Qiu: Just broke through, hasn't adapted to new power → Strikes will have a raw feel of "couldn't hold back"
  Wang Hu: Thinks the opponent is still weak → Underestimates at first, only gets serious after suffering a loss
Step 2 Anchor Info Gap:
  Wang Hu doesn't know Ye Qiu broke through → His underestimation is caused by the info gap, not intentional
  Ye Qiu knows he got stronger → But he also isn't sure exactly how strong, has a probing element
Step 3 Anchor Emotional Rhythm:
  Satisfaction segment → Rhythm fast first (Wang Hu's shock at being crushed), then slow (showing consequences)
Step 4 Fusion Output:
  Don't write the details of every move, focus on "Result → Wang Hu's reaction → Visualize cost".
  Write from Wang Hu's perspective — he hasn't reacted yet and is already flying out, only then seeing Ye Qiu's
  realm is different now.

[Final Injected Text]
"This combat is driven by info gap — Wang Hu doesn't know Ye Qiu has already broken through.
Don't write details of moves, use result + reaction instead: Wang Hu hasn't seen clearly what happened and is already flying out.
Visualize cost: Ye Qiu couldn't pull his punch, fist smashes through the wall, purlicue splits and bleeds.
Rhythm: Fast (Wang Hu flies out) → Slow (He looks up at Ye Qiu, realizes the realm changed)
→ Lingering charm (Ye Qiu looks at his own hand, not quite sure how strong he is now)."
```

### Examples

Fill in from the scene methodologies after four-step transformation. Select 1-2 methodologies for each scene type, transform them through four steps, and write them in as examples. Examples are demonstrations of "how specific characters are written in specific scenes", not a copy of universal methodologies.

**Example Annotation Requirements:** At the end of each example, use parentheses to annotate the name of the methodology it demonstrates, like (Demonstration: Sensory Layering Trade-offs·Tactile+Auditory). Annotations help the writer understand the correspondence between the example and the writing methodology, enhancing the transferability of the methodology. When the same methodology has multiple examples, only annotate the first one.

### Output

Assemble into a **flat structure**, merge writing norms, narrative rules, and constraint red lines into a single "Inviolable Rules" list to reduce nesting and priority judgment costs. **Scene writing guidelines** (loaded from scene methodologies and four-step transformed) and **Texture requirements** are kept as independent sub-sections.

#### Inviolable Rules

**Priority (from high to low):**
1. **Constraint Red Lines** — Plot red lines, character taboo zones, boundary prohibitions (When red lines conflict with narrative rules, prioritize satisfying red lines)
2. **Word Count** — Target word count hard constraint (enable compression strategy when exceeded)
3. **T1 Words / Cognitive Verbs / Senses** — Modifier thresholds, cognitive verb restraint, sensory layering
4. **Narrative Rules** (Higher than other rules at the same level in Writing Norms)
5. **Other rules at the same level in Writing Norms**

**Red Line Constraints:**
- Conflict ladder, plot red lines (2-4 items), boundary prohibitions, character taboo zones — extracted from chapter.md memo, filled in completely, no placeholders

**Web Novel Style Baseline (Applicable to any chapter):**
- Restrained use of cognitive verbs (≤2 times/chapter); natural presentation of causality; narrative is naturally warm; linear narrative throughout; reject perfect narrative; prohibit deliberate stacking of techniques; prohibit poetic tone

**Style Suggestions (Can deviate):**
- POV Strategy: Read from chapter.md outline.pov, inject POV character + narrator's limitations.
- Description: Use actions and perceptions to drive the narrative, senses serve the atmosphere.
- Rhythm: Paragraphs rise and fall naturally, sentence lengths alternate. The above can be moderately deviated from if serving emotions or plot.

The prompt injection segment of the genre example file (genre-example) is superimposed on the web novel style baseline, not replacing it.
- **Specific Narrative Rules**: {Select corresponding rules from the "Narrative Rule Selection" section based on this chapter's scene types, each rule is associated with character settings before injection}

**Deduplication check after assembly:** The same semantic meaning only appears once in the output.

#### Scene Writing Guidelines

(Selected from the results of loading scene methodologies and four-step transformation): This chapter involves {N} scene types ({list of types}. Optional values: dialogue / fight / environment / inner-mono / transition / group-scene). For each type, inject 1-2 specific writing guidelines.

#### Texture Requirements

Extract from chapter.md memo + Author additions. **Append "Imperfection" constraints:**
- Useless details: 1-2 fragmented slice-of-life details that don't serve the main plot
- Dialogue rhythm: Allow half-finished sentences, filler words, pauses, repetitions
- Paragraph precision layering (allocate ink by weight): High weight scenes (at least 3 action/perception level details for core conflict/emotion/info gap expansion), Medium weight scenes (normal narrative rhythm, character interaction drives plot), Low weight scenes (optional transition template reference — Note: the following is a reference structure, can be fine-tuned based on context. 1 sentence of environment switch + 1 sentence of character action + 1 sentence of emotion/atmosphere blank, ≤ 100 words)

#### Appendix: Demonstration Paragraphs (Non-binding reference)

**Declaration:** The following demonstrations are just for style reference, the writer can choose whether to follow them or not, they must not replace any directives above. Filled in from example methodologies after four-step transformation, attached with methodology tags.

## Step 3: Conflict Detection

Check after filling:
- Preceding context emotional residue = Character initial state first character's starting point (Consistent)
- Character initial state ending point = Scene raw materials turning point (Consistent)
- Scene raw materials info gap is consistent with Task Instructions·This chapter's driving force (Suspense driven → every scene has an info gap)
- Constraint red line conflict ladder tier matches Task Instructions·This chapter's driving force (Threat driven → tier shouldn't be Entry level)
- Writing Norms are populated (non-empty)
- Texture Requirements field is populated (non-empty), containing "imperfection" constraints

After everything is filled, read through globally once — cover the field names, and see if each seed reads like a narrative image or a writing directive.

## Step 4: Acceptance Self-check

Check prompt.md item by item:

| Check Item | Standard |
|--------|------|
| Structural completeness | All 6 elements of Character/Task Instructions/Background Info/Examples/Input/Output are present |
| Field population completeness | Each element field has a value, no `______` placeholders left |
| writing-style injection | All four fields of role/core_principles/possible_mistakes/depiction_techniques are injected |
| Hard constraints complete | Character red lines, worldview taboo zones, plot red lines are all present |
| Preceding context imagery | Has a specific ending scene description (ch-1 marked as no previous text, cuts directly to action) |
| Scene weight annotation | High/medium/low weight is annotated at the end of each scene, with 1 high weight + at least 1 low weight |
| Writing Methodology | Focus locking/sensory layering/rhythm alternation/info control have been injected into Task Instructions |
| Examples populated | At least 1 scene writing example transformed through four steps |
| Technique sparseness | Only 1-2 technique guidelines injected per scene type, not fully covered (techniques are covered by anti-ai step) |
| POV Strategy | Read from outline.pov, inject POV character + narrator's limitations + info passing rules for supporting characters |
| Description/Rhythm/Sentence structure requirements | Description methods have values; Rhythm contains overall + emotional segment changes + natural fluctuation of paragraph lengths; Sentence structure alternates long and short, no 4 consecutive sentences of the same structure |
| Emotional requirements | Suggest at least one subtle emotional contrast, without breaking emotional continuity |
| Information requirements | Allow idle writing/digressions/dead clues, at least one paragraph with extremely low information density |
| Paragraph breakdown | Scene raw materials end with ¶ structural guidelines (at least 2 paragraphs, each with function/rhythm/notes) |
| Narrative rules injected | Writing Norms segment contains "Narrative Rules" sub-segment, 3 universal rules + scene appended rules are all populated, each rule has an associated character setting deduction |
| No source annotations | `Source:`, `(Source)` etc. source annotations do not appear anywhere in the prompt text, the writer doesn't need to know where the rules come from |
| Texture fields | Contains "imperfection" constraints (half-finished sentences / slice-of-life details / paragraph precision layering) |
| No meta leakage | Does not contain self-referential phrases like "the following is the main text of the novel" |
| No full-paragraph copying of chapter outline | Content of each layer is extracted and injected, not copied verbatim |
| Word count compression strategy injected | Target word count field in Task Instructions contains elasticity notes + compression strategy description ("±10% acceptable, narrative integrity first" / "exceeding word count prioritize compressing low weight scenes, do not delete red lines" or similar expressions) |
| Sensory exception injected | Writing methodology·sensory layering trade-offs contain sound exception rule (key sound clues do not count towards the 2 sensory types quota) |

**After passing all**, write to `prompts/vol-{N}-ch-{M}-prompt.md`.
If not passed, return to Step 2 to correct the corresponding element.
