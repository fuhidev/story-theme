# Environment Description

> The environment is not a background board, but an emotion amplifier. Each technique starts with a `[Tag]`.

## Injection Timing

As a standard scene type, environment description is automatically identified and loaded by the prompt-crafter based on the core event of the scene. **If the core scene event involves the following elements, it is automatically identified as the `environment` type:**

| Trigger Condition | Judgment Criteria | Example |
|---------|---------|------|
| **Spatial Movement** | Character enters a new location, scene undergoes a spatial transition | Walking into a tavern, passing through a dense forest, arriving at the battlefield |
| **Atmosphere Enhancement** | The current scene requires specific atmospheric rendering (suspense / suppression / glory / ruin) | Exploring an abandoned manor, a palace audience, a stormy night |
| **Time Marking** | Need to account for changes in time / weather / season | Dusk falling, the first snow of winter, a rainstorm approaching |
| **Special Scene** | The scene itself is the narrative core (maze / secret realm / ruin exploration) | Entering a cave dwelling secret realm, exploring underground ruins |

## Tag Selection Guide

| Scene Characteristics | Matching Tags | Selection Reason |
|---------|---------|---------|
| Need to create an immersive atmosphere | [Five Senses] | Construct visual sense using multi-sensory dimensions |
| Need to express emotion through scenery | [Emotion Rendering] | Externalize character emotions through the environment, blending emotion and scenery |
| Need to explain background indirectly | [Detail Interaction] | Hint at situations through object details, no straightforward summaries |


## [Five Senses] Multi-Sensory Superposition — Building an Immersive Picture

**Core:** Abandon single visual description; superimpose multi-dimensional portrayals of hearing, smell, touch, and scent.

### Execution Rules

- Select 2-3 sensory combinations for each environmental description; do not pile up all senses.
- Sensory choices should fit the scene's atmosphere: prioritize touch + hearing for cold scenes, prioritize smell + hearing for oppressive scenes.
- Each sensory clue should have a narrative function, not just added for the sake of being "multi-sensory".

```
❌ The ruined temple was dirty, messy, and full of dust and spider webs.

✅ The wooden door creaked, echoing within the hall.
   The smell of rotting grass and accumulated dust floated in the air, and a chill crept up from the ankles.

❌ The sun was warm, the breeze brushed the face, the fragrance of flowers overflowed, and the birds chirped crisply.

✅ The sun scorched the back of the neck, the wind carrying the scent of wilted grass rushed over,
   and cicadas chirped intermittently in the distance.
```

**Forbidden:** Using all five senses causing information overload; using AI cliché sentence patterns like "the air was filled with" or "as if you could smell".


## [Emotion Rendering] Expressing Emotion Through Scenery — Environment Changes with State of Mind

**Core:** The perception of scenery dynamically changes with the protagonist's state of mind, externalizing character emotions through the environment. The same scenery presents different characteristics under different emotions.

### Execution Rules

- The characteristics of the same scene under different states of mind should be opposite (sunlight is warm when happy, glaring when depressed).
- The choice of scenery for emotion rendering should be consistent with the character's current core emotion.
- Do not write emotion words directly (sadness / anger / joy), but let the reader feel it through sensory filters.

```
❌ The protagonist was in a good mood, felt the sun was exceptionally bright, and the scenery was exceptionally beautiful.

✅ The sunlight was like gold, casting a warm glow on the road. The wildflowers swayed toward the sun,
   and even the pebbles on the roadside felt lovely.

❌ The protagonist had just gone through a breakup; she felt everything around her was very sad.

✅ The sunlight glared so brightly she couldn't open her eyes, the grass and flowers by the road were covered in mud,
   and the crushed stones hurt the soles of her feet.
```

**Forbidden:** Directly using emotion words like "happy mood / depressed inside" to summarize; the choice of scenery for positive and negative emotions being exactly the same.


## [Detail Interaction] Objects Expressing Meaning — Let Details Tell the Story

**Core:** Rely on objects and tiny scene details to indirectly explain the background, situation, and character state; do not summarize straightforwardly.

### Execution Rules

- Choose 1-2 representative detail objects; do not use too many.
- The characteristics of the object hint at the character's situation: cheap / dilapidated → distressed, exquisite / elegant → wealthy.
- Details can hint at time (new / old / degree of wear) and events (traces / damage / changes).

```
❌ He came from a poor family and didn't have any good things at home.

✅ The rim of the enamel mug on the table had several chipped gaps,
   the toothpaste tube was repeatedly squeezed flat, and the remaining paste inside was scraped clean.

❌ This was a luxurious study.

✅ A half-open landscape scroll was spread out on the rosewood desk.
   Several wolf-hair brushes hung on the pen rack, and beside the Xuan paper with wet ink,
   the clear water in the celadon brush washer had been dyed a light ink color.
```

**Forbidden:** Directly using summary words like "poor / luxurious / dilapidated / magnificent"; piling up multiple details of the same type (if three bowls are chipped, writing about one is enough).

---

> 💡 **Expansion Directions:**
> - How weather / season / time serves the emotional tone
> - The layering of spatial layout (long shot → medium shot → close-up → macro)
> - The functional utility of the environment in different scenes like combat / dialogue / pursuit
> - Detail anchors for a sense of era / region
> - Counter-examples: common AI clichés like "sunlight poured in" and "the air was filled with"
