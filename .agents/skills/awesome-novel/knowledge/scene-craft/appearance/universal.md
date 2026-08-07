# Character Appearance Description

> Do not pile up facial features, do not interrupt the plot, and do not disconnect appearance from the character. Each technique starts with a `[Tag]`.

## Injection Timing

Character appearance is not a scene type and cannot be automatically detected via the scene type in Input·Scene Raw Material. The prompt-crafter additionally loads this file to inject into Output·Writing Guidelines **when any of the following conditions are met**:

| Trigger Condition | Judgment Criteria | Example |
|---------|---------|------|
| **New character appearance** | The characters appearing in this chapter include someone not present in the previous chapter | A new villain is introduced in Volume 2, and the reader sees him for the first time |
| **Character appearance change** | The character undergoes obvious changes like injury, aging, outfit change, transformation, etc. | The protagonist reveals his face for the first time after being disfigured, the supporting female character changes her style |
| **Scene involves appearance perception** | The core scene event or scene description in Input·Scene Raw Material includes "looking at face / observing appearance / sizing up", etc. | "He sized up the person opposite him", "She noticed the other person's scars" |

### Loading Method

It is not placed in the regular scene type loading process. After the scene sequence in Input·Scene Raw Material is filled, **additionally check** whether the core event of each scene triggers the above conditions. If triggered, load `appearance/universal.md` + `appearance/{genre}.md` (if it exists), and append it to Output·Writing Guidelines after the four-step conversion.

## Tag Selection Guide

| Scene Characteristics | Matching Tags | Selection Reason |
|---------|---------|---------|
| Character's first appearance, need to give readers a first impression | [Anchor] [Dynamics] | Establish memory points using a core visual anchor, bring out appearance through actions |
| Character's appearance does not match personality/experience | [Background Story] | Make appearance features have an origin, not designed out of thin air |
| Appearance description feels like a ledger (from hair to toes) | [Anchor] | Grasp only one signature feature, leave the rest to the reader's imagination |
| Appearance description feels like pausing the plot to introduce separately | [Dynamics] | Integrate appearance into continuous actions like entering, sitting down, scanning |
| Character traits are only used once upon appearance and never mentioned again | [Background Story] | Bind traits with the character's experience, so they can be repeatedly referenced later |
| Do not want straightforward description of looks, want stronger impact | [Indirect Characterization] | Contrast characters through others' expressions/atmospheric changes, leaving room for reader imagination |


## [Anchor] Extract Core Visual Anchors — Using Points to Represent the Whole

**Core:** Do not pile up details all over the body, just grab 1 signature feature to amplify, leaving the rest to the reader's imagination.

### Execution Rules

- Lock a single core identifier for each character: scar, eye shape, mole, posture, specific pattern.
- Engrave details around the identifier, do not evenly describe hair, facial features, figure, and other regular parts.
- Abandon generic template words like "sinking fish and swooping geese, sword eyebrows and starry eyes, face like crown jade".

```
❌ His eyebrows were thick and heavy, his eyes round and large, with a beard on his face, looking very ferocious.

✅ The flesh on his face rose and fell with his breathing, and a knife scar extended from the corner of his left eye to the root of his ear, radiating a fierce aura.

❌ She had a beautiful face and touching eyes.

✅ Her fox-like eyes narrowed into thin slits when she smiled, and the teardrop mole at the corner of her eye swayed people's hearts.
```

**Forbidden:** Ledger-style descriptions from head to toe; applying internet-famous ancient-style aesthetic cliches; listing multiple ordinary facial features.


## [Dynamics] Dynamic Behavior Materialized — Do Not Pause the Plot

**Core:** Abandon the static writing style of "characters standing still, describing looks separately", and integrate appearance into continuous actions such as entering, sitting down, scanning, and smiling.

### Execution Rules

- Appearance description is interspersed in the character's action flow, without interrupting the narrative pacing.
- Figure and aura do not rely on straightforward description, but are reflected indirectly through the environment, clothing, and the reactions of others.
- Signature features are exposed and extended with action changes, strengthening the dynamic sense of the scene.

```
❌ He was tall and burly, looked fierce, and had an intimidating aura.

✅ He pushed the door and entered, making the doorframe tremble slightly. As he took large strides and sat down, his coarse cloth shirt bulged with tense muscles.
   His gaze swept across the room, and those touched by his line of sight shrank their necks one after another.
   He grinned, and the knife scar on his face twisted accordingly.
```

**Forbidden:** Plot stagnation, starting a new paragraph just to introduce appearance statically; describing statically throughout, with the character having no actions whatsoever.


## [Background Story] Binding Appearance to Personality/Experience/Fate — Let the Skin Tell a Story

**Core:** Appearance details are more than just looks; they must be deeply bound to the character's profession, past trauma, personality habits, and life experiences, giving the details narrative quality.

### Execution Rules

- Design exclusive postures, scars, and habitual small actions, and add the underlying causes.
- Appearance features appear repeatedly with character states and scenes; they are not just used once upon appearance and discarded.
- Use external details to hint at identity, experience, and state of mind.

```
❌ The veteran was tall and ordinary-looking.

✅ His sitting posture was always as straight as a javelin, and his left leg muscles would tense up slightly involuntarily—
   It was an old ailment left by shrapnel injuries on the battlefield in his early years.
```

**Forbidden:** Appearance completely disconnected from persona and experience; features only used once upon appearance and never echoed later.


## [Indirect Characterization] Writing Appearance Through the Eyes of Others — High-Level Blank Space Technique

**Core:** Do not describe the character's looks directly, but contrast the character's traits through the expressions of others and atmospheric changes at the scene, leaving room for reader imagination and creating a stronger impact.

### Execution Rules

- Prohibit piling up evaluative adjectives like "nation-toppling beauty, looking like a celestial being".
- Present the effect through third-party perspectives' physiological reactions, behavioral pauses, and sudden atmospheric changes.
- The more specific the bystander's reaction is (rather than a vague "everyone was stunned"), the stronger the impact.
- The choice of contrasting perspective should fit the scene: lower rank observing higher rank, commoner observing noble, enemy observing strong opponent.

```
❌ Her appearance was stunningly beautiful, nation-toppling, and everyone was shocked by her beauty.

✅ The moment she stepped into the hall, the clamor abruptly stopped.
   Guests raising their glasses suspended them, forgetting to drink, and the servers' footsteps paused in mid-air.
   The candlelight reflected on her face, making the women present unable to look away from any angle.

❌ He looked fierce and wicked, seemingly not someone to mess with.

✅ He stood at the entrance of the alley, and the punks opposite unanimously took half a step back.
   The one at the very front swallowed a mouthful of saliva, the hand gripping the knife trembling slightly.
```

**Forbidden:** Straightforwardly using evaluative conclusions like "beautiful/handsome/ugly/fierce"; vaguely writing "everyone was stunned" without providing specific reaction details.
