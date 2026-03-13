<?php

$body = <<<'BODY'
# DIRECTIVE: directive-propose

Propose a new change — create it and generate all artifacts in one step.

## Input

Optionally provide a change name (kebab-case, e.g. `my-feature`) or a description. If a
description is given, derive a kebab-case slug from it before continuing.

## Steps

### Step 1 — Get the change name

If no argument was provided, ask the user what they want to build.
From their description, derive a kebab-case slug (e.g. "add user auth" → `add-user-auth`).

### Step 2 — Create the change directory

```
directive change:new <change-name>
```

### Step 3 — Get artifact build order

```
directive change:status <change-name> --json
```

Parse the JSON to get:
- `applyRequires`: array of artifact IDs needed before implementation (e.g. `["tasks"]`)
- `artifacts`: list of all artifacts with their status and dependencies

### Step 4 — Create artifacts in sequence until apply-ready

Loop through artifacts in dependency order (artifacts with no pending dependencies first):

For each artifact with `status: "ready"`:

a. Fetch instructions:
```
directive change:instructions <artifact-id> --change <change-name> --json
```

b. Read the JSON fields:
- `template`: structure for your output file
- `context`: project background (constraints for you — do NOT include in output)
- `rules`: artifact-specific rules (constraints for you — do NOT include in output)
- `outputPath`: where to write the artifact
- `dependencies`: completed artifacts to read for context

c. Read any completed dependency files for context.

d. Create the artifact file at `outputPath` using `template` as structure.
   Apply `context` and `rules` as constraints — do NOT copy them into the file.

e. Show brief progress: "Created <artifact-id>"

After each artifact, re-run:
```
directive change:status <change-name> --json
```
Check if every artifact ID in `applyRequires` has `status: "done"`.
Stop when all `applyRequires` artifacts are done.

If an artifact requires user input, ask before continuing.

### Step 5 — Show final status

```
directive change:status <change-name>
```

## Output

After completing all artifacts:
```
## Change Ready: <change-name>

**Schema:** <schema-name>
**Artifacts created:** N artifacts

All artifacts created! Invoke `directive-apply` to start implementing.
```

## Guardrails

- Always ask for a change name or description if none was provided
- If a change with that name already exists, suggest using `directive-continue` instead
- For each artifact: read templates and dependencies before writing — never guess structure
- Do NOT copy `context` or `rules` blocks into artifact files
- Stop when all `applyRequires` artifacts are done — do not create beyond that
- If creation of an artifact is unclear, pause and ask before continuing
BODY;
return ['description' => 'Create a change and generate all artifacts in one step', 'body' => $body];
