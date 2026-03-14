<?php

$body = <<<'BODY'
# DIRECTIVE: directive-sync

Sync delta specs from a change to its main specs (agent-driven merge). The change stays active.

## Input

Optionally specify a change name. If omitted, inferred from context or selected interactively.

## Steps

### Step 1 — Select the change

If no change name was provided:

```
directive change:list --json
```

Show available changes and ask the user to select the one to sync.

### Step 2 — Find delta specs

Look for delta spec files at:
```
directive-spec/changes/<name>/specs/*/spec.md
```

Each file may contain:
- `## ADDED Requirements` — new requirements to add
- `## MODIFIED Requirements` — changes to existing requirements
- `## REMOVED Requirements` — requirements to delete
- `## RENAMED Requirements` — FROM:/TO: renames

If no delta specs are found, inform the user and stop.

### Step 3 — Apply changes to main specs (per capability)

For each delta spec at `directive-spec/changes/<name>/specs/<capability>/spec.md`:

a. Read the delta spec to understand the intended changes.

b. Read the main spec at `directive-spec/specs/<capability>/spec.md` (may not exist yet).

c. Apply changes intelligently:

**ADDED Requirements:**
- If the requirement does not exist in the main spec → add it in full
- If the requirement already exists → update it to match (treat as implicit MODIFIED)

**MODIFIED Requirements:**
- Find the requirement in the main spec
- Apply changes: add new scenarios, modify existing ones, update the description
- Preserve scenarios and content NOT mentioned in the delta

**REMOVED Requirements:**
- Remove the entire requirement block from the main spec

**RENAMED Requirements:**
- Rename FROM: → TO: in the main spec

If the main spec file does not exist yet:
- Create `directive-spec/specs/<capability>/spec.md`
- Add a brief Purpose section
- Add the ADDED requirements

### Step 4 — Show summary

After applying all changes, display:

```
## Specs Synced: <change-name>

**<capability-1>:**
- Added requirement: "..."
- Modified requirement: "..." (added N scenario(s))

**<capability-2>:**
- Created new spec file
- Added requirement: "..."

Main specs updated. The change remains active.
```

## Output

```
## Specs Synced: <change-name>

Updated main specs:

**<capability>**: <summary of changes>

Main specs are now updated. The change is still active — archive with `directive-archive` when done.
```

## Guardrails

- Read both the delta spec and the main spec before making any changes
- Preserve all existing content in the main spec not mentioned in the delta
- The operation MUST be idempotent — running twice gives the same result (no duplication)
- If something in the delta is unclear, ask the user before applying
- Do NOT archive the change — only sync the specs
BODY;
return ['description' => 'Sync delta specs from a change to main specs', 'body' => $body];
