# Writing Style Setting Guide

> Guide the author to determine the writing style - a four-field anchoring system. After selecting the genre, match the default values of the four fields from `knowledge/genre-example/`. The author can customize on this basis to override the defaults.

---

## Supreme Principle: The four fields anchor each other and are indispensable

The four fields anchor each other, and any change to one requires checking whether the other three are still consistent.

```
Narrative Identity (role) ────── Core Principles (core_principles)
      │                       │
      │   Mutually anchored   │
      │                       │
Common Mistakes (possible_mistakes) ── Descriptive Techniques (depiction_techniques)
```

**Irreversible design order:** First determine the narrative identity → then determine the core principles (what this narrative identity cannot do) → then determine the descriptive techniques (what techniques are most natural for this narrative identity) → finally determine the common mistakes (review known pitfalls).

**Adjustment rules:** Default values are only a starting point. At least one field must be discussed and adjusted - if all are the same as default, it means this style hasn't been deliberately chosen yet.

---

## Part One: Settings - Defining the four fields one by one

### 1. Narrative Identity (role): Determine the narrative identity

A one-sentence qualitative description, including the narrative distance (close to the character or overlooking the whole picture) and the narrative attitude (neutral / satirical / empathetic).

Just talk about this one sentence: **"What do you want readers to feel when reading? - Like someone sitting next to them telling the story calmly, like getting into the protagonist's head to feel everything, or like watching movie camera shots?"**

The author's answer directly determines the character positioning.

```
✅ Pass:
Narrative Identity: A limited-perspective narrator who stays close to the protagonist's inner self - the reader only knows what the protagonist knows and feels the emotions the protagonist feels.
→ A single sentence fixes the narrative distance (close to character) and the narrative attitude (empathetic but limited).

❌ Fail:
Narrative Identity: Write a good looking story.
→ Too vague. Any narrator could say this, it cannot distinguish a style.
```

**Judgment Criteria:** Does the narrative identity simultaneously answer "how far is the distance" and "what is the attitude"? If one is missing → send it back to add.

---

### 2. Core Principles (core_principles): Executable hard rules

Every principle must clearly state "what it looks like to violate it". 3-5 items, each must be verifiable.

Talk about this sentence: **"What are the absolute 'do-not's for this style? - Write whatever comes to mind, you don't have to think of it all at once, you can change it later."**

The items the author might list require processing:

| The Author Said | Written As |
|---------|--------|
| "Don't be too wordy" | No more than 4 sentences per paragraph, no more than 3 consecutive descriptive paragraphs |
| "Don't sound too AI-like" | Psychological activities do not start with "he felt" or "he realized" |
| "Faster pacing" | A Q&A exchange should not exceed three rounds, otherwise change the scene |

```
✅ Pass:
"Suddenly", "abruptly", "in an instant" should not exceed 3 times per chapter.
→ Verifiable - if it exceeds, it violates the rule.

❌ Fail:
Don't write with too much of an AI flavor.
→ Un-executable - what counts as "AI flavor"?
```

**Judgment Criteria:** Can each principle be written into an automated check? If not → change it to an operational description.

---

### 3. Common Mistakes (possible_mistakes): Specific anti-patterns

Each item corresponds to a specific way of writing (e.g., "Psychological activities do not start with 'he felt'"), rather than a general warning ("Don't write too AI-like").

```
✅ Pass:
Overuse of facial expression descriptions ("He frowned", "The corners of her mouth turned up")

❌ Fail:
Don't write like AI, be natural.
→ What is natural? Cannot be checked.
```

**Judgment Criteria:** If you show a common mistake to anyone, can they immediately judge whether they made it while writing? If not → not specific enough.

---

### 4. Descriptive Techniques (depiction_techniques): Provide methods

Each technique is operational ("picking fingernails when nervous"), not an abstract suggestion ("write more actions and fewer emotions"). Each technique has an implementation case.

```
✅ Pass:
The environment is filtered through the character's attention: when the character is anxious, what they see is not the full picture of the room, but exits, windows, and things that can serve as weapons.

❌ Fail:
Use more detail descriptions.
→ What details? In what scenarios? Unknown.
```

**Judgment Criteria:** After reading the technique, can one immediately use it to rewrite an existing piece of text? If not → not specific enough.

---

### Writing into the file

```yaml
role: <A single sentence, containing narrative distance and narrative attitude>

core_principles:
  - <Executable principle 1>
  - <Executable principle 2>
  - <Executable principle 3>

possible_mistakes:
  - <Specific anti-pattern 1>
  - <Specific anti-pattern 2>
  - <Specific anti-pattern 3>

depiction_techniques:
  - <Operational technique 1 (including case)>
  - <Operational technique 2 (including case)>
  - <Operational technique 3 (including case)>
```

---

## Part Two: Acceptance - Four-field self-check

### Judgment Criteria Quick Check

| Field | What Counts as Enough | What Counts as Not Enough | How to Fix |
|------|---------|-----------|---------|
| Narrative Identity | After reading, know "what identity am I writing as" | "Write a good story" | Ask "how far is the distance, what is the attitude" |
| Core Principles | Each item can be roughly checked for violation | "Don't write too AI-like" | Change to specific anti-patterns |
| Common Mistakes | Each item corresponds to specific writing | "Be natural" | Provide actual sentence comparisons |
| Descriptive Techniques | Can be used immediately to rewrite after reading | "Write more details" | Provide techniques under specific scenarios |

### Self-Check List

- [ ] All four fields have values?
- [ ] At least one field is different from the default value (the style has been actively chosen)?
- [ ] Narrative identity answers both narrative distance and narrative attitude simultaneously?
- [ ] Can each core principle be quantitatively checked?
- [ ] Is every common mistake a specific way of writing, not an abstract concept?
- [ ] Does every descriptive technique have an explanation of implementation methods?
- [ ] After modifying one field, have the other fields been self-checked for consistency?

---

## Cases

### Case 1: Calm Narrative (Xianxia / Fantasy)

```yaml
role: A calm but not indifferent narrator - objectively describes character actions and the environment, does not feel on behalf of the character.
  Let dialogue and action convey emotion, no psychological analysis voice-overs.

core_principles:
  - Every chapter must end with a hook, cannot end flatly.
  - Do not insert voice-overs in dialogue to explain character emotions - let the reader judge from tone and word choice.
  - Focus on dialogue, supplemented by description. After a short narrative, quickly return to dialogue to advance.
  - "Suddenly", "abruptly", "in an instant" no more than 3 times per chapter.
  - Do not write meaningless daily routines - every scene must have conflict or information advancement.

possible_mistakes:
  - Overuse of facial expression descriptions ("He frowned", "The corners of her mouth turned up")
  - Psychological activities starting with "He felt", "He realized", "He thought to himself"
  - Battle scenes written as slow-motion moves
  - Environmental descriptions standing alone in a paragraph disconnected from the plot
  - Starting paragraphs with "He/She" consecutively more than 3 times

depiction_techniques:
  - Emotions expressed through actions: picking fingernails when nervous, speaking softly instead when angry.
  - Environmental perception integrated into character action: not "the street is noisy", but "he had to raise his voice for the other person to hear".
  - Differentiate characters in dialogue through tone and word choice - do not rely on "he said", "she said" tags.
  - Passage of time hinted through environmental changes: angle of sunlight, length of shadows, temperature changes.
  - Battles use results and consequences instead of process: "By the time the noise stopped, his left arm was already hanging unnaturally by his side."
```

### Case 2: Close to Character's Inner Self (Urban Supernatural)

```yaml
role: A limited-perspective narrator who stays close to the protagonist's inner self - the reader only knows what the protagonist knows and feels the emotions the protagonist feels.
  Occasionally use inner monologue to close the distance, but do not provide an overall explanation from God's perspective.

core_principles:
  - Each chapter maintains the protagonist's inner perspective and does not cut into other characters' heads.
  - The inner monologue is in the character's own language style, not written language.
  - The dialogue is fast-paced—no more than three rounds of questions and answers, otherwise the scene will be changed.
  - There should be no more than 2 declarative sentences ending with past tense markers in each paragraph.
  - Use blank lines for scene transitions, no "at the same time" or "on the other hand" transitions.

possible_mistakes:
  - Write the inner monologue as "he thought", "he realized", "he suddenly discovered"
  - The narrator summarizes the character's emotions ("He felt sad")
  - Use "seems", "as if" and "as if" to weaken certainty
  - The description of the environment is written as a running account ("He walked into the room, and there were tables, chairs and windows in the room")
  - The inner drama of the character is too long, and the reader forgets where the scene is

depiction_techniques:
  - The inner monologue removes the guide tag and directly connects to the narrative flow: "This was not a good idea. But he opened the door anyway."
  - Emotions are expressed through physiological reactions: heartbeat, breathing, trembling hands, dry mouth - no need for the word "nervous".
  - The environment is filtered through the character's attention: when the character is anxious, what he sees is not the entire room, but exits, windows, and things that can be used as weapons.
  - Dialogue uses dislocation to create tension: the character's hands are shaking when he says "It's okay".
  - Time jump is expressed through body sense: "I don't know how long it has passed", "It feels like a lifetime has passed".
```