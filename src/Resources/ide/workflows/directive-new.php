<?php

/** @var string $projectName */
$body = <<<'BODY'
# DIRECTIVE: directive-new

Starting a new change creates the `proposal` artifact only. Follow these steps exactly.

## Input

Optionally provide a change name (kebab-case, e.g. `my-feature`) or a description. If a description
is given, derive a kebab-case slug from it before continuing.

## Steps

### Step 1 — Get the change name

Ask the user for a change name (kebab-case, e.g. `my-feature`).
If the user provides a description instead of a name, derive a kebab-case slug from it before continuing.

### Step 2 — Create the change directory

```
directive change:new <change-name>
```

### Step 3 — Check initial status

```
directive change:status <change-name>
```

Confirm that `proposal` is `ready`.

### Step 4 — Fetch proposal instructions

```
directive change:instructions proposal --change <change-name> --json
```

Read the `template`, `context`, and `outputPath` from the JSON response.

### Step 5 — Write proposal.md

Using the template and project context, produce the file at the `outputPath`
inside the change directory (e.g. `directive-spec/changes/<change-name>/proposal.md`).

When done, inform the user:
> "The proposal is created. Invoke `directive-continue` to proceed with design and specs."

**Stop here.** Do NOT write design, specs, or tasks in this session.

## Output

```
## New Change: <change-name>

**Location:** directive-spec/changes/<change-name>/
**Schema:** <schema-name>
**Status:** 0/N artifacts complete
**First artifact ready:** proposal.md

Invoke `directive-continue` to create the next artifact.
```

## Guardrails

- Do NOT create any artifacts beyond `proposal.md` in this session
- If the name is invalid (not kebab-case), ask for a valid name before proceeding
- If a change with that name already exists, suggest using `directive-continue` instead
- Stop after writing `proposal.md` — do NOT advance to design, specs, or tasks
BODY;
return $body;
