# False Positive Prevention — Do Not Modify List

> When executing the anti-ai pipeline, the following scenarios are **skipped for detection**. This prevents over-removing AI flavor, which could lead to character homogenization.

## I. Character-Specific Expressions Are Not Modified

### Catchphrases / Verbal Tics

- A character's fixed catchphrases, modal particles, and pausing habits are not modified.
- Example: Character A loves to add "that is to say," Character B loves to add "you know," Character C stutters when nervous.
- Regardless of whether they hit the forbidden word list, **character catchphrases do not trigger Gate A/B/C**.

### Character-Driven Grammatical Errors

- Grammatical errors caused by a character's educational level/personality are not modified.
- Example: An illiterate character saying "I dunno," a careless character saying "Not to tell you off but."
- Judgment standard: Whether the error fits the character setting in Layer 6 (Background) + Layer 2 (Self-Positioning).

### Specific Narrative Tones

- First-person sarcastic/complaining styles are not modified (mental activities having a lot of colloquial/extreme/inaccurate expressions).
- A character's abnormal expressions during extreme emotional states are not modified (laughing in extreme anger, silence in extreme sorrow, talkative in anxiety).
- Special sentence structures brought by the narrator's age/temperament are not modified (child's perspective/old man's memories/veteran's oral history).

## II. Dialogue Exceptions Are Not Modified

### Complete Sentence Structure Exemption

- Written language/complete sentence structures appearing in dialogue are not modified if they are intentional character traits.
- Example: A scholar character using complete sentences in dialogue—this is not AI flavor, it's character design.
- Judgment standard: Check against character setting Layer 5 (Skills) and Basic Info·Language Features.

### Dialogue Emotion Word Exemption

- Emotion words directly appearing in dialogue are not modified—people will say "I am very angry" or "I am very sad" in conversation.
- Change to tagging "emotion words inside dialogue" for quantitative statistics only, **do not trigger replacement**.

### Dialogue Tag Exemption

- Basic dialogue tags like "He said" or "She asked" are not modified because they are narrative infrastructure.
- Only process **overly modified** tags (the adverb parts in "he said angrily" or "she said softly").

## III. Specific Narrative Functions Are Not Modified

### Punctuation Function Exemption

| Scenario | Example | Handling |
|----------|---------|----------|
| Dialogue interrupted | "You—" His words were interrupted before finishing. | Keep the em dash |
| Character's voice drawn out | "Wait—a—min—ute—" | Keep |
| Omission in mental activities | "She remembered... no, maybe she remembered wrong." | Keep the ellipsis |
| Enumeration with commas | "On the desk lay a pen, paper, phone, keys." | Keep (slice-of-life enumeration) |
| Enumeration (AI template) | "This symbolizes courage, resilience, hope, and the future." | Tag (abstract concept enumeration) |

### Past Tense / Change of State Particle Function Exemption

| Scenario | Do Not Modify | Modify |
|----------|---------------|--------|
| Completed state in dialogue | "I got it," "Alright," "I'm leaving." | Do not modify (colloquial completed state) |
| Action completed | "He stood up." | Can modify → "He stood up" |
| Continuous completed tense | "He stood up, walked out, and closed the door." | Keep one completed state indicator |
| Change of state | "He began to understand." | Do not modify (habitual usage) |

### Parallel Structure Function Exemption

| Scenario | Do Not Modify | Modify |
|----------|---------------|--------|
| Character intentionally uses parallelism | Character has a need for speech/debate/emotional appeal | Keep, do not trigger automatic replacement |
| Stream of consciousness | Character uses parallelism at an emotional peak to strengthen tone | Keep, tag only |
| AI template parallelism | "Perhaps it is... perhaps it is... perhaps it is..." | Modify |
| Mechanical enumeration | "Firstly... Secondly... Lastly" | Modify |

## IV. Practical Writing Guidelines

### anti-ai agent Execution Rules

```
Hit detected → Is it in the boundary-cases list? → Yes → Skip (tag as SKIP)
                                                 → No → Process according to common-rules
```

- Before executing Phase 3 every time, read boundary-cases.md first.
- Every Gate hit must first undergo the "is it exempt" judgment, then decide whether to modify it.
- Hits tagged as SKIP are counted in quantitative metrics **but not modified**.

### When Uncertain

- Hits with ambiguity → Keep the original text, tag as `[Doubt: Suspected False Positive]` in the Phase 4 report.
- Needs author judgment → Put it in the report, do not modify.
