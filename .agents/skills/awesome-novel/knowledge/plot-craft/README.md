# plot-craft — Plot Design Methodology

> **Purpose: To discuss with the author; AI displays options for the author to decide, not automatically injected.**

## Description

The methodological documents in this directory are read by **volume-planner / chapter-planner** during the volume/chapter outline planning phase, serving as reference materials when discussing with the author.

AI **cannot decide on its own** the plot direction, conflict types, or twist methods — these are core decisions at the creation level and must be confirmed by the author before implementation.

## How to Use

```
Volume Outline Planning
  ├─ Establish emotional direction → Show tragedy tropes/emotional pull methods, ask the author which they prefer
  ├─ Set conflict ladders → Show conflict escalation methods, ask the author how to design each layer
  ├─ Break down scene cards (First Chapter) → Show opening hook methods, let the author choose
  └─ Define core conflict → Show plot twist ideas, discuss whether they are needed

Chapter Outline Planning
  ├─ Set conflict ladders → Show conflict escalation/twist methods, discuss the turning point of this chapter
  ├─ Hooks operation → Show hook methods, discuss what hooks to use
  └─ Break down scene cards (First Chapter) → Show opening hooks, let the author choose
```

## Directory Structure

```
plot-craft/
├── README.md
├── index.md                           # Index
├── conflict-escalation.md (implicit)  # Four major techniques for conflict escalation
├── hook-techniques.md                 # Three ways to write hooks and suspense
├── tragedy-techniques.md              # Four tropes for tragedy and heartbreak
├── emotional-pull.md                  # Four layers of emotional pull + pacing control
├── opening-hooks.md                   # Five hooks to grab readers at the beginning
└── plot-twists.md                     # Three high-level formulas for plot twists
```

## Rules for Adding New Files

1. Every technique must have positive and negative examples + forbidden items; do not write vague theories.
2. Specify the applicable phase (volume outline/chapter outline/both) at the beginning of the file.
3. Add references in the action skill (refer to existing reference methods).
4. **Must explicitly state the trigger conditions for "discuss with the author" — not automatic execution.**
