# updater-setting-sop

Standard Operating Procedure for setting changes during planning.

## I. Input Check

| Check Item | Pass Condition | Failure Handling |
|--------|---------|---------|
| `.agent/task/setting-update-order.md` | Exists | Report error to novel-agent |
| Target file specified in order | Exists or can be created | Report path error |

## II. Order Parsing

`setting-update-order.md` structure:

```yaml
type: character | world | timeline | genre | style | memory
action: create | modify | delete
target: settings/character-setting/{id}.md  # or other file paths
content: |
  # Content to write/append (structured)
reason: Required by author/volume outline/chapter outline
```

## III. Setting Update Process

### Scenario A: Add New Character

1. Check if the ID under `settings/character-setting/` is unique
2. If conflict → Report to author to confirm if it should be overwritten
3. Create character file, use standard template:

   ```markdown
   # {Character Name}

   ## Basic Information
   - **ID:** {Unique Identifier}
   - **Debut Volume/Chapter:** vol-{N}-ch-{M}
   - **Faction:** {Faction}

   ## Character Positioning
   - **Core Motive:** ...
   - **Relationship with Protagonist:** ...

   ## Abilities/Traits
   - ...

   ## Appearance Record
   - vol-{N}-ch-{M}: First appearance
   ```

4. If this character is related to other characters → Synchronously append the relationship to the relevant character files

### Scenario B: Modify Worldview/Genre/Style

1. Read existing files, confirm modifications are not contradictory
2. If new content conflicts with existing content:
   - Minor inconsistency → Append explanation, mark `[updated]`
   - Fundamental contradiction → STOP, show conflicting points to the author
3. No conflict → Append or replace specified paragraphs

### Scenario C: Append Timeline Event

1. Read existing content in `settings/timeline.md`
2. Insert new event in chronological order
3. If the event involves a character → Synchronously mark it in the character file

### Scenario D: Directly Modify Memory

1. Read target file in `.claude/memory/`
2. Append content marked with `[writer-preference]`
3. Do not run diff (rules specified by the author take effect directly)

### Scenario E: Delete Setting

1. Confirm deletion scope
2. Check if other files reference the deleted content
3. Has associated dependencies → Show reference chain to author for confirmation
4. No dependencies or already confirmed → Execute deletion

## IV. Consistency Check (Universal)

Check after any setting change:

- [ ] New content does not contradict existing files under `settings/`
- [ ] New character ID does not duplicate existing characters
- [ ] Worldview modifications do not cause logical contradictions in already written chapters (Mark potentially affected chapters)
- [ ] memory modifications do not mix `[community-defaults]` and `[writer-preference]` markers

## V. Acceptance Checklist

- [ ] Changes executed according to order requirements
- [ ] Newly created files formatted correctly
- [ ] No unresolved conflicts (all conflicts shown to author)
- [ ] Associated updates (e.g., new character → relationship sync) executed
- [ ] order file cleaned up
