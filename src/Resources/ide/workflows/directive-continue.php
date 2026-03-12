<?php

/** @var string $projectName */
$body = <<<'BODY'
# DIRECTIVE: directive-continue

Continue to the next artifact for the active change.

## Input

Optionally specify a change name. If omitted, the active change is inferred from context.
If ambiguous, show available changes and ask the user to select.

## Steps

### Step 1 — Check current status

```
directive change:status --json
```

Parse the response to understand:
- `schemaName`: The schema in use
- `artifacts`: Array of items with `status` (`"done"`, `"ready"`, `"blocked"`)
- `isComplete`: Whether all artifacts are done

### Step 2 — If all artifacts are complete

Congratulate the user. Suggest using `directive-apply` to implement tasks or
`directive-archive` to archive. **STOP**.

### Step 3 — Pick the next artifact and generate it

Pick the FIRST artifact with `status: "ready"`. Then:

```
directive change:instructions <artifact-id> --change <name> --json
```

Read `template`, `context`, `rules`, `outputPath`, `dependencies`. Read any dependency files for
context. Create the artifact file at `outputPath` using the template as structure. Apply `context`
and `rules` as constraints when writing — do NOT copy them into the output file.

Show what was created and what is now unlocked.

**STOP after ONE artifact.**

### Step 4 — Show updated progress

```
directive change:status --change <name>
```

## Output

```
## Artifact Created: <artifact-name>

**Change:** <change-name>
**Schema:** <schema-name>
**Progress:** N/M artifacts complete

**Unlocked:** <next artifact name>

Invoke `directive-continue` to create the next artifact.
```

## Guardrails

- Create ONE artifact per invocation — never advance past a single artifact in one session
- Always read dependency artifacts before creating a new one
- Do NOT skip artifacts — follow the schema's order
- If no artifact is ready (all blocked), report the state and stop
- Do NOT guess or auto-select a change if ambiguous — show a list
BODY;
return $body;
