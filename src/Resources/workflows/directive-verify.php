<?php

$body = <<<'BODY'
# DIRECTIVE: directive-verify

Cross-check every spec against the implementation. Report gaps. Confirm readiness to archive.

## Input

Optionally specify a change name. If omitted, inferred from context.
If ambiguous, show available changes and ask the user to select.

## Steps

### Step 1 — Load artifacts

```
directive change:instructions apply --change <name> --json
```

Read all context files: proposal, specs, design, tasks.

### Step 2 — Verify Completeness

- Read `tasks.md` and parse checkboxes: `- [ ]` (incomplete) vs `- [x]` (complete)
- Extract all requirements from delta specs (marked `### Requirement:`)
- For each requirement, search the codebase for implementation evidence

### Step 3 — Verify Correctness

For each requirement in delta specs:
- Search codebase for implementation evidence (file paths, code patterns)
- Assess if implementation matches the requirement intent
- If divergence detected, note as WARNING

For each scenario (marked `#### Scenario:`):
- Check if conditions are handled in code and in tests
- If uncovered, note as WARNING

### Step 4 — Verify Coherence

If `design.md` exists:
- Extract key decisions (sections like "Decision:", "Approach:", "Architecture:")
- Verify implementation follows those decisions
- Flag contradictions as WARNING

Check for code pattern consistency: naming, directory structure, style.

### Step 5 — Generate Verification Report

```
## Verification Report: <change-name>

| Dimension    | Status               |
|--------------|----------------------|
| Completeness | X/Y tasks, N/M reqs  |
| Correctness  | M/N reqs covered     |
| Coherence    | Pass / Issues        |
```

List CRITICAL, WARNING, SUGGESTION issues with actionable recommendations.

**Final assessment:**
- CRITICAL issues present → "X critical issue(s) found. Fix before archiving."
- WARNING only → "No critical issues. Y warning(s) to consider. Ready for archive."
- All clear → "All checks passed. Ready for archive."

## Output

A full verification report with:
- Summary scorecard table
- Issues by priority (CRITICAL / WARNING / SUGGESTION), each with recommendation
- Final assessment and suggested next step

## Guardrails

- Prefer SUGGESTION over WARNING, WARNING over CRITICAL when uncertain
- Every issue must have a specific, actionable recommendation with file references where possible
- If only tasks.md exists: verify task completion only; skip spec/design checks
- If tasks + specs exist: verify completeness and correctness; skip design check
- Do NOT block archive on warnings — inform and confirm
BODY;
return ['description' => 'Verify implementation completeness for the active change', 'body' => $body];
