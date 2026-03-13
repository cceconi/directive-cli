<?php

$body = <<<'BODY'
# DIRECTIVE: directive-commit

Create a conventional commit for the current change (manual mode only).

## Input

No argument required. Reads `openspec/config.yaml` for git configuration.

## Steps

### Step 1 — Check prerequisites

Read `openspec/config.yaml` (or `directive-spec/context/common.yaml`).

**Stop immediately** if any of these conditions are true:
- `git.agent_managed` is `false` or absent → display:
  > "Git integration is not enabled for this project. Run `directive update-git` to configure it."
  and stop.
- `git.commit_mode` is `auto` → display:
  > "Commits are managed automatically during `/dtsx-archive` in auto mode. Use `/dtsx-archive` to commit."
  and stop.

### Step 2 — Stage changes

```bash
git add -A
```

If `git status` shows nothing to commit, display:
> "Nothing to commit. Working directory is clean."
and stop.

### Step 3 — Generate commit message

Build the commit message according to `git.commit_pattern`:

**conventional** (default):
- Format: `<type>(<change-name>): <summary>`
- `type`: derive from change content (`feat`, `fix`, `refactor`, `chore`, `docs`)
- `change-name`: current change name (kebab-case) as scope
- `summary`: derived from `proposal.md` title / first bullet, max 72 chars

**free**:
- Propose a free-form message derived from the change context
- Always ask for confirmation before committing

**custom** (uses `git.commit_template`):
- Replace variables `{type}`, `{change}`, `{summary}`, `{date}` in `git.commit_template`
- Derive values from change context

### Step 4 — Confirm and commit

Display the proposed message and ask for confirmation:
> "Commit with message: `<message>`? [y/N]"

If confirmed:
```bash
git commit -m "<message>"
```

If rejected, ask if the user wants to provide a custom message:
- If yes: ask for the message, then commit
- If no: abort and inform the user no commit was made

## Output

On success:
```
## Commit

**Message:** <type>(<change>): <summary>
**Files committed:** N files

Commit created. Invoke `/dtsx-archive` when the change is complete.
```

On abort:
```
## Commit Aborted

No commit was made.
```

## Guardrails

- Do NOT commit if `git.agent_managed: false` or `git.commit_mode: auto`
- Do NOT commit without explicit user confirmation
- Keep commit message under 72 characters (summary part)
- Do NOT create WIP commits with this workflow — use `/dtsx-new`, `/dtsx-propose`, or `/dtsx-apply` for WIP commits

BODY;
return ['description' => 'Create a conventional commit (manual mode only)', 'body' => $body];
