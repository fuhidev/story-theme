# Writing Memory Format Specification

> Writing memory system: `memory/` is for dynamic memory (divided into 4 files by stage), `knowledge/` is for permanent memory (high-frequency entries after promotion). Each entry records the positive and negative feedback provided by the author during the writing process for reference in subsequent processes.

---

## 1. File Structure

```
.claude/
├── memory/ # Dynamic memory (real-time recording of each link)
│ ├── volume-memory.md # Volume link memory
│ ├── chapter-memory.md # Chapter link memory
│ ├── prompt-memory.md # Prompt word link memory
│ └── writing-memory.md # Text writing session memory
│
└── knowledge/
    └── permanent-memory.md # Permanent memory (high-frequency entries promoted from memory/)
```

## 2. File Header

Each file begins with a meta-information header:

```markdown
# {Stage} Writing Memory

> {Description of use}

**Related Agent:** {Agent list}
**Related Skills:** {Skill list}

---

## Entry List
```

## 3. Entry Format

Each memory contains the following fields:

| Field | Required | Description |
|------|------|------|
| **Stage** | Yes | Source stage: Volume Outline / Chapter Outline / Prompt Words / Text |
| **Type** | Yes | Positive Case / Negative Case / Rule |
| **Field** | Yes | Subdivided areas under this stage (see §4 of each document below) |
| **Original Text** | Yes | The author provides feedback on the original text and retains key sentences |
| **Conclusion** | Yes | Distilled into reusable rules or operational guidelines |
| **Scenario** | Yes | When does this rule apply |
| **Source** | Yes | The Agent that triggered this recording |
| **Date** | Yes | YYYY-MM-DD |

Complete entry:

```markdown
### {Serial number}. {Type}: {One sentence summary}

- **Stage:** {Stage name}
- **Type:** {positive case|negative case|rule}
- **Field:** {Field}
- **Original Text:** {Excerpt of original text from author’s feedback}
- **Conclusion:** {Refined rules}
- **Scenario:** {Applicable conditions}
- **Source:** {Agent name}
- **Date:** {YYYY-MM-DD}
- **use_count:** {Number of citations, used for promotion}
- **last_used:** {YYYY-MM-DD, last cited date}
```

> `use_count` and `last_used` are used to track the frequency of entry usage. They are incremented by the agent when referencing, and the updater performs promotion/demotion based on this when checking.

## 4. Entry Life Cycle

```
memory/ entry ──use_count >= 4──→ knowledge/permanent-memory.md
permanent-memory.md entry──3 consecutive sweeps Unused──→ Mark for removal → Delete after confirmation by the author
```

### 4.1 Promotion Conditions (memory → permanent)

| Conditions | Description |
|------|------|
| use_count >= 4 | This memory has been referenced more than 4 times by different agents/links |
| The conclusion is clear and reusable | Confirming the conclusion before promotion is a general rule rather than a one-time scenario |
| No conflict | No conflict with existing permanent memory |

Promotion operation: Remove the entry from `memory/*.md`, append to `knowledge/permanent-memory.md`, retain all original fields + `[promoted YYYY-MM-DD]` tag.

### 4.2 Downgrade Conditions (permanent → remove)

| Conditions | Description |
|------|------|
| `last_used` has not been updated in 3 consecutive sweeps | No agent has referenced this entry within 3 writing cycles |
| Author confirmation | Ask author for confirmation before removal |

Downgrade operation: Delete the entry after showing it to the author for confirmation. If the author thinks there is still value, reset `skip_count = 0` and keep it.

### 4.3 Usage Tracking Rules

- After the agent references an item of memory and applies it to the output, it increments the item's `use_count` and updates `last_used`
- Multiple references to the same item in the same writing session will only be counted once (to prevent the same session from being counted incorrectly)
- Check `last_used` when updater sweeps, and accumulate `skip_count` if it is not updated across links.