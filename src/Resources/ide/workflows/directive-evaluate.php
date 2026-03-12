<?php

/** @var string $projectName */
$body = <<<'BODY'
# DIRECTIVE: directive-evaluate

Analyse a brainstorm `discussion.md` and produce an ordered `changes-list.md` that breaks the
intent into implementable, coherent, dependency-aware changes.

## Input

Optionally specify a session slug. If omitted, list available sessions and ask the user to select.

## Steps

### Step 1 — List available sessions (if no argument given)

Scan `directive-spec/brainstorm/` for existing session directories (format: `YYYY-MM-DD-<slug>/`).

For each session, determine its state:
- **open**: `changes-list.md` does not exist, OR none of the changes listed in it are present in
  `directive-spec/changes/`
- **in-progress**: at least one listed change exists in `directive-spec/changes/`, but not all
- **kicked-off**: all listed changes exist in `directive-spec/changes/`

Display the list with states and ask the user: "Which session would you like to evaluate?"

### Step 2 — Read discussion.md

Load `directive-spec/brainstorm/<session>/discussion.md` from the selected session.

If it does not exist, inform the user:
> "No `discussion.md` found in this session. Invoke `directive-discuss` first."
Then STOP.

### Step 3 — Verify session state

- If state is **in-progress** or **kicked-off**: display `changes-list.md` read-only and inform
  the user: "This session is locked (`<state>`). No edits can be made." Then STOP.
- If state is **open**: proceed to Step 4.

### Step 4 — Decompose along 3 axes

Read `discussion.md` thoroughly and decompose the intent using:

1. **Functional coherence** — each change covers one bounded context or user-facing capability
2. **Implementable size** — calibrate each change to fit in an agent context window
3. **Inter-change dependencies** — identify topological order; foundational changes come first

### Step 5 — Format each proposed change

For each proposed change, produce:
- **name** — kebab-case slug (short, descriptive, unique within this session)
- **description** — one sentence: what the change delivers
- **reason** — why this boundary was chosen (which axis drove the split)
- **dependencies** — list of other change names that must be done first (empty if none)

### Step 6 — Write changes-list.md

Write `directive-spec/brainstorm/<session>/changes-list.md`:

```markdown
# Changes: <session-slug>

> Evaluated: YYYY-MM-DD | Session: open

## Suggested implementation order

1. `<change-name>` — <description>
...

## Change details

### <change-name>

**Description:** <one sentence>
**Reason for split:** <axis-driven rationale>
**Dependencies:** <list of change names, or "none">
```

### Step 7 — Confirm and suggest next step

Inform the user:
> "N changes proposed. Review and refine with `directive-discuss`, or invoke `directive-kickoff`."

## Output

```
## Changes list saved

**Session:** <session-slug>
**File:** directive-spec/brainstorm/<session>/changes-list.md
**Changes proposed:** N

Invoke `directive-kickoff` to create all changes at once.
```

## Guardrails

- Locked sessions (in-progress or kicked-off) are read-only — show content and STOP
- If `changes-list.md` already exists and state is open: replace it entirely
- Each change must have all four fields: name, description, reason, dependencies
- If no `discussion.md` exists, stop and redirect to `directive-discuss`
BODY;
return $body;
