<?php

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
## Git Addon

> Applicable only if `git.agent_managed: true` in `directive-spec/context/common.yaml`.
> If this key is absent or false, **ignore this section entirely**.

### Git Step 1 — Squash WIP commits

Regroup all WIP commits into a single staging area:
```bash
git reset --soft $(git merge-base HEAD <base_branch>)
```

Where `<base_branch>` is `git.base_branch` from config (`main`, `develop`, etc.).

Skip this step if the branch has only one commit or no `wip:` commits.

### Git Step 2 — Create the final commit

Build the commit message according to `git.commit_pattern` (see `directive-commit` for pattern rules).

If `git.commit_mode: auto` → commit silently, no confirmation:
```bash
git add -A && git commit -m "<generated-message>"
```

If `git.commit_mode: manual` → show the proposed message and wait for confirmation before committing.

### Git Step 3 — Close the branch (opt-in)

> Only if `git.strategy` is `feature-branch` or `gitflow` (skip for `trunk-based`).

Ask the user:
> "Merge `<branch>` into `<base_branch>` and delete it? [y/N]"

If confirmed:
```bash
git checkout <base_branch>
git merge --no-ff <branch>
git branch -d <branch>
```

### Git Step 4 — Push (opt-in)

> Only if `git.remote` is non-empty in config.

Ask the user:
> "Push to remote? [y/N]"

If confirmed:
```bash
git push
```

BODY;
return ['description' => 'Archive the completed change', 'body' => $body];
