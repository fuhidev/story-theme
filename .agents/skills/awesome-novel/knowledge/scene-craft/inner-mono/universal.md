# Psychological Activity / Inner Monologue

> Core principle: **Psychology is primarily presented through physiological reactions, fragmented thoughts, and cognitive collapse, with emotion words as a secondary supplement.**
> Prioritize using bodily reactions (chills running through the body, trembling fingertips, clenched jaw) instead of emotional nouns.
> However, it is permissible to use 1-2 emotion words at key emotional nodes to help readers understand ("a trace of fear flashed across his heart"),
> but do not rely on emotion words for expression throughout the text. Each technique starts with a `[Tag]`.

## Injection Timing

Psychological description is not a scene type and cannot be automatically detected via the scene type in Input·Scene Raw Material. The prompt-crafter additionally loads this file to inject into Output·Writing Guidelines **when any of the following conditions are met**:

| Trigger Condition | Judgment Criteria | Example |
|---------|---------|------|
| **Character encounters a major event** | L3 core suspense/turning point involves the character receiving shocking information | Learning a loved one was murdered, discovering betrayal, truth revealed |
| **Character's emotions fluctuate violently** | The scene emotion in Input·Scene Raw Material is intense, such as fear/anger/breakdown/ecstasy | Confrontation erupts, life-or-death moment, reuniting after a long time |
| **Character faces a major choice** | The chapter involves the character making a key decision (key_choices) | Choosing a faction, deciding who to sacrifice, deciding whether to take a gamble |
| **Face-slapping / Reversal scene** | The opponent's progressive process from disbelief to breakdown | Villain is crushed, conspiracy is exposed |
| **Character's inner conflict** | Contradiction between what the character wants to do and what they should do | Wants to save but can't, wants to love but can't |

### Loading Method

It is not placed in the regular scene type loading process. After the scene sequence in Input·Scene Raw Material is filled, **additionally check** whether the core event + emotional design + key choices of each scene trigger the above conditions. If triggered, load `inner-mono/universal.md` + `inner-mono/{genre}.md` (if it exists), and append it to Output·Writing Guidelines after the four-step conversion.

## Tag Selection Guide

| Scene Characteristics | Matching Tags | Selection Reason |
|---------|---------|---------|
| Character feels fear, tension, anxiety | [Physiological] | Use bodily reactions like chills, tinnitus, and trembling fingertips instead of "scared" |
| Character is angry, suppressed, enduring | [Physiological] | Use throbbing temples, clenched jaw, and heavy breathing instead of "angry" |
| Character is attracted, flustered, nervous | [Physiological] | Use tight chest, burning ears, and stiff limbs instead of "heart pounding" |
| Character encounters immense grief, breakdown, ecstasy | [Fragmented] | Thoughts jump around illogically; behavior and emotion form an uncoordinated contrast |
| Character witnesses facts that overturn their cognition | [Collapse] | Three-stage progression: arrogant questioning → self-deception → utter breakdown |
| Face-slapping/reversal scenes need amplified satisfaction | [Collapse] | Let the reader see the villain's full process from disbelief to breakdown |


## [Physiological] Physiological Synesthesia Method — Mapping Emotions Through Bodily Reactions

**Core:** Do not straightforwardly label emotion words; indirectly reflect the inner state through human senses, limbs, and bodily instinctive reactions.

### Emotion → Physiological Reaction Mapping Table

| Emotion | Physiological Signal |
|------|---------|
| **Fear** | Chills running through the body, tinnitus, dry and astringent mouth, trembling fingertips, auditory isolation, tight throat |
| **Anger** | Temples throbbing, jaw clenched and aching, nails digging into flesh, heavy breathing, blood rushing to the head |
| **Attraction / Flustered** | Tight chest, short breath, burning ears, stiff limbs, sweaty palms |
| **Suppression / Enduring** | Clenching fingers, rolling Adam's apple, biting down on jaw, pausing in silence, breath caught in the chest |

```
❌ After learning the truth, he was filled with fear and completely panicked.

✅ A chill shot straight to the top of his head, his ears buzzed incessantly, and all surrounding sounds faded away.
   He tried to swallow, but his throat was smoking dry, and his fingertips trembled uncontrollably.
```

**Control Usage:** Prioritize physiological reactions over direct emotional adjectives ("scared" → "trembling fingertips"). Allow 1-2 emotion words at key emotional nodes to assist reader understanding, but do not rely on emotion words throughout.


## [Fragmented] Fragmented Thoughts Method — Fragmented Thinking Under Extreme Emotion

**Core:** When a person falls into intense emotions like immense grief, ecstasy, or breakdown, their thinking loses logic, word order becomes messy, and thoughts jump and fracture. Use incoherent muttering and contradictory behavior to strengthen emotional tension.

### Execution Details

- Behavior and emotion form a contrast: calm on the surface, actions out of control.
- Thoughts jump without logic, interspersed with irrelevant daily thoughts.
- Actions gradually lose composure; details expose the true state of mind.

```
❌ Looking at the fallen person, he shouted in grief, tears flowing uncontrollably.

✅ His expression was calm as he raised his hand to constantly stuff pills into the other's mouth, muttering to himself:
   "It's just an ordinary fireball spell, take the medicine and we'll go to the temple fair tomorrow...
   Your clothes are dirty, I'll wash them for you later."
   His movements became more and more hurried, pills mixing with black blood spilling all over the floor.
```

**Forbidden:** Remaining logically clear and speaking neatly when emotionally agitated; straightforwardly crying and venting through shouting.


## [Collapse] Progressive Psychological Collapse — Layered Description of Cognitive Dissonance

**Core:** When a character encounters reality that overturns their cognition, their psychology slides down layer by layer in three stages, progressively amplifying the satisfaction/impact.

### Three-Stage Progression

| Stage | Psychological State | Manifestation |
|------|---------|------|
| **Stage One** | Contempt, mockery | Believes the other party is playing tricks, an accident occurred, impossible to be true |
| **Stage Two** | Self-deception | Facts are right in front of them, forcefully making excuses, refusing to accept |
| **Stage Three** | Utter breakdown | Illusions shattered, self-esteem and cognition completely disintegrate, accompanied by physical loss of composure |

```
❌ The senior martial brother was shocked, thinking: This is impossible, how could he be so strong?

✅ A sneer hung on his lips, treating it only as the other party being deliberately mystifying and playing tricks.
   As the pressure swept over, his smile suddenly stiffened, cold sweat crawling all over his forehead.
   He kept defending in his heart: He must have used an evil art, he won't last long.
   The moment the spirit stones exploded, the debris scraped his cheeks raw.
   He retreated again and again, the light in his eyes completely gone—his proud talent was reduced to a joke at this moment.
```

**Forbidden:** Skimming over psychological description with just a single "couldn't believe it"; writing the breakdown directly with no layers or transition.
