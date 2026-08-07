# updater-archive-sop

Standard Operating Procedure for lore-keeping during archiving.

## I. Input Check

| Check Item | Pass Condition | Failure Handling |
|--------|---------|---------|
| `.agent/task/archive-order.md` | Exists, contains vol/chapter numbers | Report error to novel-agent |
| `.agent/{chapter}-draft-ai.md` | Exists | Skip diff, continue other updates |
| `archives/vol-{N}-ch-{M}*.md` | Exists | Report missing main text |
| `settings/character-setting/` | Readable/Writable | If not exists, create directory |
| `settings/timeline.md` | Readable/Writable | If not exists, create file |
| `.agent/status.md` | Exists | Report error to novel-agent |

## II. Pre-Archive Check

| Check Item | Action |
|--------|------|
| Main text draft exists? | Does `archives/vol-{N}-ch-{M}-*.draft.md` exist? If missing → **STOP**, main text has not been generated yet |
| chapter.md complete? | Do memo + emotional_design have values? If missing → Return to fill in chapter outline |

## III. Archiving Process

### Step 1: Finalize Main Text

1. If `.agent/{chapter}-draft-ai.md` does not exist, copy the current draft as an AI original snapshot.
2. Determine if an anti-AI flavor version exists under `archives/`:
   - **If `.anti-ai.md` exists** → Take it as the final version, rename to `.md`:
     `archives/vol-{N}-ch-{M}-{slug}.anti-ai.md` → `archives/vol-{N}-ch-{M}-{slug}.md`
     Then delete `.draft.md`
   - **If only `.draft.md` exists** → Rename to remove the `-draft` tag:
     `archives/vol-{N}-ch-{M}-{slug}.draft.md` → `archives/vol-{N}-ch-{M}-{slug}.md`
     <!-- In this path, draft.md == final version, the draft-ai.md snapshot saved in step 1 is consistent with the final content, self-consistent -->
3. Verify the archived file content is correct.

### Step 2: Update Character Status + Emotional Arc

1. Read the main text, extract the characters appearing in this act.
2. For each appearing character:
   - If a character file already exists → Append the status changes for this act.
   - If a new character (appears in text but no setting file) → Create a character file, marked `[auto-extracted]`.
3. Append status history format:
   ```markdown
   ## vol-{N}-ch-{M} Status Changes
   - **Location:** Character's current location
   - **Status:** Brief description of character's current status
   - **Interpersonal Relationship Changes:** xxx
   - **Capability/State Changes:** xxx
   - **Key Dialogue/Behavior in this Act:** "..."
   ```
4. Append plot resume (follows status history):
   ```markdown
   ### Plot Resume
   #### Volume {N} Chapter {M}
   - **Action:** {What the character actually did in this act — kill/choose/save/betray etc., including target and result}
   - **Relationship Change:** {What qualitative change happened to the relationship with whom, leave blank if none}
   ```
5. Add emotional arc (follows plot resume):
   ```markdown
   ### Emotional Arc
   #### Volume {N} Chapter {M}
   - **Emotional State:** Angry/Suppressed/Relieved/Expectant/Fearful/Warm/Determined
   - **Trigger Event:** Trigger event description
   - **Intensity:** 7/10
   - **Arc Direction:** Rising/Falling/Flat/Turning
   - **Expression Method:** Physical reaction/Behavior/Dialogue/Environmental interaction
   ```

### Step 2.5: Creature/Monster Detection

1. Read the main text, extract animals, monsters, or creatures appearing or mentioned in this chapter.
2. Compare with the "Creatures and Monsters" section in `settings/world-setting.md`.
3. If a creature is found not in the list → Show to the author for confirmation:
   > "The monster [{Monster Name}] appeared in this chapter but is not in the creature list of world-setting.md. Should it be added?"
4. Author confirms → Append to the creature list in world-setting.md.

### Step 3: Timeline Update

1. Extract key events from this chapter (changing the situation, character cognition, relationship changes, etc.).
2. Append to `settings/timeline.md`:
   ```markdown
   | Chapter | Event | Impact |
   |------|------|------|
   | vol-{N}-ch-{M} | {One-sentence event} | {Impact on subsequent story} |
   ```

### Step 5: Main Text Consistency Check (L2 Compliance)

Perform content compliance verification on the final main text during archiving:

1. **Setting Compliance**
   - Worldview is consistent (no contradictions)
   - No OOC (character behaviors match settings)
   - No taboo content for the genre
   - No continuity breaks with previous text

2. **Chapter Outline Fulfillment**
   - Did every item in `required_changes` occur in the main text
   - Is the payoff fulfilled
   - Are prohibitions obeyed

3. **Hook Fulfillment**
   - Newly planted hooks have `seed_text` that can be extracted
   - Resolving hooks have clear paragraphs
   - Hook weight matches the `payoff_plan`

If there are violations → Annotate them in status.md for the author to fix next time, do not block the archiving.

### Step 6: Collect Writing Feedback

First ask the author (or extract from conversation):

> "While writing this chapter, did you have any writing requirements, editing habits, or discover areas the chapter outline didn't consider? Just tell me in a few categories."

Guide the author to review three types of feedback:

1. **Writing Requirements** — Your specific preferences for style, rhythm, descriptions, and dialogue ("The dialogue is too wordy", "Action descriptions aren't detailed enough").
2. **Anti-AI Modifications** — Which AI-flavored expressions did you edit out (fatigue words, sentence templates, meta-narratives).
3. **Chapter Outline Omissions** — Missing content or directional deviations from the chapter outline discovered during actual writing.

If the author says "nothing special", then automatically extract it from the diff between the AI snapshot vs final main text.

### Step 6.5: Record AI Flavor Grade

After writing feedback, record the anti-AI quality data for this chapter to track trends later:

```markdown
## AI Flavor Grade Record
- **Grade:** Light / Medium / Heavy
- **Banned Word Density:** X times/thousand words
- **Parallel Paragraph Count:** X paragraphs
- **Psychological Word Ratio:** X%
- **Dialogue Tag Density:** X%
- **Average Sentences per Paragraph:** X sentences
- **Repetitive Description Density:** X times/thousand words
- **Original Word Count:** XXXX
- **Modified Word Count:** XXXX (Change ±X%)
```

Data source: Cite the scoring results outputted by reader-review, or extract from the Phase 4 report of anti-ai.md. If neither exists, skip without calculating.

### Step 7: Merge Dynamic Memory (Categorized Writing)

Process the feedback collected in Step 6 into three categories:

**① Writing Requirements** → Append to `.claude/memory/writing-memory.md`
```markdown
- **Original Text:** {Author's original keywords}
- **Conclusion:** {Actionable writing guideline}
- **Scene:** {Under what circumstances it applies}
- **use_count:** 1
```

**② Anti-AI Modifications** → Semantically merge into `.claude/knowledge/anti-ai.md`
- Read AI snapshot vs final text, extract modification patterns.
- Perform semantic merge with existing rules:
  - Completely identical → Skip
  - Semantic duplication → Merge into one, keep the better phrasing
  - Overlapping scenes → Expand the scene scope of the existing entry
  - Conflict → STOP, show to author for confirmation
- Append content marked with `[writer-preference]`

**③ Chapter Outline Omissions** → Append to `.claude/memory/chapter-memory.md`
```markdown
- **Original Text:** {Author's exact words or omissions found via diff}
- **Conclusion:** {What to pay attention to for the next chapter outline}
- **Scene:** {Applicable phase}
- **use_count:** 1
```

Before merging, show to the author to confirm if the categorization is accurate.

### Step 7.5: Report Learning Results

Output summary of this memory merge:
- ✏️ Writing requirements: Added N items
- 🤖 Anti-AI rules: Added N items / Merged M items / Skipped N items
- 📋 Chapter outline omissions: Recorded N items

### Step 8: Hooks Health Check

After archiving this chapter, execute for characters with hook references:
- Are high-priority hooks unmentioned for over 5 chapters
- Are normal hooks unmentioned for over 3 chapters
- Are there continuous weak hooks for over 3 chapters
- Is there at least 1 strong hook every 5 chapters

If abnormalities exist → Prompt the author in status.md or conversation.

### Step 9: Stagnation Detection

Check if there has been substantive progress in the last 3 chapters:
- Has the core conflict advanced
- Have character relationships changed
- Are there new information / new suspense

If no substantive progress for 3 consecutive chapters → Prompt the author: "The last 3 chapters have progressed slowly, is it necessary to adjust the rhythm?"

### Step 10: Volume Boundary Detection

Read the `chapters/` directory, filter the chapter files for the current volume:
- Not entirely completed → Update status.md, move `current_chapter` forward
- Entirely completed → Update status.md: `last_volume_completed = true`, `current_phase = review`. Output volume completion report:

```text
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
  Volume {N} "{title}" all {M} chapters completed
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Archived chapters: {List chapter by chapter}

Next step options:
1. Plan Volume {N+1}
2. Review the entire volume
3. Modify a certain chapter
```

### Step 11: Status Advance + Cleanup

- Update `.agent/status.md`: phase→`archive`, last_archived→current chapter number
- Delete `.agent/{chapter}-draft-ai.md`
- Delete `.agent/task/archive-order.md`

## IV. Acceptance Checklist

- [ ] Writing feedback collected (author confirmed or auto-extracted from diff)
- [ ] Main text draft tag removed as final version
- [ ] Status + emotional arc of all appearing characters updated
- [ ] Creature/monster detection completed + author confirmed
- [ ] Possessions/experiences updated (if changed)
- [ ] timeline appended with events from this chapter
- [ ] Main text consistency check completed (settings/outline/hooks)
- [ ] Three types of memory categorized and merged: Writing Requirements / Anti-AI / Outline Omissions
- [ ] Showed to author for categorization confirmation before merging
- [ ] Learning results report outputted
- [ ] hooks health check executed
- [ ] Stagnation detection executed
- [ ] Volume boundary detection executed + report outputted
- [ ] status.md advanced
- [ ] AI snapshot cleaned up
- [ ] order file cleaned up
