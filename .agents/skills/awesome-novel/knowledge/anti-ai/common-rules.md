# Anti-AI Common Rules — Three-Level Forbidden List

> Shared across all genres. The core reference for the item-by-item removal phase of anti-ai.

## Three-Tier Processing Logic

| Tier | Processing Method | Corresponding Original Words |
|------|-------------------|------------------------------|
| **T1 Replace Immediately** | Tag and replace immediately upon appearance, 0 tolerance | ★★★★★ Most toxic sentence structures + Level 1 forbidden words |
| **T2 Same-Paragraph Clustering** | Tag only if appearing 2+ times in the same paragraph; keep only the most appropriate one. Ignore single occurrences | Clustered versions of existing Level 2 words |
| **T3 Full-Text Density** | Process only if the full-text density is too high; no per-word threshold, based on paragraph/word count ratio | Basic words, structural skeleton words |

Judgment Priority: T1 > T2 > T3. If the same word appears in multiple tiers, handle it according to the highest tier.

---

## I. T1 Replace Immediately (Original ★★★★★ + Level 1 Forbidden Words)

Replace immediately upon appearance, 0 tolerance.

### Most Toxic Sentence Structures (★★★★★)

| Structure | Example | Handling |
|-----------|---------|----------|
| "Not A, (but) B" | "He wasn't angry, but disappointed." | Write B directly |
| "With a..." universal adverbial | "He looked out the window, with a trace of melancholy." | Break into short sentences or change to an action |
| "As if/like/just like..." | "The voice sounded as if it came from far away." | Delete or describe plainly |
| "Make/cause" structure | "This scene made him think of his childhood." | Delete "make", let the subject act directly |
| "When... time" | "When he opened the door" | Break into independent action sentences |
| Fronted cognitive verbs | "He realized/He felt/He noticed" | Delete, write the facts directly |
| "At the same time/Meanwhile" | "Meanwhile, in another place" | Change the scene opening directly |

---

### Level 1 Forbidden Words (Replace Immediately)

### Modal Category

| Word | Replacement Strategy |
|------|----------------------|
| as if, like, just like, similar to | Delete or describe plainly |
| a trace of, a touch of, a bit, somewhat | Use specific quantifiers or delete |
| couldn't help but, involuntarily | Write the action directly; keep those with a rhythmic function |
| suddenly, abruptly, fiercely | Moved to context-sensitive category (≤4 times/chapter); lenient on red-line paragraphs |

> **Note:** The purpose of the above rules is to reduce the uniformity of the AI tone, not to completely ban normal expression. Moderate use (≤2 times/paragraph, ≤3 times/chapter) does not constitute AI flavor—when a certain word has a rhythmic function in a paragraph, it can be kept.

### Action Category

| Word | Replacement Strategy |
|------|----------------------|
| pupils shrank, body stiffened | Replace with specific physical reactions |
| gasped a breath of cold air, breath stagnated | Replace with breathing change details |
| revealed (expression) | Replace with specific smiling methods/facial actions |
| fell into (silence) | Write the specific behavior after silence |
| surged, welled up (emotion) | Replace with physical reactions |

### Expression Category

| Word | Replacement Strategy |
|------|----------------------|
| corner of the mouth curled up... | Use specific actions (he smiled/corner of his mouth twitched) |
| a trace of... flashed through his eyes | Replace with action (he looked down/he looked away) |
| face revealed... | Replace with specific facial feature changes |

### Psychological Category

| Word | Replacement Strategy |
|------|----------------------|
| feel, think, realize (guiding psychology) | Prioritize replacing with actions; allowed ≤2 times/chapter at critical psychological nodes |
| a wave of... surged in his heart | Replace with action |
| his inner heart... | Show through behavior |
| only then did he realize/understand | Suggested to change to a specific reaction, but can be kept in red-line paragraphs |
| he couldn't help but/involuntarily | Prioritize direct action, keep those with rhythmic function |

### Judgment Category

| Word | Replacement Strategy |
|------|----------------------|
| obviously, evidently | Everyone knows / State facts directly |
| undoubtedly | Surely / State directly |
| unquestionable | Just is / State directly |
| seemingly, perhaps, probably (high freq) | Delete or change to specific state |
| actually, unexpectedly (high freq) | Let the reader judge for themselves |

### Adjective Category

| Word | Replacement Strategy |
|------|----------------------|
| firm, profound, icy (universal adjectives) | Delete or change to specific state |
| warm, soft (generalized light sensations) | Use specific light and shadow descriptions |
| so-called AABB style adjectives | Keep only colloquial usages |
| slowly, gradually (modifying verbs) | Delete, or change to specific speed |
| a certain kind, a wave, a burst (with abstract emotions) | Delete or specify |

### Transition Category

| Word | Replacement Strategy |
|------|----------------------|
| however, but | Moved to T2 — tag only if appearing ≥2 times in the same paragraph; single occurrence kept |
| thereupon/so | Moved to T3 — ≤3 times/chapter overall; keep if narrative function is obvious |
| meanwhile | Change the scene opening directly |
| in short, to sum up, altogether | Delete |
| this chapter, next chapter, the story continues | Delete (meta-narrative prohibited) |

---

## II. T2 Same-Paragraph Clustering + T3 Full-Text Density

### T2 Same-Paragraph Clustering (Tag condition: ≥2 times in the same paragraph)

Only tag if it appears ≥2 times in the same paragraph; keep only the most fitting one. Single occurrences are ignored.

| Word | Replacement Strategy |
|------|----------------------|
| however, but | Appears ≥2 times in the same paragraph → keep one, delete the rest |
| in addition, besides, simultaneously | Appears ≥2 times in the same paragraph → merge or split |
| although... but..., despite... yet... | Appears ≥2 groups in the same paragraph → keep only one group |
| "Le" particle clustering (≥4 in the same paragraph) | Tag the paragraph for the author to judge; do not modify presumptuously after tagging |

### T3 Full-Text Density (Tag condition: exceeding proportion in the whole text)

No per-word threshold; based on the paragraph/word count proportion of the full text. Only tag if the proportion exceeds the limit.

| Detection Item | Threshold | Handling |
|----------------|-----------|----------|
| "Le" particle density | Average >2.5 per paragraph | Tag high-density paragraphs, keep critical "le"s |
| Period density | Continuous 10+ sentences ending in a period | Change some periods to commas/ellipses to break them up |
| Paragraph length uniformity | Continuous 5 paragraphs with sentence difference ≤1 | Scatter or merge to create length variations |
| Basic word density | important/critical/core >3 times/chapter | Replace with specific descriptions or delete |

### Context-Sensitive Category (Cross-tier, judged by context)

Triggers replacement when exceeding the threshold; can be kept if not exceeding. **Words that already appeared in T2/T3 are processed according to T2/T3 first.**

| Word | Threshold | Replacement Strategy |
|------|-----------|----------------------|
| suddenly/abruptly/fiercely | ≤4 times/chapter | Lenient on red-line paragraphs, keep those with rhythmic function |
| actually/unexpectedly | ≤3 times/chapter | Lower penalty, allow moderate retention |
| gradually | ≤2 times/chapter | Replace with details of change |
| still/yet | ≤2 times/chapter | Replace with state descriptions |
| seemingly/perhaps/probably | ≤3 times/500 words | Moderately relax, keep necessary vague judgments |
| thereupon/so | ≤3 times/chapter | Keep if narrative function is obvious |

### Formal Tone

| Word | Replacement Strategy |
|------|----------------------|
| disintegrate/collapse | disappear/scatter/gone |
| undoubtedly | definitely/just is |
| obviously | everyone knows |
| deep in the heart | delete or write action directly |
| at this time he still didn't know | delete (omniscient perspective prohibited) |

### Summary Sentence Structures

| Structure | Handling |
|-----------|----------|
| "This is..." | Delete, let the scene end naturally |
| "He finally understood..." | Delete, stop on the image/action |
| "Everything... all..." | Delete or change to a specific state |
| "What he didn't know was..." | Delete or change to a specific hook |

### Parallel Sentence Structures

| Pattern | Handling |
|---------|----------|
| "Perhaps it is... perhaps it is... perhaps it is..." | Keep the strongest one, scatter the rest |
| "Not... not... but rather..." | Write the "but rather" part directly |
| "Neither... nor... let alone..." | Keep only the most specific one |
| "Both X and Y" symmetrical filling ("both nervous and excited") | Delete one side, keep the primary side |
| "Not only... but also..." value-elevating skeleton | Delete the "Not only" part, keep the fact after "but also" |
| "First... second... finally" mechanical arrangement | Scatter into natural narration, do not make it feel like a list |
| "From X to Y, from A to B" parallel traversal | Choose one or two most representative ones, delete the rest |
| "No... no... not even..." negative parallelism | Keep only the most specific one, or scatter into statements |

### Elevating Sentence Structures

| Pattern | Handling |
|---------|----------|
| "This night is destined to..." | Delete |
| "From this moment on..." | Delete |
| "Perhaps this is the meaning of..." | Delete |
| "The future is promising/let's wait and see/long days ahead" positive energy wrap-up | Delete, stop on a specific image |
| Exclamation mark wrap-up ("...!") | Change to a period or action |

---

## III. Prohibited Sentence Structure Templates

### Metaphor Category

| Type | Handling | Exception |
|------|----------|-----------|
| AI template metaphor ("like a picture scroll") | Delete | — |
| Universal metaphor ("like a...") | Delete or describe plainly | — |
| Slice-of-life metaphor ("like being bitten by a mosquito") | Keep | Fits character's cognition |
| Character-based metaphor ("like the way his dad beat him") | Keep | Fits character background |
| "As if/like/just like" guidance | Delete | — |

### Structure Category

| Structure | Handling |
|-----------|----------|
| "Continuous 4 sentences with the same subject" | Vary subjects and sentence structures |
| "Continuous 3 paragraphs with the same opening" | The subject/scene of the first sentence of each paragraph cannot repeat |
| "Continuous 5 sentences with the same structure" | Alternate statements/short sentences/dialogue |
| "Continuous 2 paragraphs describing the same object" | Do not repeatedly describe scenery/characters |
| "Reuse of the same metaphor" | Metaphors/imagery used once should not be reused in the same chapter |
| "Repetition of the same emotion word" | Emotion words like nervous/happy/sad ≤3 times per chapter |

### Punctuation Category

| Rule | Explanation |
|------|-------------|
| Full-text ban on em dashes (——) for pauses | Limit to ≤3 times/chapter, ban multiple uses within one paragraph. Can be used for pauses/turns, penalized if exceeded |
| Em dash parenthetical redundancy (——description——) | "A——long appositive——did B" → Write "A did B" directly |
| Voice interruption in dialogue using "..." or direct truncation | Silence should end with a period |
| Forced interruption in dialogue | Can use "——", ≤1 time per chapter |
| "Le" particle purification (Warning) | Continuous 3 sentences with 5+ "le"s → Tag paragraph for author to judge, do not blindly modify |

### Rhetorical Questions and Assumption Skeletons

| Structure | Handling |
|-----------|----------|
| "If I told you..." rhetorical buildup | State the conclusion directly, no need to ask then answer |
| "Have you ever thought..." guiding rhetorical question | Delete the guide, write facts directly |
| "Imagine..." hypothetical introduction | Delete, describe directly |
| "Research shows..." unsourced citation | Delete "Research shows" or change to a specific source |

---

## IV. Quick Reference for Replacement Strategies

### Externalizing Emotions

| Emotion Word | Replace With |
|--------------|--------------|
| Nervous/Anxious | Trembling hands, sweaty palms, rapid breathing, restlessness |
| Angry/Trembling with rage | Veins on the back of the hand popping out one by one, voice becoming softer instead |
| Afraid/Fearful | Fingers touching the doorknob and withdrawing, holding breath |
| Sad/Sorrowful | Zoning out, hands stopping, movements slowing down |
| Surprised/Shocked | Hand stopping in mid-air, freezing, forgetting to breathe |
| Heartache/Heartbroken | Digging fingernails into flesh without feeling the pain |
| Wronged/Aggrieved | Biting the lower lip, leaving a white mark |
| Despairing | Cigarette ash falling all over the trouser leg without flicking it off |

### Dialogue Tag Replacements

| Phrasing | Replacement |
|----------|-------------|
| "He said angrily" | Delete adverb, or change to action: "He slammed the cup on the table. '...'" |
| "She said softly" | '...' She lowered her head after speaking |
| High frequency of "He said" "She said" | Use actions/context to carry on |
| Everyone has the same tone | Differentiate tone according to character personality |

### Perspective Control

| ❌ Prohibited | ✅ Required |
|--------------|-------------|
| God's-eye view (Omniscient) | Focus on the protagonist's perspective |
| Perspective jumping between supporting characters | Protagonist must be present |
| Supporting character screen time exceeds protagonist's | Briefly summarize supporting character plots |
| Omniscient narration ("He didn't know a bigger storm was brewing") | Delete |

---

## V. Quick Reference for Fatigue Word Original Thresholds

Retaining original thresholds for quick scanning:

| Word | Single Chapter Threshold | Notes |
|------|--------------------------|-------|
| suddenly/abruptly/fiercely | ≤4 times | Lenient on red-line paragraphs |
| actually/unexpectedly | ≤3 times | Moderately retain |
| gradually | ≤2 times | Replace with details of change |
| still/yet | ≤2 times | Replace with state descriptions |
| however/but | ≤4 times | T2 same-paragraph ≥2 times to tag |
| thereupon/so | ≤3 times | Keep if narrative function is obvious |
| meanwhile | ≤1 time | Change the scene opening directly |
| pupils shrank/body stiffened | ≤2 times | Maintain zero tolerance |
| gasped cold air/breath stagnated | ≤1 time | Maintain zero tolerance |
| seemingly/perhaps/probably | ≤3 times/500 words | Keep necessary vague judgments |
