<?php

/** @var string $projectName */
$body = <<<'BODY'
# DIRECTIVE: directive-reflect

Re-read proposal, design, specs, and tasks. Write a concise reflection to
`directive-spec/changes/<change-name>/reflect.md`.

## Input

Optionally specify a change name. If omitted, inferred from context.

## Steps

### Step 1 — Load change artifacts

Read the proposal, design, specs, and tasks files for the change.

### Step 2 — Write reflect.md

Produce `directive-spec/changes/<change-name>/reflect.md` with these sections:

- **What went well** — specific accomplishments and decisions that worked
- **What was harder than expected** — obstacles encountered, where estimates were off
- **Learnings** — concrete takeaways for future changes

Keep it concise (200–400 words total). Be specific, not generic.

When done, inform the user:
> "Reflection saved to `directive-spec/changes/<change-name>/reflect.md`."

## Output

```
## Reflection saved

**Change:** <change-name>
**Location:** directive-spec/changes/<change-name>/reflect.md

Invoke `directive-archive` to finalize.
```

## Guardrails

- Be specific and honest — avoid generic phrases like "the project went well"
- Target 200–400 words total — this is a summary, not an essay
- Do NOT modify any existing artifacts
- Do NOT skip any of the three sections (what went well / harder / learnings)
BODY;
return $body;
