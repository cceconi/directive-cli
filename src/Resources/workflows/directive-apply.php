<?php

$body = <<<'BODY'
# DIRECTIVE: directive-apply

Implement tasks from the active change.

## Input

Optionally specify a change name. If omitted, inferred from context.
If ambiguous, show available changes and ask the user to select.

## Steps

### Step 1 — Check status

```
directive change:status --change <name> --json
```

Parse `schemaName` and confirm tasks artifact exists.

### Step 2 — Get apply instructions

```
directive change:instructions apply --change <name> --json
```

This returns:
- `contextFiles`: paths to proposal, specs, design, tasks
- `progress`: total, complete, remaining
- `tasks`: list with status
- State: `"blocked"` (missing artifacts), `"all_done"`, or in-progress

Handle states:
- `"blocked"`: show message, suggest `directive-continue`
- `"all_done"`: congratulate, suggest `directive-archive`
- Otherwise: proceed to implementation

### Step 3 — Read context files

Read all files listed in `contextFiles`: proposal, specs, design, tasks.

### Step 4 — Show current progress

Display: schema, progress (`N/M tasks complete`), remaining tasks overview.

### Step 5 — Implement tasks (loop until done or blocked)

For each pending task:
- Show which task is being worked on
- Make the code changes required
- Keep changes minimal and focused
- Mark task complete in tasks file: `- [ ]` → `- [x]`
- Continue to next task

Pause if:
- Task is unclear → ask for clarification
- Implementation reveals a design issue → suggest updating artifacts
- Error or blocker encountered → report and wait for guidance
- User interrupts

## Output

During implementation:
```
Working on task N/M: <task description>
[...implementation happening...]
✓ Task complete
```

On completion:
```
## Implementation Complete

**Change:** <change-name>
**Schema:** <schema-name>
**Progress:** M/M tasks complete ✓

All tasks done! Invoke `directive-archive` to archive.
```

On pause:
```
## Implementation Paused

**Progress:** N/M tasks complete

**Issue:** <description>

**Options:**
1. <option 1>
2. <option 2>
```

## Guardrails

- Always read context files before starting
- Keep going through tasks until done or blocked
- Mark each task `[x]` immediately after completing it — do NOT batch completions
- Pause on errors, blockers, or unclear requirements — do NOT guess
- Keep code changes minimal and scoped to each task
BODY;
return ['description' => 'Implement tasks from the active change', 'body' => $body];
