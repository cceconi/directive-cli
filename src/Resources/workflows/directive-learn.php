<?php

$body = <<<'BODY'
# DIRECTIVE: directive-learn

Identify 3–5 reusable lessons from the change and append them to
`directive-spec/specs/lessons-learned.md`.

## Input

Optionally specify a change name. If omitted, inferred from context.

## Steps

### Step 1 — Read the change artifacts

Read proposal, design, specs, tasks, and `reflect.md` (if present) for the change.

### Step 2 — Extract lessons

Identify 3–5 concrete, reusable lessons. Each lesson should be:
- Specific to a decision or pattern (not generic advice)
- Applicable to future changes in this project
- Phrased as an actionable insight

### Step 3 — Append to lessons-learned.md

Open `directive-spec/specs/lessons-learned.md` (create if absent).
Append a new section:

```markdown
## <change-name> — <YYYY-MM-DD>

- <lesson 1>
- <lesson 2>
- <lesson 3>
```

Do NOT overwrite existing content.

When done, inform the user:
> "Lessons appended to `directive-spec/specs/lessons-learned.md`."

## Output

```
## Lessons Recorded

**Change:** <change-name>
**File:** directive-spec/specs/lessons-learned.md
**Lessons added:** N

Invoke `directive-archive` to finalize.
```

## Guardrails

- Do NOT overwrite existing entries in `lessons-learned.md` — only append
- Preserve all prior content; this file is cumulative
- 3–5 lessons only — quality over quantity
- Each lesson must reference a specific decision or event from the change
BODY;
return ['description' => 'Capture lessons learned after the change', 'body' => $body];
