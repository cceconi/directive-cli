<?php

$body = <<<'BODY'
# DIRECTIVE: directive-kickoff

Bulk-create all changes listed in `changes-list.md` for a brainstorm session and lock the
session definitively.

## Input

Optionally specify a session slug. If omitted, list open sessions and ask the user to select.

## Steps

### Step 1 — List available sessions (if no argument given)

Scan `directive-spec/brainstorm/` for session directories (format: `YYYY-MM-DD-<slug>/`).

Determine session state (open / in-progress / kicked-off).
Display only sessions in state **open** and ask the user to select.

If no session is open, inform the user:
> "No open brainstorm session found. All sessions are either in-progress or already kicked off."
Then STOP.

### Step 2 — Read changes-list.md and verify state

Load `directive-spec/brainstorm/<session>/changes-list.md`.

If it does not exist:
> "No `changes-list.md` found. Invoke `directive-evaluate` first."
Then STOP.

Verify state:
- **in-progress** or **kicked-off**: inform user and STOP — kickoff is no longer possible.
- **open**: proceed.

### Step 3 — Display the recap

Show clearly:
- The session slug and date
- Total number of changes to create
- Suggested implementation order (numbered list with name + description)
- Any dependency relationships

### Step 4 — Ask for confirmation

Ask: "Ready to create these N changes? This will invoke `directive change:new <name>` for each one
and lock this session. Type **yes** to proceed or **no** to cancel."

- If no or cancel: STOP without creating anything.
- If yes: proceed to Step 5.

### Step 5 — Create each change

For each change in suggested order, run:

```
directive change:new <name>
```

Confirm after each: "`<name>` — created ✓"

If a call fails (e.g., change already exists), report the error and continue with the next change.

### Step 6 — Display the final report

```
## Kickoff complete!

**N changes created:**
- `change-one` ✓
- `change-two` ✓

**Session status:** kicked-off 🔒

**Suggested next step:** Start with `change-one` — invoke `directive-new change-one` to create
the first artifact, or `directive-continue change-one` to proceed if already created.
```

## Output

```
## Kickoff complete!

**Session:** YYYY-MM-DD-<slug>
**Changes created:** N
**Session status:** kicked-off

Invoke `directive-new <first-change>` to start implementation.
```

## Guardrails

- Ask for confirmation before creating any change — never auto-proceed
- If the user cancels, STOP without creating anything
- Only show sessions in state **open** for selection
- If a change creation fails, report and continue — do NOT stop the entire kickoff
BODY;
return ['description' => 'Kick off a brainstorm session by bulk-creating all changes', 'body' => $body];
