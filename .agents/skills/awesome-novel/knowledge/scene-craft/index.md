# Scene Writing Methodology

> A guide to writing fiction organized by scene type. Each scene type has a directory containing a universal methodology + specializations for various genres.

## Directory Structure

```
scene-craft/
├── index.md                      # Index
├── README.md                     # Usage instructions
├── prose/                        # Prose techniques (always loaded)
├── dialogue/                     # Dialogue scenes
│   ├── universal.md              #   Universal dialogue methodology
│   ├── xianxia.md                #   Xianxia genre specialization
│   └── suspense-crime.md         #   Suspense/crime genre specialization
├── fight/                        # Combat/confrontation scenes
│   ├── universal.md              #   Universal combat methodology
│   ├── xianxia.md                #   Xianxia genre specialization
│   └── suspense-crime.md         #   Suspense/crime genre specialization
├── environment/                  # Environment/atmosphere description
│   ├── universal.md              #   ✅ Five senses / mood rendering / detail interaction
│   ├── xianxia.md                #   Xianxia genre specialization
│   └── suspense-crime.md         #   Suspense/crime genre specialization
├── appearance/                   # Character appearance description
│   └── universal.md              #   ✅ Anchors / dynamics / background story / indirect characterization
├── inner-mono/                   # Psychological activity / inner monologue
│   └── universal.md              #   ✅ Physiological synesthesia / fragmented thoughts / progressive collapse
├── group-scene/                  # Group scenes (to be added)
│   └── universal.md
└── transition/                   # Transition scenes
    └── universal.md              #   ✅ Emotional continuation / crisis hooks / sensory contrast / object clues / dynamic camera movement / B-roll scenery / dialogue continuation
```

## Tag System

Technique entries in each scene type file start with a `[Tag]`. Tags are used as filtering criteria when selected by the prompt-crafter:

```
## [Power] Power Dynamics - Who leads and who defends
## [Subtext] Mismatch - Dialogue and action are inconsistent
## [Pacing] Pacing changes
## [Information Gap] Dialogue driven by information gaps
```

After loading the file, the prompt-crafter does not inject everything into the Output·Writing Guidelines - it matches tags based on the core event of the scene, selecting only 2-4 relevant ones.

### Tag Matching Logic

```
Input·Scene Raw Material Core Scene Event: "Fang Yan avoids Lu Zheng's questioning in the office"
  → Triggered tags: [Concealment], [Power], [Subtext]
  → Select these three from dialogue/universal.md → Four-step conversion → Output·Writing Guidelines

Input·Scene Raw Material Core Scene Event: "Two characters chatting casually on the street"
  → Triggered tags: No direct conflict tags
  → Select [Action], [Differentiation] from dialogue/universal.md → Four-step conversion → Output·Writing Guidelines
```

Priority: Tag-matched techniques > Universal techniques without tags.

## Loading Method

The prompt-crafter reads from the following paths based on the scene type identified in the Input·Scene Raw Material:

1. Universal methodology: `scene-craft/{type}/universal.md`
2. Genre specialization: `scene-craft/{type}/{current_genre}.md` (Read if it exists, skip otherwise)

Universal methodology + Genre specialization merged → Filtered by tags → Four-step conversion → Injected into Output·Writing Guidelines.
