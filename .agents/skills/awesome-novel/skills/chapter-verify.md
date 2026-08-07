# Verify Chapter Outline skill

After the chapter outline is produced, check its quality and completeness item by item. Only when all items pass can it enter the prompt generation phase. For acceptance standards, refer to `.claude/knowledge/chapter-setting-style.md`.

## Process

Structured feedback (show to author) → Checklist self-check → Quick sniffing → AI flavor self-check

## I. Show Structured Feedback to the Author

After completing the steps according to `chapter-setting-style.md` — show it to the author using the format in §Step 1 of the Acceptance Process. It only passes if the author explicitly says "Correct". "Pretty much" or "Up to you" do not count.

If not passed → Return to STEP 3 to modify.

## II. Checklist Self-check

After completing the steps according to `chapter-setting-style.md` — verify the 24 items in the checklist from §Step 2 of the Acceptance Process one by one. If any item is unqualified, return it for modification.

## III. Quick Sniffing

After completing the steps according to `chapter-setting-style.md` — execute §Step 3 of the Acceptance Process.

## IV. AI Flavor Self-check and Removal

Scan the full text according to the "6 Fatal Patterns" and "Empty Adjectives Quick Lookup" in the style layer of `chapter-setting-style.md`. If any rule is hit → check again after modification; it only passes when confirmed that all are cleared.

## V. Confirmation

After all checks pass, write to `chapters/vol-{N}-ch-{M}.md`, `status` → `outline`
