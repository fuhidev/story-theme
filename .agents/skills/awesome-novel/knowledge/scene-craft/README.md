# scene-craft — Scene Writing Methodology

> **Purpose: After the four-step conversion, inject into prompts for direct use by AI, no author decision required**

The methodology documents in this directory are **automatically loaded on demand by the prompt-crafter** during the writing loop. After going through the four-step conversion method (Anchor Character → Anchor Information Gap → Anchor Emotional Pacing → Fuse Output), they are injected into the Output·Writing Guidelines.

**Do not** ask the author "what technique to use for this dialogue" - the choice of scene-level writing techniques is an execution-level operation, not a creative decision.

## Loading Mechanism

```
Input·Scene Raw Material completely filled
  │
  ├─ Regular scene type → scene-craft/{type}/universal.md
  │                  + scene-craft/{type}/{genre}.md (overwrites if it exists)
  │
  ├─ Special check → appearance/ (when a new character appears / appearance changes)
  │              inner-mono/ (during major events / emotional fluctuations)
  │
  └─ Output → Four-step conversion → Inject into Output·Writing Guidelines
```

## Directory Structure

```
scene-craft/
├── README.md
├── index.md
├── prose/             Prose techniques (always loaded)
├── pov/               POV switching (always loaded)
├── death-scene/       Character death / exit
├── dialogue/          Dialogue scenes
├── fight/             Combat scenes
├── appearance/        Character appearance description
├── inner-mono/        Psychological activity
├── environment/       Environment description
├── group-scene/       Group scenes
└── transition/        Transition scenes
```

## Rules for Adding New Files

1. One directory per scene type, containing `universal.md` (universal) + `{genre}.md` (specialized overwrite).
2. Each technique starts with a `[Tag]`, facilitating the prompt-crafter to match based on scene events.
3. Example sentences must satisfy three elements (subject + action + dialogue/result + emotional hint).
4. **No need to write "when to use" in the file - automatically detected by the prompt-crafter** (scene type detection or special condition trigger).
