# Character Setting Guide

> Fill in the basic information first, then discuss the 6-layer cognitive model layer by layer. After discussing, write it into `settings/character-setting/<id>.md`.
> For villain settings, refer to `knowledge/character-craft/villain-types.md` — three types of high-IQ villain templates (Chess Player / Martyr / Mimic).
> The 6 layers influence downwards from layer 1. Layers 1-3 are abstract (deciding *why* they do it), and layers 4-6 are concrete (deciding *how* they do it).

---

## Principle 1: The Character is a Decision Engine, Not a Resume

**Rule:** After filling in the settings, you must be able to infer what the character would think and do in any given situation. If you fill out a bunch of info but can't deduce their behavior → that's a resume, not a character setting.

✅ Correct:
> **Worldview**: The strong make the rules, the weak follow them.
> → When encountering something unfair, the character won't complain, they will think about how to get stronger.

❌ Incorrect:
> Personality: Smart, calm, rational
> → Stacking labels. How does "smart" manifest? How calm? Will they still be calm if their family is in danger?

**Judgment Standard:** Given an unexpected scenario ("someone stabs a stranger next to them on the street"), can you state without thinking what the character would do? If not → the setting isn't specific enough, keep discussing.

---

## Principle 2: The Protagonist Must Have True Flaws

**Rule:** "Too righteous", "too kind", "too trusting" are not flaws — they are just different ways of praising the character. A true flaw is something that makes the character make the **wrong choice** at a critical moment.

✅ Correct:
> Impulsive (charges straight in when friends/family are in danger, regardless of consequences)
> Suspicious (trusts no one, can't ask for help even when needing it)
> Inferiority complex (feels unworthy of being saved, gives up asking for help at critical moments)

❌ Incorrect:
> "He is too kind" / "He is too righteous" / "He trusts people too much"
> → These are variations of virtues. The reader can feel the author is praising the character.

**Judgment Standard:** Will this flaw cause the character to make a wrong decision in chapters 3-5? If not → it's not a true flaw, change it.

---

## Principle 3: Supporting Characters Have Self-Driven Goals

**Rule:** Supporting characters do not exist for the protagonist. Take the protagonist away, and the supporting character still has their own desires and fears, and can act independently.

✅ Correct:
> Lin Yan is a dark web informant. She helps the protagonist because she owes a favor, but she has her own survival rules — don't pick sides, don't stand out, live long. If necessary, she will betray the protagonist to protect herself.
> → Take the protagonist away, and Lin Yan is still that woman surviving in the gray area, continuing her intelligence trade.

❌ Incorrect:
> Old Zhang is the protagonist's friend, always showing up to help when the protagonist needs it.
> → Tool character. Take the protagonist away, and Old Zhang disappears.

**Judgment Standard:** If you remove the protagonist from the story, does this supporting character still have something to do? If not → reject and add a self-driven goal.

---

## Basic Information

Determine these first, then proceed to the cognitive model.

Ask the author:
1. "What is the character's name? Are they a protagonist, antagonist, or supporting character in the story?"
2. "What do they look like? Any signature physical features?"
3. "What is their background — origin, experiences, where they came from?"
4. "Do they have any signature catchphrases, profanities, or linguistic quirks?"

Write into the file:

```markdown
### Basic Information
- **ID**: <pinyin_id>
- **Name**: <Chinese Name>
- **Story Role**: protagonist / antagonist / supporting
- **Appearance**: Physical features
- **Background**: Origin and experiences
- **Linguistic Features**: Signature catchphrases, profanities
```

**Basic information is not the core of the setting, but it determines the character's position in the story and the reader's first impression of them.**

---

## 6-Layer Cognitive Model

Discuss layer by layer downwards starting from layer 1. Each layer depends on the previous one being finalized. Do not skip layers or go backwards.

**Structure:**
```
Abstract (Why they do it)          Concrete (How they do it)
Layer 1 Worldview   ←Hard         Layer 4 Capabilities    ←Changeable
  ↓                                 ↓
Layer 2 Self-Identity ←Hard       Layer 5 Skills          ←Changeable
  ↓                                 ↓
Layer 3 Values       ←Hard        Layer 6 Environment     ←Changeable
```

**Changeability Rules:** The top three layers (Worldview → Self-Identity → Values) are harder to change the further up you go — the worldview is almost fixed, while values might loosen under the impact of major events. The bottom three layers (Capabilities → Skills → Environment) can naturally change as the plot progresses: a character can learn new skills, change environments, or grow their capabilities. The essence of plot progression is "the bottom three layers change, while the top three layers are shaken."

**Changing the top three layers requires extraordinary hardship.** It doesn't happen with a single conversation or sudden event — it requires continuous impact, repeated verification, and making the character unable to explain the new reality with old beliefs, ultimately collapsing and rebuilding.

**Example — Layer 2 Self-Identity:**
> A character identifies as a "Demon Lord", killing people and destroying sects for no reason; he does everything a demon should. But his worldview (Layer 1) has a bottom line — "This world must exist, it cannot be destroyed." Therefore, everything he does has a prerequisite: it must not affect the world's survival. As long as the world won't be destroyed, he kills and destroys without psychological burden.
>
> This is the subtle relationship between Layer 1 and Layer 2: His worldview hasn't made him "good", it just drew a boundary for the "demon". He objectively eliminates some threats to the world's survival — but not *for* the world, only because those threats touched his bottom line. He still feels he is a Demon Lord, and the world's fear of him is not wrong.
>
> To change this self-identity of "I am a Demon Lord", it's not enough for someone to praise him — he needs to see with his own eyes that a truly good person did what he did, and still chose to be a good person. He needs to realize "I chose to be a demon because I was afraid of the price of being a good person", and this realization requires him to sacrifice himself to protect someone in a life-or-death moment, survive, and then face the question "Why did I do that?"

**Example — Layer 3 Values:**
> When the country and their own family both need the character, what do they choose?
>
> If the character's value is "Country first, then family", then when the country drafts them, they will leave their family and head to the frontlines. It's not because they don't care about their family — but because when two values conflict, the "Country" ranks above the family. This choice doesn't need to change; it's the value system at work.
>
> What truly tests values isn't "choosing the right thing" — it's "both are right, but you can only choose one". The character must choose between two things they cherish equally, and the result of that choice defines who they truly are.

**Contrast — Layer 4 Capabilities:**
> Conversely, changing Layer 4 Capabilities doesn't require this level of hardship. Shen Mu's archery skills can improve in the first chapter: going from missing all three rapid-fire shots to hitting two out of three can happen naturally through practice and real combat.

### Layer 1: Worldview — How the Character Sees the World

Clarify "how the character believes this world operates". This isn't the author's setting; it's the version the character **personally believes**.

Ask this question:
> "In your character's eyes, what is this world like? Who survives, and who doesn't?"

✅ Correct:
> The strong make the rules, the weak follow them. Complaining is useless; only getting stronger works.

❌ Incorrect:
> He believes in justice. (Too abstract — what specific behaviors does justice entail?)

**Judgment Standard:** Will this worldview cause the character to conflict with others (especially other characters)? If not → it's too mild, make the character a bit more opinionated.

**Self-Check for this section (complete before moving to Layer 2):**
- [ ] Is the worldview the version the character believes in, not the author's world rules?
- [ ] Can the worldview explain why the character does things this way?

---

### Layer 2: Self-Identity — How the Character Sees Themselves

Clarify "what kind of person the character thinks they are". Note: this might not be who they actually are — cognitive dissonance is a dramatic space.

Ask this question:
> "What kind of person does the character think they are? — Note, we are asking what *they* think, which may not match reality."

✅ Correct:
> Thinks of himself as calm, rational, and planning before acting, but in reality, when friends/family are in danger, his first reaction is to charge in; the rationality is an afterthought.
> → Cognitive dissonance. The character always finds an excuse for themselves after acting impulsively.

❌ Incorrect:
> He is very confident. (Confidence is a result, not a way of viewing oneself.)

**Judgment Standard:** Is there a gap between their self-perception and actual behavior? If not → the character is too "self-consistent" and lacks room for growth.

**Self-Check for this section (complete before moving to Layer 3):**
- [ ] Is the self-identity how the character sees themselves, not the author's evaluation of them?
- [ ] Is there a gap (cognitive dissonance) between their self-identity and actual behavior?

---

### Layer 3: Values — The Source of Difficult Choices

Clarify "what the character absolutely will not do, and what would make them make an exception". Values are meant to be **challenged** — the character's choices when "Value A conflicts with Value B" is the true character building.

Just ask this question:
> "What is something the character would absolutely never do? — What if they had to? Under what conditions would they make an exception?"

✅ Correct:
> Will not betray brothers. Will not oppress civilians. Can use opponents but won't insult their dignity.
> → Each can be challenged — what if betraying a brother could save more people?

❌ Incorrect:
> Values friendship, kind and righteous.
> → Too abstract. Values it to what extent? Would they kill for a friend?

**Judgment Standard:** Can you design a "what if..." challenge scenario for each value? If not → it's too abstract, ask for details.

**Self-Check for this section (complete before moving to Layer 4):**
- [ ] Are there at least 2 specific behavioral boundaries (not abstract virtues) for values?
- [ ] Can each value be challenged with a "what if..." scenario?

---

### Layer 4: Capabilities — What Capabilities the Character Possesses

Clarify "what the character can actually do, and where their output ceiling is". Capabilities aren't titles — "Golden Core Realm" isn't a capability description, "Golden Core Realm can fly on a sword" is.

Ask this question:
> "What can the character actually do? Where is the output ceiling? — How many can they fight? What's the range? Under what circumstances does it fail?"

✅ Correct:
> Can sense the source of killing intent within ten paces; can draw an eighty-pound bow with one hand, three rapid-fire shots without missing; photographic memory but needs time to recall.

❌ Incorrect:
> High martial arts skills. (How high? Can beat how many people? Comparable to whom?)

**Judgment Standard:** For each capability, can you answer "range, ceiling, and limiting conditions"? If not → not specific enough.

**Self-Check for this section (complete before moving to Layer 5):**
- [ ] Does each capability have a clear output ceiling (how many they can fight/range/when it fails)?
- [ ] Is the capability a specific manifestation ("flying on a sword"), not a title ("Golden Core Realm")?

---

### Layer 5: Skills — What Specific Things the Character Can Do

Skills are different from capabilities — capability is "can they fight?", skill is "can they cook or do accounting?". Skills have origins and therefore limitations.

Ask this question:
> "What specific techniques has the character learned? Where from? — Street survival skills learned as a dock worker are useless in the imperial court."

✅ Correct:
> Street survival skills learned at the bottom of the Green Gang (distinguishing the number of people by footsteps, identifying status by clothing); learned abacus and bookkeeping from an accountant.

❌ Incorrect:
> Proficient in zither, chess, calligraphy, and painting. (Origin? No one is born proficient.)

**Judgment Standard:** Are there limitations to the skill's usage scenarios? (In what situations is this skill useless?) If not → it's too versatile.

**Self-Check for this section (complete before moving to Layer 6):**
- [ ] At least 1 skill, with an origin explanation (where they learned it/why they know it)?
- [ ] Does the skill have usage scenario limitations (where/when it doesn't apply)?

---

### Layer 6: Environment — Growing Environment and Current Situation

Clarify "what environment the character grew up in, and what position they are currently in". Origins determine the behavioral baseline — a poor boy's choices won't be the same as a rich young master's.

Ask these two questions:
1. "In what environment did the character grow up? How does this affect them now?"
2. "What position is the character in right now? What baggage can they not shake off?"

✅ Correct:
> Son of a hunter at the foot of Mount Cangwu. Parents died when demonic beasts attacked the village. Adopted by the old shopkeeper of the Green Gang. Grew up working at the docks, has seen all walks of life.

❌ Incorrect:
> Poor background. (How poor? What specific baggage do they have now? How does this relate to the main plot?)

**Judgment Standard:** Can the current situation explain "why the character is in this position in Chapter 1"? If not → the situation is disconnected from the plot.

**Self-Check for this section (complete before outputting):**
- [ ] Are the origin and situation consistent with the world setting (does the character have a corresponding place in the worldview)?
- [ ] Can the current situation explain the character's position and state at the beginning of the story?
- [ ] Do all six fields have values, with no empty fields?

---

## Common Mistakes

| Mistake | Manifestation | Fix |
|------|------|------|
| Labels instead of settings | "Aloof/Two-faced/Tsundere" | Break down into worldview and identity — Why aloof? What experience caused it? |
| Fake flaws | "Too righteous/Too kind/Too trusting" | Change to true flaws that make the character choose wrongly (Impulsive/Suspicious/Inferiority complex) |
| Tool-like supporting characters | Supporting characters exist only for the protagonist; disappear without them | Give supporting characters their own desires and fears |
| Capabilities without boundaries | "High martial arts" "Very strong" | Add specific ceilings — how many they can fight, range, when it fails |
| Situation disconnected from plot | A lot of origin info written, but totally unrelated to the main plot | Every piece of situation info must answer "how is this useful to the plot?" |
| Misplaced reader insertion | The protagonist is someone the reader relates to, not someone they look up to | The protagonist isn't a perfect idol; they make the reader think "I might do that too" |

---

## Where to Output

`settings/character-setting/<pinyin_id>.md`, one file per character.

### Possessions

Items, equipment, props, etc., currently owned by the character. The updater will automatically add/remove/update these based on plot changes when archiving.

```markdown
### Possessions
| Name | Type | Origin | Status | Notes |
|------|------|------|------|------|
| Qingshuang Sword | Magical Artifact | Captured from Black Wind Stronghold | In use | Crack on the blade, needs repair |
| Peiyuan Pill ×3 | Elixir | Sect monthly allowance | Unused | — |
```

- **Type:** The broad category of the item (Artifact/Elixir/Equipment/ID/Weapon/Intel, etc.), no restrictions.
- **Origin:** Where it was obtained (Captured/Purchased/Gifted/Crafted, etc.).
- **Status:** In use / Unused / Consumed / Destroyed / Gifted away — updated by updater based on plot changes.

### Experiences

Major events the character participated in, locations explored, missions completed. No genre restrictions — secret realm explorations, dungeon raids, criminal investigations, battlefield experiences all count as "experiences".

```markdown
### Experiences
| Event/Location | Type | Result | Status |
|-----------|------|------|------|
| Cangwu Ruins | Secret Realm Exploration | Obtained Peiyuan Pill recipe | Ongoing |
| Ten Thousand Bone Cave | Secret Realm Exploration | Obtained Xuanming Bone Armor | Completed |
```

- **Type:** Broad event category (Secret Realm/Dungeon/Mission/Battlefield/Investigation, etc.), no restrictions.
- **Result:** Main gains or changes.
- **Status:** Ongoing / Completed — updated by updater when archiving.

### Plot Resume

Records what the character actually did and how relationships changed in each chapter. The updater extracts and appends this from the text during archiving.

```markdown
### Plot Resume

#### Vol 1 Ch 3
- **Action:** Killed Zhao Hu, the boss of Black Wind Stronghold, and rescued kidnapped villagers.
- **Relationship Changes:** Became hostile with the remnants of Black Wind Stronghold; rescued villager Uncle Zhang owes him a life.

#### Vol 1 Ch 5
- **Action:** Refused the recruitment of the Green Gang's old shopkeeper, choosing to track the source of the demonic beasts alone.
- **Relationship Changes:** Relationship with the Green Gang changed from cooperative to distant; Lin Yan secretly keeping an eye on his movements.
```

- **Action:** What the character actually did in this chapter (including objects and results), summarized in one sentence.
- **Relationship Changes:** What qualitative change occurred in the relationship with whom (Hostile/Cooperative/Distant/Owes favor, etc.). Leave blank if none.

---

## Examples

The following examples can be used as references when discussing the corresponding character types.

### Example 1: Xianxia Fantasy — Protagonist Shen Mu

```markdown
### Basic Information
- **ID**: shen-mu
- **Name**: Shen Mu
- **Story Role**: protagonist
- **Appearance**: Lean build, rough hands, old scar on the left eyebrow.
- **Background**: Son of a hunter at the foot of Mount Cangwu. Parents died when demonic beasts attacked the village. Adopted by the old shopkeeper of the Green Gang.
- **Linguistic Features**: Speaks with the rough tone of the docks; swears when anxious, catchphrase "His grandma's".

### 6-Layer Cognitive Model
- **Worldview**: The strong make the rules, the weak follow them. Complaining is useless; only getting stronger works.
- **Self-Identity**: Thinks of himself as calm, rational, and planning before acting, but in reality, when friends/family are in danger, his first reaction is to charge in; the rationality is an afterthought.
- **Values**: Will not betray brothers. Will not oppress civilians. Can use opponents but won't insult their dignity.
- **Capabilities**: Can sense the source of killing intent within ten paces; can draw an eighty-pound bow with one hand, three rapid-fire shots without missing; photographic memory but needs time to recall.
- **Skills**: Street survival skills learned at the bottom of the Green Gang (distinguishing the number of people by footsteps, identifying status by clothing); learned abacus and bookkeeping from an accountant.
- **Environment**: Grew up working at the docks, has seen all walks of life. Currently staying in a side room given by the Green Gang's old shopkeeper, located in an alley near the docks.

### Plot Resume

#### Vol 1 Ch 3
- **Action:** Killed Zhao Hu, the boss of Black Wind Stronghold, and rescued kidnapped villagers.
- **Relationship Changes:** Became hostile with the remnants of Black Wind Stronghold; rescued villager Uncle Zhang owes him a life.

#### Vol 1 Ch 5
- **Action:** Refused the recruitment of the Green Gang's old shopkeeper, choosing to track the source of the demonic beasts alone.
- **Relationship Changes:** Relationship with the Green Gang changed from cooperative to distant; Lin Yan secretly keeping an eye on his movements.
```

### Example 2: Urban Supernatural — Supporting Character Lin Yan

```markdown
### Basic Information
- **ID**: lin-yan
- **Name**: Lin Yan
- **Story Role**: supporting
- **Appearance**: Early thirties, dressed unassumingly, walks without a sound, a face you wouldn't remember even if you looked twice in a crowd.
- **Background**: Grew up in a shantytown in Jiangcheng. Father went to prison, mother worked in a factory. Started roaming the streets at thirteen.
- **Linguistic Features**: Speaks without profanity but every sentence has a sting, catchphrase "What do you think?"

### 6-Layer Cognitive Model
- **Worldview**: There is no black and white in this world, only "those you can't afford to mess with." Neither the Special Management Bureau nor the Dark Web are good, but those who oppose them end up worse.
- **Self-Identity**: Thinks of herself as a cautious middleman who doesn't pick sides, doesn't stand out, and lives long. But occasionally helps people when she shouldn't — and can't explain why afterward.
- **Values**: Won't touch drugs or human trafficking. Can sell intel but not lives. Favors owed must be repaid.
- **Capabilities**: Can remember the face and voiceprint of someone she met once, and recognize them three years later. Lockpicking — from regular padlocks to electronic combination locks, all opened within three minutes. Disguise — after changing attire and gait, acquaintances walk right past without recognizing her.
- **Skills**: A living map of Jiangcheng — knows all surveillance blind spots, dark alleys, and abandoned buildings. Learned lockpicking for three months from an old locksmith, then practiced on her own.
- **Environment**: Ran errands and delivered goods for the Dark Web for three years; knows too much that shouldn't be known. The Special Management Bureau doesn't know she exists. Now lives in a rental room in the south of the city, with a window facing the back alley, ready to escape at any time.
```

---

## Two-Step Final Check (Perform after discussion)

After everything is discussed and written into the file, perform a two-step self-check. Do not proceed to the next phase if it doesn't pass.

### Step 1: Format Check

- [ ] Is the filename the pinyin ID, not Chinese?
- [ ] Is the `Story Role` one of the three (protagonist / antagonist / supporting)?
- [ ] Are the basic information fields complete (ID / Name / Role / Appearance / Background / Linguistic Features)?
- [ ] Do all six layers have values, with no empty fields?

### Step 2: Usability Check

| Check Item | How to Check | If Failed Action |
|--------|--------|-----------|
| Behavior inferable | Give the character an unexpected scenario; can you state their reaction? | Cannot state → Return, setting not specific enough |
| Protagonist true flaw | Will the flaw cause the character to make a wrong choice in chapters 3-5? | Fake flaw → Return and rewrite |
| Supporting self-driven | Without the protagonist, does the supporting character still have things to do? | Tool character → Return and add self-driven goal |
| Capabilities bounded | Does each capability have a clear output ceiling? | "Very strong" no boundary → Return and add limits |
| Skills sourced | Does each skill explain where it was learned? | No source → Return and supplement |
| Consistent with world | Do the origin and situation correspond to a place in the worldview? | Disconnected → Return and adjust |

---

## Step 3: Character Live Test (Scenario Roleplay Test)

After passing the format and usability checks, let the character **come alive** in a scenario. The Agent dynamically generates test scenarios based on the character's 6-layer settings, and then answers in character: what they choose, what they do, what they say. The author judges if it's "in character".

**Rule:** The scenario grows from the character's specific settings — if they have a Layer 3 value conflict, the scenario should press on that conflict. If they have a Layer 1 worldview belief, the scenario should clash with that belief. Do not run generic questions.

### Process

1. Agent reads the character's 6 layers + linguistic features.
2. Agent generates **3 test scenarios** based on the settings (see examples below), each with a clear question.
3. Agent answers in character — providing the character's choice, action, and exact words.
4. Author evaluates each one: "In character" / "Not quite" / "Completely wrong".
5. If there's a "Not quite" or "Completely wrong" → Agent traces back to pinpoint which layer the deviation occurred in → Return to the corresponding layer for further discussion → Retest the scenario after modification.

### Scenario Generation Principles

| Test Dimension | Target Layer | Where the Scenario Grows From |
|---------|--------|----------------|
| Value conflict | Layer 3 | Grows from the character's value entries — if they have "don't betray brothers", make the scenario a choice between brothers and justice. |
| Worldview impact | Layer 1 | Grows from the character's worldview beliefs — if they believe "the strong make the rules", have them witness the weak defeating the strong. |
| Self-identity dissonance | Layer 2 | Grows from the gap between self-identity and actual behavior — if they think "I am a Demon Lord", make them save someone they shouldn't. |
| Situation reaction | Layer 6 + language | Grows from origin and situation — if they grew up on the docks, the scenario involves a conflict with officials at the docks. |
| Capability limit | Layer 4 | Grows from capability ceilings — if they have a limit, the scenario pushes past that line. |

### Example

> **Character: Shen Mu (Xianxia, dock background, believes the strong make the rules)**
>
> Test Scenarios Generated by Agent:
>
> Scenario 1 (Pressing Layer 1 Worldview): You see an unarmed mortal pointing at a Golden Core cultivator's nose and cursing. The cultivator raises his hand to kill. No one around dares to interfere. What do you do? What are you thinking?
>
> Scenario 2 (Pressing Layer 3 Values + Layer 2 Self-Identity): You discover that the Green Gang's old shopkeeper, whom you owe a favor, is selling demonic beast cubs to rogue cultivators practicing evil arts. You promised you would repay the life you owe him. He tells you to keep watch. What do you do?
>
> Scenario 3 (Pressing Layer 6 Situation + Language): You return to the docks injured, only to find your side room has been taken by a merchant from out of town. He throws a bag of money at you and tells you to get lost. You are surrounded by dock workers you know. What do you say, and what do you do?
>
> Agent answering Scenario 1 as Shen Mu:
> Choice: Rushes up to pull the mortal away, standing between them.
> Action: Pushes the mortal three steps back with his left hand, right hand on his bowstring, doesn't draw an arrow. Eyes fixed on the cultivator's hand.
> Words: "He can't even take one slap from you. If you want to kill, kill, but don't dirty my ground at the docks."
> Thoughts: It's none of my business if this guy is looking for death, but if a cultivator kills someone at the docks and the government seals the port, where the hell am I going to fish?
>
> Author's judgment: "In character." → Pass.
>
> If the author says "Not in character — Shen Mu wouldn't meddle" → Agent traces back: There's a conflict in Layer 1 Worldview — he believes "the strong make the rules", but his behavior chose to intervene. Is Layer 1 inaccurate, or is there an unexpressed self-identity in Layer 2 (he thinks he's cold-blooded but actually has a bottom line)? → Discuss further, then retest Scenario 1.

### Live Test Passing Standard

If all three scenarios are judged "In character" by the author → The character setting is usable. You don't need every detail to be perfect; as long as the character's reactions in key conflicts are predictable and consistent, it's considered a pass.