# Writing Memory Recording skill

## Process

Capture Timing → Record → Fallback Cleanup

## 1. Capture Timing

The following situations are considered feedback that needs to be recorded:

| Timing | Judgment Standard | Example |
|------|---------|------|
| Explicit denial from author | Author says "wrong", "bad", "not like this" and provides an alternative direction | "This conflict is too abstract, can't write a concrete scene" |
| Author proactively proposes rules | Author says "always do this from now on", "remember this" | "Don't use visual descriptions for this type of scene, use tactile" |
| Author corrects accepted content | Author goes back to modify confirmed outputs | After outline is confirmed, author says "changing to XXX is more reasonable" |
| Author provides positive examples | Author says "this is right", "just like this" or points to reference materials saying "want this effect" | "This emotional progression is very good, keep this for this type in the future" |

**Do NOT record as memory:**
- Temporary, casual rhetorical questions from the author ("Is this okay?", "What do you think?")
- Purely functional operational confirmations ("Is the format correct?", "Is the file written?")
- Author explicitly stating "let's just write it like this for now, talk about it later"

## 2. Record

### 2.1 Recording Subject

Each agent directly appends to the corresponding file under `memory/` during the conversation:

| Agent | Target File | Append Method |
|-------|---------|---------|
| volume-planner | volume-memory.md | Append immediately after author confirmation/correction |
| chapter-planner | chapter-memory.md | Append immediately after author confirmation/correction |
| prompt-crafter | prompt-memory.md | Append immediately after author confirmation/correction |
| writer | writing-memory.md | Triggered indirectly through reader feedback |
| reader | writing-memory.md | Append immediately after author feedback confirmation |
| updater | All | Write missed items during fallback cleanup |
| novel-agent | All | Sync accumulated memories when dispatching sub-agents |

### 2.2 Recording Timing

After receiving recordable feedback, **complete the append before the current conversation ends**. Do not interrupt midway through the conversation to record—wait until the current step is completed before writing.

### 2.3 Recording Format

Strictly follow the entry format of `memory-format-spec.md`. Key principles:

- **Original Text** should retain the keywords of the author's statement, do not over-transcribe
- **Conclusion** must be actionable guidance, not empty words
  - ✅ "Core conflicts must have a specific opposing party, do not use abstract concepts like 'fate' or 'society'"
  - ❌ "Conflicts should be written specifically" (too vague)
- **Scene** describes the trigger conditions
  - ✅ "When determining the core conflict, the author felt the opposing force was too abstract"
  - ❌ "Volume outline discussion" (too vague)

### 2.4 Duplicate Check

Check for duplicates before appending: If there is already a similar entry in the same file, and the conclusion and scene are consistent → skip and do not append. Append only when the new feedback differs from existing entries in conclusion or applicable scene.

### 2.5 Auto-Summarization

If a single file exceeds 50 entries → mark as `(compressed)`, and the updater will compress it when processing:
- Keep the most recent 30 complete entries
- Group the remaining entries by (Domain + Type), compress each group into 1 summary entry, labeled `(compressed.YYYY-MM-DD)`

## 3. Fallback Cleanup

The novel-agent dispatches the updater to perform memory fallback after sub-agent tasks are completed:

1. Read all memory files, check if entry formats are correct (missing mandatory fields → fill or delete)
2. Compress files with over 50 entries
3. Process permanent memory in conjunction with the Promotion/Demotion logic in §4
4. Ask the author, "Is there anything else to record?"—if yes, append it

## 4. Permanent Memory Management

### 4.1 Usage Tracking

Each agent increments the `use_count` when referencing a memory entry:

- After the agent reads the memory file, for each memory actually **applied to the output**, increment `use_count` +1 and update `last_used`
- Within the same writing phase (same chapter outline/prompt/main text), referencing the same entry multiple times only counts as 1
- No need to change the count midway through the conversation—update uniformly before the agent finishes the current step

### 4.2 Promotion Process (Executed by Updater)

Read `memory/*.md`, for each entry:

```
if entry.use_count >= 4:
    1. Remove this entry from memory/*.md
    2. Append to .claude/knowledge/permanent-memory.md
       → Keep all original fields
       → Append line `[promoted YYYY-MM-DD]`
    3. Record promotion log (entry summary + promotion date)
```

Promotion condition confirmation:
- `use_count >= 4` and the conclusion is clear and reusable
- Does not conflict with existing entries in permanent-memory.md (if same conclusion but different scene, merge scene descriptions)

### 4.3 Demotion Process (Executed by Updater)

Read `.claude/knowledge/permanent-memory.md`, for each entry:

```
if entry.last_used was not updated in this sweep:
    entry.skip_count = (entry.skip_count or 0) + 1

if entry.skip_count >= 3:
    1. Mark this entry as a candidate for removal
    2. Show to author for confirmation: "Entry '{summary}' hasn't been referenced for 3 consecutive cycles, remove it?"
    3. Author confirms → Delete entry
    4. Author denies → Reset skip_count = 0
```

> Initialize `skip_count: 0` when first added to permanent memory.
