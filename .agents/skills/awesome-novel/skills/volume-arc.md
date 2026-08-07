# Main Plot Breakdown skill

Executed only during the initial planning. Subsequent volume planning skips this skill.

## Process

Determine Author Type → Overall Main Plot → Determine Endpoint → Find Breakpoints → Determine Each Volume's Conflict → Write → Three-Way Verification

## 1. Determine Author Type

Ask: "Do you currently have a complete concept for the entire book's story direction—from beginning to end? Or just ideas for the first volume, making it up as you go later?"

- **Type A: Knows everything** → Go through the full process 2→7
- **Type B: Only knows the beginning** → Skip Section 3 (Determine Endpoint) and Section 4 (Find Breakpoints); Section 2 (Overall Main Plot) narrows to "First Volume Direction"; Section 5 only determines the first volume's conflict, subsequent volumes marked as "TBD"
- **Somewhere in between** → Follow Type A, subsequent volume conflicts marked as "TBD"

## 2. Overall Main Plot

Extract the three elements from the author's answer (**Who + Pursuing what + Opposing what**), condense it into one sentence for the author to confirm.

- Type A: Standard — the core conflict of the entire book that cannot be answered without finishing all volumes
- Type B: Only discuss first volume direction — Who + Wants to do what + Obstructed by what

## 3. Determine Endpoint

> Type B skips. Endpoint and breakpoints marked "TBD", enter Section 5 directly.

Working backward from the ending, do not ask "how many volumes," ask for the "final act." Follow different branches based on the author's answer type (Has imagery / Has tone but no imagery / Completely clueless).

After the endpoint is determined, verify consistency with the overall main plot: Contradictions might be sources of tension, confirm the author's intent.

## 4. Find Natural Breakpoints

> Type B skips.

Fundamental changes between the starting point and the endpoint. The Agent can propose deductions based on the genre + overall main plot for the author to confirm.

## 5. Determine Each Volume's Conflict

Confirm volume by volume, do not list them all at once.

| Field | Standard | Unqualified Example |
|------|------|-----------|
| Title | 2-4 words, summarizing the core event of the volume | "Volume 1" (Equals not giving one) |
| Core Conflict | **Where the main plot is at +** Who + Doing what + Obstructed by what. It's a subset of the main plot, not starting from scratch | "Protagonist continues investigating" (No confrontation); "Protagonist gets involved in an international conspiracy" (Irrelevant to main plot) |
| Estimated Chapters | Estimated based on genre pacing, allow ±30% | 0 or empty |

Handling of subsequent volumes:
- Type A: Later volumes must have at least one sentence of core conflict + floating chapter count, to be detailed when writing reaches them
- Type B: Only write the first volume, all subsequent volumes marked "TBD"

## 6. Output and Write

Write to `story.md` in format:

Type A contains the book's core conflict + structural type + total volumes + each volume's core conflict.
Type B core conflicts are marked "TBD", only the first volume has content.

## 7. Acceptance: Three-Way Verification

All three verifications must pass to proceed to determining volume direction:

1. **Overall Main Plot → Volume by Volume:** Pointing to the overall main plot, ask for each volume "Which step of the main plot is this volume?" — Can answer ✅, Cannot answer or is far-fetched ❌
2. **Volume Sequence → Complete Path:** String together the core conflicts of all volumes; is it a complete storyline without breaks or skipped steps?
3. **Volumes → Reverse Infer Overall Main Plot:** Can you guess the book's core conflict just by looking at the volume conflicts? Or do they point in different directions?

Type B: Verification 1 only verifies the first volume, Verification 2/3 pass automatically.

**Final Check:** Point to the overall main plot and ask "Without this volume, can the main plot be finished?" — Cannot be finished ✅
