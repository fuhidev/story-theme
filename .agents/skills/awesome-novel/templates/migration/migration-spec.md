# Migration Specification — Migration from 2.x to 4.0

> Agents execute setting migration according to this specification. The field mapping table for each file defines the correspondence between old fields → new template locations.
> Acceptance criteria: Every required field in the new template can find a source in the old version, and the content is complete after migration.

## Field Status Markers

| Marker | Meaning |
|--------|---------|
| ✅ Fill directly | Old field value can be directly filled into the new location |
| 🔄 Induce | Agent reads old field content, then summarizes and refines to fill in the new sub-field |
| ⚠️ To be inferred | No direct corresponding field in the old version, Agent infers from context |
| ❌ New addition | Completely non-existent in the old version, requires author to supplement |

---

## §1 story.yaml → story.md

**Old path:** `story.yaml`
**New path:** `story.md`
**Template:** `templates/migration/story.md.template`

### Field Mapping

| New Template Location | Old Version Source | Migration Method | Acceptance |
|-----------------------|--------------------|------------------|------------|
| `# {title}` | `story.yaml → title` | ✅ Fill directly | Title is consistent |
| Meta Information → skill_version | — | ❌ New addition | Write `4.0` |
| Meta Information → Genre | `writing-style.yaml → genre_profile` | ✅ Fill directly | Fill if has value, mark "To be confirmed" if none |
| Meta Information → Tags | No direct field | ⚠️ Infer from story.yaml content | Can be empty |
| Meta Information → Status | — | ❌ New addition | Write "Writing" |
| Reference Paths → World-building Summary | `story.yaml → world_setting.summary` | ✅ Fill directly | Not empty |
| Reference Paths → Genre Summary | — | ❌ New addition | Mark "To be confirmed" |
| Reference Paths → Characters Summary | `story.yaml → characters.summary` | ✅ Fill directly | Not empty |
| Reference Paths → Writing Style Summary | `story.yaml → writing_style.summary` | ✅ Fill directly | Not empty |
| Main Storyline → Structure Type | No direct field | ⚠️ Infer from volumes count + content | At least one filled |
| Main Storyline → Total Volumes | Number of `volumes/*.yaml` files | ✅ Fill directly | Number is correct |
| Main Storyline → Core Conflict of the Book | `core_conflict` of each volume | 🔄 Summarize core conflicts of all volumes into one sentence | Not empty |
| Volume Planning → Volume N Title | `volume-{N}.yaml → title` | ✅ Fill directly | Every volume has it |
| Volume Planning → Volume N Arc Position | No direct field | ⚠️ Infer from `volume-{N}.yaml → summary` | Mark "To be confirmed" |
| Volume Planning → Volume N Core Conflict | `volume-{N}.yaml → core_conflict` | ✅ Fill directly | Every volume has it |
| Volume Planning → Volume N Estimated Chapters | Count of `volume-{N}.yaml → chapters_summary` | ✅ Fill directly | Number is correct |

### Acceptance

- `story.md` title line is correct
- At least the Genre in the three meta info fields has a value
- All 4 rows in the reference path table are not empty (Genre row marked "To be confirmed")
- Main storyline section: Structure type + Total volumes + Core conflict are all complete
- Volume planning: Correct number of volumes, each volume has at least title and core conflict
- No remnants of old story.yaml

---

## §2 world-setting.yaml → settings/world-setting.md

**Old path:** `settings/world-setting.yaml`
**New path:** `settings/world-setting.md`
**Template:** `templates/migration/world-setting.md.template`

### Field Mapping

| New Template Location | Old Version Source | Migration Method | Acceptance |
|-----------------------|--------------------|------------------|------------|
| Geography → Main Scenes | `world-setting.yaml → details.geography` | 🔄 Extract key place names/scenes from overall description | At least 1 main scene |
| Geography → Climate | `world-setting.yaml → details.geography` | 🔄 Extract climate characteristics from description | Not empty |
| Geography → Geographical Limitations | `world-setting.yaml → details.geography` | 🔄 Extract geographical limitations (like transport/terrain) from description | Not empty |
| Politics → Form of Rule | `world-setting.yaml → details.politics` | 🔄 Extract ruling structure from description | Not empty |
| Politics → Major Factions | `world-setting.yaml → details.politics` | 🔄 Extract at least 2 factions from description | At least 2 |
| Politics → Social Stratification | `world-setting.yaml → details.politics` + `sociology` | 🔄 Extract social hierarchy from description | Not empty |
| Politics → Cost of Disobedience | `world-setting.yaml → details.politics` + `rules` | 🔄 Extract from description | Not empty |
| Rules → World Level | `world-setting.yaml → details.rules` + `physics` + `biology` | 🔄 Extract from foundational physical/biological rules | Not empty |
| Rules → Social Level | `world-setting.yaml → details.rules` + `culture` + `sociology` | 🔄 Extract from social/cultural rules | Not empty |
| Rules → Personal Level | No such sub-field in old version | ⚠️ Infer from other sections or mark "To be confirmed" | Mark "To be supplemented" |

**Explanation:** The new template is more structured than the old version (has sub-fields). Agent reads the free-text paragraphs of the old version, understands them, and categorizes them into the new sub-fields. Contents of old fields like `culture`, `history`, `sociology` are distributed into Geography/Politics/Rules sections.

### Acceptance

- All three sub-fields of Geography have substantive content
- All four sub-fields of Politics have substantive content
- Three sub-fields of Rules: World level and Social level have content, Personal level can be empty but marked "To be supplemented"
- Compare with the old file to ensure no important setting points are lost

---

## §3 writing-style.yaml → settings/writing-style.md

**Old path:** `settings/writing-style.yaml`
**New path:** `settings/writing-style.md`
**Template:** `templates/migration/writing-style.md.template`

### Field Mapping

| New Template Location | Old Version Source | Migration Method | Acceptance |
|-----------------------|--------------------|------------------|------------|
| `## role` | `writing-style.yaml → role` | ✅ Fill directly | Not empty |
| `## role` (merged personality) | `writing-style.yaml → personality` | 🔄 Merge to the end of the role section | Not empty |
| `## core_principles` | `writing-style.yaml → core_principles.*` | 🔄 Merge all sub-fields into one list | Not empty, no duplicates |
| `## possible_mistakes` | `writing-style.yaml → possible_mistakes` | ✅ Fill directly | Not empty |
| `## depiction_techniques` | `writing-style.yaml → depiction_techniques` | ✅ Fill directly | Not empty |

**Explanation:** The new version uses markdown format (not YAML). The 5 sub-fields of the old `core_principles` (global_rules / natural_expression / description_vs_depiction / character_building / pov_consistency) are merged into one list and written into `## core_principles`. `personality` is merged to the end of the `## role` section. `genre_profile` → written into §8 genre-setting.md. `workflow` / `writing_model` → no corresponding location in the new version, skip during migration.

### Acceptance

- The ## role section has merged personality, content is not empty
- The ## core_principles contains contents of all sub-fields from the old version
- The ## possible_mistakes is fully migrated
- The ## depiction_techniques is fully migrated
- Compare with the old file to ensure no omissions

---

## §4 anti-ai.yaml → settings/anti-ai.md

**Old path:** `settings/anti-ai.yaml`
**New path:** `settings/anti-ai.md`
**Template:** `templates/migration/anti-ai.md.template`

### Field Mapping

| New Template Location | Old Version Source | Migration Method | Acceptance |
|-----------------------|--------------------|------------------|------------|
| Fatigue Words → Adverbs | Adverb words in `anti-ai.yaml → fatigue_words_zh` + `fatigue_words_en` | ✅ Fill directly | At least included |
| Fatigue Words → Verbs | Verb class in `anti-ai.yaml → fatigue_words_zh` | ✅ Fill directly | Not empty |
| Fatigue Words → Adjectives | Adjective class in `anti-ai.yaml → fatigue_words_zh` | ✅ Fill directly | Not empty |
| Fatigue Words → Conjunctions | Conjunction class in `anti-ai.yaml → fatigue_words_zh` | ✅ Fill directly | Not empty |
| Fatigue Words → Physical Reaction Templates | cliche_action/cliche_environment in `anti-ai.yaml → fatigue_words_zh` | ✅ Fill directly | Not empty |
| Sentence Structure Rules | `anti-ai.yaml` + `tic-patterns.yaml` | ✅ Fill directly | Reference path is correct |
| Rewriting Algorithms → Perception Word Removal | Perception word related rules in `anti-ai.yaml` | 🔄 Agent summarizes old rules | Not empty |
| Rewriting Algorithms → "le" Character Purification | "le" related rules in `anti-ai.yaml` | 🔄 Agent summarizes old rules | Not empty |

**Explanation:** The new version has clearer classifications; sub-categories of the old `fatigue_words_zh` are filled into the new categories.

### Acceptance

- Each category of fatigue words has substantive content
- Reference path for sentence structure rules is correct
- Rewriting algorithms have specific descriptions

---

## §5 hooks.yaml → settings/foreshadowing.md

**Old path:** `settings/hooks.yaml`
**New path:** `settings/foreshadowing.md`
**Template:** `templates/migration/foreshadowing.md.template`

### Field Mapping

| New Template Location | Old Version Source | Migration Method | Acceptance |
|-----------------------|--------------------|------------------|------------|
| Active Foreshadowing Table | `status=pending/mentioned` in `hooks.yaml → hooks[]` | 🔄 Extract as table row: description→Foreshadowing, introduced_in→Intro Chapter, payoff_timing→Expected Resolution, last_mentioned_chapter→Last Mentioned | All unresolved hooks in the old version appear |
| Resolved Foreshadowing Table | `status=resolved` in `hooks.yaml → hooks[]` | 🔄 Extract as table row: description→Foreshadowing, introduced_in→Intro Chapter, resolution_chapter→Resolution Chapter | All resolved hooks in the old version appear |
| Abandoned Foreshadowing Table | `status=abandoned` in `hooks.yaml → hooks[]` | 🔄 Extract as table row | All abandoned hooks in the old version appear |

**Explanation:** The old hooks.yaml has dozens of detailed fields (hook_type, priority, hook_strength, etc.), the new version only retains core summary information. Detailed metadata is not maintained here——it has been dispersed into the memo of each chapter's chapter.md.

### Acceptance

- The three tables cover all hooks in the old hooks.yaml, none missing
- Active/Resolved/Abandoned classifications are consistent with the old version
- Each item has at least a description and the chapter it is located in

---

## §6 character yaml → settings/character-setting/*.md

**Old path:** `settings/character-setting/{id}.yaml`
**New path:** `settings/character-setting/{id}.md`
**Template:** `templates/migration/character.md.template`

### Field Mapping

| New Template Location | Old Version Source | Migration Method | Acceptance |
|-----------------------|--------------------|------------------|------------|
| Basic Info → id | Filename (without extension) | ✅ Fill directly | Not empty |
| Basic Info → Name | `{character}.yaml → name` | ✅ Fill directly | Not empty |
| Basic Info → Story Role | `{character}.yaml → story_role` | ✅ Fill directly | Not empty |
| Basic Info → Appearance | `{character}.yaml → appearance` | ✅ Fill directly | Not empty |
| Basic Info → Background | `{character}.yaml → background` + `summary` + `age` + `occupation` | 🔄 Merge multiple fields | Not empty |
| Basic Info → Linguistic Traits | No such field in old version | ❌ New addition | Mark "To be supplemented" |
| Abstract Layer → Worldview | `{character}.yaml → cognition.l1_worldview` | ✅ Fill directly | Not empty |
| Abstract Layer → Self-Positioning | `{character}.yaml → cognition.l2_self_identity` | ✅ Fill directly | Not empty |
| Abstract Layer → Values | `{character}.yaml → cognition.l3_values` | ✅ Fill directly | Not empty |
| Practical Layer → Capabilities | `{character}.yaml → cognition.l4_core_abilities` | ✅ Fill directly | Not empty |
| Practical Layer → Skills | `{character}.yaml → cognition.l5_skills` | ✅ Fill directly | Not empty |
| Practical Layer → Environment | `{character}.yaml → cognition.l6_environment` | ✅ Fill directly | Not empty |
| Relationships | `{character}.yaml → relationships` | 🔄 Convert format item by item | Original relationship count is correct |
| Status History | `{character}.yaml → state_history` | ✅ Fill directly | Not empty (if history exists) |
| Emotional Arc | No such field in old version | ❌ New addition | Mark "To be supplemented" |

### Acceptance

- Each old character yaml corresponds to a new character md, count is consistent, none missing
- Cognitive 6-layer model fully migrated (old version has 6 fields, new version has 6 locations)
- Relationships converted item by item
- Background has merged multiple fields from the old version
- Linguistic traits and emotional arc are marked "To be supplemented"

---

## §7 volume yaml → volumes/volume-{N}.md

**Old path:** `volumes/volume-{N}.yaml`
**New path:** `volumes/volume-{N}.md`
**Template:** `templates/migration/volume.md.template`

### Field Mapping

| New Template Location | Old Version Source | Migration Method | Acceptance |
|-----------------------|--------------------|------------------|------------|
| `# Volume {N}: {Title}` | `volume-{N}.yaml → title` | ✅ Fill directly | Not empty |
| Core Conflict | `volume-{N}.yaml → core_conflict` | ✅ Fill directly | Not empty |
| Estimated Chapters | Count of `volume-{N}.yaml → chapters_summary` | ✅ Fill directly | Number is correct |
| Chapter List → Chapter Titles | `volume-{N}.yaml → chapters_summary[].title` | ✅ Fill directly | Every chapter has it |
| Chapter List → Conflict Events | `volume-{N}.yaml → chapters_summary[].summary` | ✅ Fill directly | Every chapter has it |

**Explanation:** The old `summary` and `main_events` fields have no direct location in the new template, Agent can integrate their core content into the volume's introductory paragraph.

### Acceptance

- Number of volumes is consistent with the old version
- Core conflict of each volume fully migrated
- Number of items in chapter list is consistent with the old chapters_summary
- Not marked "To be supplemented"

---

## §8 genre-setting.md (New)

**No such file in old version.** The `genre_profile` field in the old `writing-style.yaml` recorded the genre ID.

### Field Mapping

| New Template Location | Old Version Source | Migration Method | Acceptance |
|-----------------------|--------------------|------------------|------------|
| Selected Genre | `writing-style.yaml → genre_profile` | ✅ Fill directly | Fill if has value, mark "To be confirmed" if none |
| Genre Satisfaction | No such field in old version | ⚠️ Extract from genre-example library | Mark "To be confirmed" |
| Pacing Rules | No such field in old version | ⚠️ Extract from genre-example library | Mark "To be confirmed" |
| Cliches to Avoid | No such field in old version | ⚠️ Extract from genre-example library | Mark "To be confirmed" |
| Genre Taboos | No such field in old version | ⚠️ Extract from genre-example library | Mark "To be confirmed" |

**Explanation:** This file is new in 3.0. If the old version has `genre_profile`, Agent pre-fills it from the corresponding genre configuration under `knowledge/genre-example/`; if not, mark all as "To be discussed".

---

## §9 chapter yaml → chapters/vol-{N}-ch-{M}.md (Only archived)

**Old path:** `chapters/vol-{N}-ch-{M}.yaml` (Only `status: archived`)
**New path:** `chapters/vol-{N}-ch-{M}.md`
**Template:** `templates/migration/chapter.md.template`

### Field Mapping

| New Template Location | Old Version Source | Migration Method | Acceptance |
|-----------------------|--------------------|------------------|------------|
| `# Volume {N} Chapter {M}: {Title}` | `volume` + `chapter` + `title` | ✅ Fill directly | Not empty |
| Status | `status` | ✅ Fill directly | `archived` |
| Outline → Summary | `outline.summary` | ✅ Fill directly | Not empty |
| Outline → Key Points | `outline.key_points` | ✅ Fill directly | Not empty |
| Outline → Appearing Characters | `outline.characters` | ✅ Fill directly | Not empty |
| Outline → Scene Location | `outline.location` | ✅ Fill directly | Not empty |
| Outline → Time | `outline.time` | ✅ Fill directly | Not empty |
| Memo → Current Task | `memo.current_task` | ✅ Fill directly | Not empty |
| Memo → Reader Expectations → Emotional State | `memo.reader_expectation.state` | ✅ Fill directly | Not empty |
| Memo → Reader Expectations → Strategy | `memo.reader_expectation.strategy` | ✅ Fill directly | Not empty |
| Memo → Reader Expectations → Specifics | `memo.reader_expectation.detail` | ✅ Fill directly | Not empty |
| Memo → Payoff Plan → Must Resolve | `memo.payoff_plan.must_resolve` | ✅ Fill directly | Not empty |
| Memo → Payoff Plan → Must Suppress | `memo.payoff_plan.must_hold` | ✅ Fill directly | Not empty |
| Memo → Payoff Plan → Advance Without Closing | `memo.payoff_plan.partial_advance` | ✅ Fill directly | Not empty |
| Memo → Transition Functions | `memo.downtime_functions` | ✅ Fill directly | Not empty |
| Memo → Critical Choices | `memo.key_choices` | ✅ Fill directly | Not empty |
| Memo → Character Information Status | No such sub-section in old version | ❌ New addition | Mark "To be supplemented" |
| Memo → End of Chapter Changes | `memo.required_changes` | ✅ Fill directly | Not empty |
| Memo → Hard Constraints | `memo.prohibitions` | ✅ Fill directly | Not empty |
| Emotion Design → Main Emotion | `emotional_design.primary_mood` | ✅ Fill directly | Not empty |
| Emotion Design → Emotion Progression | `emotional_design.mood_progression` | ✅ Fill directly | Not empty |
| Emotion Design → Intensity Peak | `emotional_design.intensity_peak` | ✅ Fill directly | Not empty |
| Emotion Design → Intensity Level | `emotional_design.intensity_level` | ✅ Fill directly | Not empty |
| Emotion Design → Emotional Hook | `emotional_design.emotional_hook` | ✅ Fill directly | Not empty |
| Emotion Design → Reader Gain | `emotional_design.satisfaction_beat` + `micro_payoffs` | 🔄 Induce from both | Not empty |

### Migration Explanation

**narrative_pov / narrative_style:** The root level of the old chapter YAML may have these fields (e.g. `narrative_pov: "Lin Mo third-person limited POV"`), the new chapter.md template has no separate location. Agent writes its content to the end of Memo → Current Task, marked as `[POV]`.

**segments section:** Some old chapters have a `segments:` paragraph-level split——this is deprecated in the new version, paragraph-level splits are moved to the prompt layer. Skip during migration.

**cycle_position / suppression_stack:** Emotion cycle fields specific to InkOS, not present in the new version. Skip during migration.

**prompt_path / prompt_variant / archive_path:** Metadata fields of old chapter files, the new version manages file relationships through naming conventions. Skip during migration.

### Acceptance

- All archived chapters have been migrated, none missing
- All fields marked ✅ have content
- All fields marked ❌ are marked "To be supplemented"
- Total chapter count is consistent with the old version
- Skipped non-archived chapters are listed in the report

---

## §10 timeline file (New)

**No such file in old version.** The timeline is automatically appended by the updater during archiving, only need to create an empty file during migration.

**Template:** `templates/migration/timeline.md.template`

### Acceptance

- `settings/timeline.md` exists, header is correct

---

## Overall Acceptance Checklist

After executing all migration steps, Agent checks item by item:

### Structure Acceptance

- [ ] `story.md` exists, meta information filled
- [ ] `settings/world-setting.md` exists
- [ ] `settings/writing-style.md` exists
- [ ] `settings/genre-setting.md` exists
- [ ] `settings/anti-ai.md` exists
- [ ] `settings/foreshadowing.md` exists
- [ ] `settings/timeline.md` exists
- [ ] Character files under `settings/character-setting/` are consistent with the old version
- [ ] Number of volumes for `volumes/volume-{N}.md` is consistent with the old version
- [ ] All archived chapters under `chapters/` are migrated
- [ ] Main text in `archives/` fully copied
- [ ] Prompts in `prompts/` fully copied
- [ ] Old `.yaml` moved into `old/`, no remnants
- [ ] Deprecated files (author-intent.md, current-focus.md, drafts/, etc.) cleaned up

### Field Completeness Acceptance

For each migrated file, check item by item according to its § section acceptance conditions:

- [ ] Fields marked ✅ → All have content
- [ ] Fields marked 🔄 → Agent's summarized content is reasonable, no lost info
- [ ] Fields marked ⚠️ → Marked "To be confirmed" or "To be inferred"
- [ ] Fields marked ❌ → Marked "To be supplemented"

### Post-Migration Note

> After completing the migration, the `old/` directory retains the old files. Once confirmed correct, you can manually delete them with `rm -rf old/`.
> Fields marked as "To be discussed", "To be confirmed", "To be supplemented" will be gradually perfected in subsequent writing.
