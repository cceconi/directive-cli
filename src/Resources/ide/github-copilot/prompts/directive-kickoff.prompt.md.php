<?php

/** @var string $projectName */
$body = <<<'PROMPT'
# SKILL: directive-kickoff

Bulk-create all changes listed in `changes-list.md` for a brainstorm session and lock the session
definitively.

## Step 1 — List available sessions (if no argument given)

If the user did not specify a session, scan `directive-spec/brainstorm/` for existing session
directories (format: `YYYY-MM-DD-<slug>/`).

For each session, determine its state:
- **open**: none of the changes listed in `changes-list.md` are present in
  `directive-spec/changes/`
- **in-progress**: at least one listed change exists in `directive-spec/changes/`, but not all
- **kicked-off**: all listed changes exist in `directive-spec/changes/`

Display only sessions with state **open** and ask the user: "Which session would you like to kick off?"

If no session is in state `open`, inform the user:
> "No open brainstorm session found. All sessions are either in-progress or already kicked off."
Then STOP.

## Step 2 — Read changes-list.md and verify state

Load `directive-spec/brainstorm/<session>/changes-list.md` from the selected session.

If `changes-list.md` does not exist, inform the user:
> "No `changes-list.md` found in this session. Run `/dtsx-evaluate` first to produce the changes list."
Then STOP.

Verify the session state again (same logic as Step 1):
- If state is **in-progress**: inform the user:
  > "This session is already in-progress: some changes have been created but not all. Kickoff is no
  > longer possible."
  Then STOP.
- If state is **kicked-off**: inform the user:
  > "This session has already been kicked off: all changes have been created."
  Then STOP.
- If state is **open**: proceed to Step 3.

## Step 3 — Display the recap

Read `changes-list.md` and display a clear recap showing:
- The session slug and date
- The total number of changes to create
- The suggested implementation order (numbered list with name and description)
- Any dependency relationships between changes

Example output:
> **Session:** `2026-03-12-my-feature` | 4 changes to create
>
> **Suggested implementation order:**
> 1. `change-one` — foundations and data model
> 2. `change-two` — core business logic (depends on: change-one)
> 3. `change-three` — API endpoints (depends on: change-two)
> 4. `change-four` — UI integration (depends on: change-three)

## Step 4 — Ask for confirmation

Ask the user:
> "Ready to create these N changes? This will call `directive change:new <name>` for each one and
> lock this session. Type **yes** to proceed or **no** to cancel."

- If the user says **no** or cancels: STOP without creating any change.
- If the user says **yes** or confirms: proceed to Step 5.

## Step 5 — Create each change

For each change listed in `changes-list.md`, in the suggested implementation order:

Run in the terminal:
```
directive change:new <name>
```

After each call, confirm to the user: "`<name>` — created ✓"

If a `directive change:new` call fails (e.g., change already exists), report the error and continue
with the next change.

## Step 6 — Display the final report

Once all changes have been processed, display:

> **Kickoff complete!**
>
> **N changes created:**
> - `change-one` ✓
> - `change-two` ✓
> - `change-three` ✓
>
> **Session status:** kicked-off 🔒
>
> **Suggested next step:** Start with `change-one` — run `/dtsx-new change-one` to create the first
> artifact (proposal.md), or `/dtsx-propose change-one` to generate all artifacts at once.

PROMPT;
return "---\nmode: agent\ndescription: Kick off a brainstorm session by bulk-creating all changes listed in changes-list.md for " . $projectName . "\n---\n\n" . $body;
