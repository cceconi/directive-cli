<?php

/** @var string $projectName */
$body = <<<'BODY'
# DIRECTIVE: directive-archive

Confirm all tasks are checked `[x]` and verification passed. Archive the completed change.

## Input

Optionally specify a change name. If omitted, inferred from context.
If ambiguous, show available changes and ask the user to select.

## Steps

### Step 1 — Check artifact completion

```
directive change:status --change <name> --json
```

If any artifacts are not done, warn the user and ask for confirmation before continuing.

### Step 2 — Check task completion

Read `tasks.md`. Count `- [ ]` (incomplete) vs `- [x]` (complete).

If incomplete tasks found, warn and ask for confirmation before continuing.

### Step 3 — Archive the change

```
directive change:archive "<change-name>"
```

This moves the change directory to the archive location.

### Step 4 — Display archive summary

Show: change name, schema used, archive location, any warnings raised during the process.

## Output

On success:
```
## Archive Complete

**Change:** <change-name>
**Schema:** <schema-name>
**Archived to:** directive-spec/changes/archive/YYYY-MM-DD-<name>/

All artifacts complete. All tasks complete.
```

On success with warnings:
```
## Archive Complete (with warnings)

**Change:** <change-name>
**Archived to:** directive-spec/changes/archive/YYYY-MM-DD-<name>/

**Warnings:**
- Archived with N incomplete tasks (user confirmed)
- Archived with N incomplete artifacts (user confirmed)
```

## Guardrails

- Do NOT skip the task or artifact completion checks
- Do NOT block archive on warnings — inform and confirm with the user
- Show a clear summary of what happened (including any warnings acknowledged)
- If the archive target already exists, fail with an error — do NOT overwrite
BODY;
return $body;
