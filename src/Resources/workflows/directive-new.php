<?php

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
## Git Addon

> Applicable only if `git.agent_managed: true` in `directive-spec/context/common.yaml`.
> If this key is absent or false, **ignore this section entirely**.

### Git Step 1 — WIP commit (if needed)

Check working directory status:
```bash
git status
```

If there are modified, added, or deleted files:
```bash
git add -A && git commit -m "wip: $(git branch --show-current)"
```

This commit is created automatically, without confirmation, regardless of `commit_mode`.

### Git Step 2 — Create and switch to the change branch

```bash
git checkout -b <branch_prefix><change-name>
```

Where `<branch_prefix>` comes from `git.branch_prefix` (default: `feat/`).

BODY;
return ['description' => 'Start a new Directive change', 'body' => $body];
