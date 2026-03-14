---
title: "Slash Commands — Lifecycle"
description: "Reference for /dtsx-new, /dtsx-continue, /dtsx-propose, /dtsx-apply, /dtsx-verify, /dtsx-reflect, /dtsx-learn, /dtsx-sync, /dtsx-archive, /dtsx-commit — commands that drive the change lifecycle."
group: lifecycle
commands: [dtsx-new, dtsx-continue, dtsx-propose, dtsx-apply, dtsx-verify, dtsx-reflect, dtsx-learn, dtsx-sync, dtsx-archive, dtsx-commit]
nav_order: 20
---

# Slash Commands — Lifecycle

These commands orchestrate the change lifecycle: from artifact creation through to archiving. They rely on `directive change:*` CLI commands to read and write change data.

**Standard lifecycle:**
`/dtsx-new` → `/dtsx-continue` (×N) → `/dtsx-apply` → `/dtsx-verify` → `/dtsx-archive`

**Fast-track:**
`/dtsx-propose` → `/dtsx-apply` → `/dtsx-archive`

---

### `/dtsx-new <name>`

**Description** — Creates a change and generates only the `proposal.md`. Stops so the proposal can be validated before continuing with `/dtsx-continue`.

**Input** — `<name>` (required): kebab-case change name (e.g. `add-user-auth`). If absent, the agent asks what you want to build and derives a slug.

**Preconditions** — None. A change with that name must not already exist (otherwise the agent offers to continue the existing one).

**Workflow**
1. `directive change:new <name>` — creates `directive-spec/changes/<name>/`
2. `directive change:instructions proposal --change <name> --json` — retrieves template and rules
3. The agent writes `proposal.md` (why, what changes, capabilities, impact)
4. The agent stops and invites the user to validate and continue with `/dtsx-continue`

**CLI invoked**
- `directive change:new <name>`
- `directive change:instructions proposal --change <name> --json`

**Git Addon** *(conditional — ignored if `git.agent_managed: false` or absent)*
> After `proposal.md` is created:
> 1. `git status` — if modified files: `git add -A && git commit -m "wip: <current-branch>"`
> 2. `git checkout -b <branch_prefix><name>` (e.g. `feat/<name>`)

**Guardrails**
- Stop after `proposal.md` — do not automatically generate design/specs/tasks
- If the change already exists, ask whether to continue the existing one or create a new one

---

### `/dtsx-continue [<change>]`

**Description** — Writes the next incomplete artifact in the sequence (design → specs → tasks). Invoke as many times as needed until all artifacts are ready.

**Input** — `[<change>]` (optional): change name. If absent, the agent infers from conversation context or lists active changes.

**Preconditions** — `proposal.md` must exist. Each artifact is unlocked in sequence.

**Workflow**
1. `directive change:list --json` (if no name provided) — change selection
2. `directive change:status <name> --json` — identifies the next unlocked (`ready`) artifact
3. `directive change:instructions <artifact> --change <name> --json` — template + rules
4. The agent loads already-completed artifacts as context
5. The agent writes the next artifact
6. Repeat until `tasks.md` is complete

**CLI invoked**
- `directive change:list --json`
- `directive change:status <name> --json`
- `directive change:instructions <artifact> --change <name> --json`

**Guardrails**
- Read existing artifacts before writing a new one
- If all artifacts are already complete, report it and suggest `/dtsx-apply`

---

### `/dtsx-propose <name>`

**Description** — Fast-track: creates the change and generates **all** artifacts in one pass until `applyRequires` is satisfied. Equivalent to `/dtsx-new` + `/dtsx-continue` × N chained automatically.

**Input** — `<name>` (required): kebab-case name. If absent, the agent asks what you want to build.

**Preconditions** — The change must not already exist (same behaviour as `/dtsx-new`).

**Workflow**
1. `directive change:new <name>`
2. `directive change:status <name> --json` — retrieves full artifact order and `applyRequires`
3. For each artifact in order (until `applyRequires` is satisfied):
   - `directive change:instructions <artifact> --change <name> --json`
   - The agent writes the artifact, loading prior artifacts as context
4. The agent stops when all `applyRequires` artifacts have status `done`
5. The agent invites the user to run `/dtsx-apply`

**CLI invoked**
- `directive change:new <name>`
- `directive change:status <name> --json`
- `directive change:instructions <artifact> --change <name> --json` (× N artifacts)

**Git Addon** *(conditional — ignored if `git.agent_managed: false` or absent)*
> After all artifacts are generated:
> 1. `git status` — if modified files: `git add -A && git commit -m "wip: <current-branch>"`
> 2. `git checkout -b <branch_prefix><name>`

**Guardrails**
- Read each completed artifact before creating the next one (chained context)
- If an artifact requires a non-obvious decision, pause and ask

---

### `/dtsx-apply [<change>]`

**Description** — Implements change tasks: reads specs/design/tasks, writes code, runs tests and phpstan, marks tasks complete one by one.

**Input** — `[<change>]` (optional): change name. If absent, the agent infers or lists.

**Preconditions** — `tasks.md` must exist and the change must be in `apply-ready` state (all `applyRequires` artifacts `done`).

**Workflow**
1. `directive change:list --json` (if no name provided)
2. `directive change:status <name> --json` — verifies required artifacts are present
3. `directive change:instructions apply --change <name> --json` — tasks enriched with stack context
4. The agent reads `proposal.md`, `design.md`, `specs/`, `tasks.md` for the change
5. The agent implements tasks one by one: file edits, tests, static analysis
6. After each task: `- [ ]` → `- [x]` in `tasks.md`

**CLI invoked**
- `directive change:list --json`
- `directive change:status <name> --json`
- `directive change:instructions apply --change <name> --json`

**Git Addon** *(conditional — ignored if `git.agent_managed: false` or absent)*
> At the start of apply, if current branch ≠ `<branch_prefix><name>`:
> 1. `git status` — if modified files: `git add -A && git commit -m "wip: <current-branch>"`
> 2. `git checkout <branch_prefix><name>`

**Guardrails**
- Pause if a task is ambiguous — ask before implementing
- Do not modify files outside the change scope without informing the user
- Mark tasks complete immediately after each one

---

### `/dtsx-verify [<change>]`

**Description** — Checks that implementation conforms to specs, design, and tasks. Produces a report classified as CRITICAL (blocking) / WARNING (should fix) / SUGGESTION (could improve).

**Input** — `[<change>]` (optional).

**Preconditions** — The change must have `tasks.md` and specs. `/dtsx-apply` must have been run.

**Workflow**
1. `directive change:list --json` (if no name provided)
2. `directive change:status <name> --json`
3. `directive change:instructions apply --change <name> --json` — loads tasks + specs
4. The agent analyses implemented code against spec requirements and design decisions
5. The agent produces the verification report

**CLI invoked**
- `directive change:list --json`
- `directive change:status <name> --json`
- `directive change:instructions apply --change <name> --json`

**Guardrails**
- Read-only: `/dtsx-verify` does not modify any files
- If divergences are found, recommend `/dtsx-reflect` before `/dtsx-archive`

---

### `/dtsx-reflect [<change>]`

**Description** — Updates the change artifacts (specs, design, tasks) to reflect decisions made during implementation. Use when implementation has diverged from the specs.

**Input** — `[<change>]` (optional).

**Preconditions** — The change must have been (at least partially) applied. Typically invoked after a `/dtsx-verify` report flagging divergences.

**Workflow**
1. `directive change:list --json` (if no name provided)
2. The agent reads all change artifacts (`proposal.md`, `design.md`, `tasks.md`, specs)
3. The agent analyses implemented code to detect divergences from the artifacts
4. The agent updates affected artifacts (decision notes, corrected decisions, added scenarios)

**CLI invoked**
- `directive change:list --json`

**Guardrails**
- Present proposed modifications before writing them
- Never delete requirements — add decision notes if behaviour changed

---

### `/dtsx-learn [<change>]`

**Description** — Capitalises technical learnings from a change into project context files (`config.yaml`, `context/<tech>.yaml`). Similar to `/dtsx-stack` but driven by decisions discovered during the change.

**Input** — `[<change>]` (optional).

**Preconditions** — The change must be implemented (or in progress). Most effective after `/dtsx-verify` or `/dtsx-reflect`.

**Workflow**
1. `directive change:list --json` (if no name provided)
2. The agent reads all change artifacts (including `reflect.md` if present)
3. The agent identifies technical decisions and emerging patterns from the change
4. The agent determines which files are affected: `directive-spec/config.yaml` and/or `directive-spec/context/<tech>.yaml`
5. The agent presents proposed updates to the user for confirmation
6. The agent applies updates (non-destructive merge)

**CLI invoked**
- `directive change:list --json`

**Guardrails**
- Always ask for confirmation before writing to context files
- Never overwrite existing unmodified content (non-destructive merge)

---

### `/dtsx-sync [<change>]`

**Description** — Merges delta specs from the change into main specs (`directive-spec/specs/<capability>/spec.md`), without archiving the change. The change remains active and can continue to be worked on.

**Difference from `/dtsx-archive`** — `/dtsx-sync` synchronises specs but leaves the change open. `/dtsx-archive` synchronises AND archives (the change is no longer modifiable).

**Input** — `[<change>]` (optional).

**Preconditions** — The change must have delta specs in `directive-spec/changes/<name>/specs/`.

**Workflow**
1. `directive change:list --json` (if no name provided)
2. The agent reads delta specs in `directive-spec/changes/<name>/specs/*/spec.md`
3. For each delta spec:
   - `## ADDED Requirements`: creates or enriches `directive-spec/specs/<capability>/spec.md`
   - `## MODIFIED Requirements`: updates the existing requirement
   - `## REMOVED Requirements`: removes or marks deprecated with reason + migration path
4. The change remains active after the sync

**CLI invoked**
- `directive change:list --json`

**Guardrails**
- Verify that MODIFIED content matches the existing requirement before overwriting
- Report if a capability in the delta specs does not yet exist in the main specs

---

### `/dtsx-archive [<change>]`

**Description** — Finalises a completed change: syncs delta specs into main specs, then moves the change to `directive-spec/changes/archive/`.

**Input** — `[<change>]` (optional).

**Preconditions** — All artifacts must be `done`. All tasks must be `[x]`. Typically called after `/dtsx-verify` (and optionally `/dtsx-learn`).

**Workflow**
1. `directive change:list --json` (if no name provided)
2. `directive change:status <name> --json` — checks completion
3. If artifacts or tasks are incomplete: warning + confirmation before proceeding
4. Sync delta specs (same mechanism as `/dtsx-sync`)
5. `directive change:archive <name>` — moves to `directive-spec/changes/archive/YYYY-MM-DD-<name>/`

**CLI invoked**
- `directive change:list --json`
- `directive change:status <name> --json`
- `directive change:archive <name>`

**Git Addon** *(conditional — ignored if `git.agent_managed: false` or absent)*
> 1. **Squash WIP** (if `wip:` commits on the branch): `git reset --soft $(git merge-base HEAD <base_branch>)`
> 2. **Conventional commit**:
>    - Mode `auto`: silent generation per `commit_pattern`, no confirmation
>    - Mode `manual`: message presented → confirmation → `git commit -m "<message>"`
>    - Pattern `conventional`: `<type>(<change-name>): <summary>` (derived from `proposal.md`)
> 3. **Branch close** (opt-in, not in trunk-based): `git checkout <base>` + `git merge --no-ff` + `git branch -d feat/<name>`
> 4. **Push** (opt-in, if `git.remote` configured): `git push`

**Guardrails**
- Warn if artifacts or tasks are incomplete, but do not block if the user confirms
- Always ask for confirmation before branch close and push

---

### `/dtsx-commit [<change>]`

**Description** — Generates and applies a conventional git commit for the current change, at any point after `/dtsx-apply`. Allows checkpointing work without archiving. **Manual mode only.**

**Input** — `[<change>]` (optional).

**Preconditions**
- `git.agent_managed: true` in `directive-spec/context/common.yaml`
- `git.commit_mode: manual` — if `auto`, the agent displays: *"Commits are managed automatically during `/dtsx-archive` in auto mode. Use `/dtsx-archive` to commit."*
- If git is not configured, the agent displays an explanatory message and stops.

**Workflow**
1. `directive change:list --json` (if no name provided)
2. Check `git.commit_mode` and `git.agent_managed` (see preconditions)
3. If `strategy` ≠ `trunk-based` and current branch ≠ `feat/<name>`: `git checkout feat/<name>`
4. The agent reads `proposal.md` (title) and `tasks.md` (completed `[x]` tasks)
5. The agent generates the commit message per `git.commit_pattern`:
   - `conventional`: `<type>(<change-name>): <summary>` (derived from content)
   - `free`: free-form message derived from context
   - `custom`: replaces `{type}`, `{change}`, `{summary}`, `{date}` in `git.commit_template`
6. The agent presents the message to the user — edits allowed
7. `git add -A && git commit -m "<message>"`

**CLI invoked**
- `directive change:list --json`
- Git commands: `git branch`, `git checkout`, `git add`, `git commit`

**Guardrails**
- Always present the commit message before committing
- Do not commit if no files are modified (`git status` clean)
