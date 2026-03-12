<?php

/** @var string $projectName */
return <<<'MD'
# SKILL: directive-evaluate

Analyse a brainstorm `discussion.md` and produce an ordered `changes-list.md` that breaks the intent
into implementable, coherent, dependency-aware changes.

## Step 1 — List available sessions (if no argument given)

If the user did not specify a session, scan `directive-spec/brainstorm/` for existing session
directories (format: `YYYY-MM-DD-<slug>/`).

For each session, determine its state:
- **open**: `changes-list.md` does not exist, OR none of the changes listed in it are present in
  `directive-spec/changes/`
- **in-progress**: at least one listed change exists in `directive-spec/changes/`, but not all
- **kicked-off**: all listed changes exist in `directive-spec/changes/`

Display the list with states and ask the user: "Which session would you like to evaluate?"

## Step 2 — Read discussion.md

Load `directive-spec/brainstorm/<session>/discussion.md` from the selected session.

If `discussion.md` does not exist, inform the user:
> "No `discussion.md` found in this session. Run `/dtsx-discuss` first to capture the intent."
Then STOP.

## Step 3 — Verify session state

Determine the state of the selected session (same logic as Step 1).

- If state is **in-progress** or **kicked-off**: display `changes-list.md` read-only and inform the
  user:
  > "This session is locked (`<state>`). No edits can be made. Here is the current changes list:"
  Then show `changes-list.md` and STOP.
- If state is **open**: proceed to Step 4.

## Step 4 — Decompose along 3 axes

Read `discussion.md` thoroughly and decompose the intent into changes using these three axes:

1. **Functional coherence** — each change should cover one bounded context or one coherent
   user-facing capability. Avoid mixing unrelated concerns in a single change.
2. **Implementable size** — calibrate each change to fit comfortably within an agent context window
   (typically: a few files, one feature area). Split if a change would touch too many layers.
3. **Inter-change dependencies** — identify which changes must be implemented before others. Prefer
   a topological order where foundational changes come first.

## Step 5 — Format each proposed change

For each proposed change, produce:
- **name** — kebab-case slug (short, descriptive, unique within this session)
- **description** — one sentence: what the change delivers
- **reason** — why this boundary was chosen (which axis drove the split)
- **dependencies** — list of other change names that must be done first (empty list if none)

## Step 6 — Write changes-list.md

Write `directive-spec/brainstorm/<session>/changes-list.md` with the following structure:

```markdown
# Changes: <session-slug>

> Evaluated: YYYY-MM-DD | Session: open

## Suggested implementation order

1. `<change-name>` — <description>
2. `<change-name>` — <description>
...

## Change details

### <change-name>

**Description:** <one sentence>
**Reason for split:** <axis-driven rationale>
**Dependencies:** <list of change names, or "none">

### <change-name>
...
```

If a `changes-list.md` already exists and the state is `open`, replace it entirely.

## Step 7 — Confirm and suggest next step

Inform the user:
> "Changes list saved to `directive-spec/brainstorm/<session>/changes-list.md`.
> N changes proposed. Review and refine with `/dtsx-discuss`, or run `/dtsx-kickoff` to create all
> changes at once."
MD;
