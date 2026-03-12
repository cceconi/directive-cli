<?php

$body = <<<'BODY'
# DIRECTIVE: directive-discuss

Start or enrich a brainstorm session. Capture the user's intent conversationally and structure it
in a `discussion.md` artifact under `directive-spec/brainstorm/`.

## Input

Optionally provide a session slug or subject. If omitted, list existing sessions and ask.

## Steps

### Step 1 — Check session state

Scan `directive-spec/brainstorm/` for existing session directories (format: `YYYY-MM-DD-<slug>/`).

- If multiple sessions exist, list them and ask the user: "Which session would you like to work on?
  Or start a new one?"
- For the selected session, determine its state:
  - **open**: `changes-list.md` does not exist, OR none of the changes listed in it are present in
    `directive-spec/changes/`
  - **in-progress**: at least one listed change exists in `directive-spec/changes/`, but not all
  - **kicked-off**: all listed changes exist in `directive-spec/changes/`
- If state is **not** `open`: display `discussion.md` read-only and inform the user:
  > "This session is locked (`<state>`). No edits can be made. Here is the current discussion:"
  Then show `discussion.md` and STOP.

### Step 2 — Create or identify the session

**New session:**
- If no subject was provided, ask: "What is the subject of this brainstorm? (used as slug)"
- Create directory `directive-spec/brainstorm/YYYY-MM-DD-<slug>/` (today's date, slug in lowercase
  kebab-case derived from the subject)
- Confirm to the user: "Session `YYYY-MM-DD-<slug>` created."

**Existing open session:**
- Load `discussion.md` from the selected directory
- Inform the user: "Resuming session `YYYY-MM-DD-<slug>`. Current content will be shown before each
  question so you can enrich or leave it as-is."

### Step 3 — Ask four open questions (sequentially)

For an existing session, show the current value of each section before asking.

1. **Objective** — "What is the main objective of this project or feature? What problem does it solve?"
2. **Planned features** — "What features or capabilities are you envisioning? (list as many as you like, even rough ideas)"
3. **Known constraints** — "Are there any known constraints? (technical, business, timeline, integrations, etc.)"
4. **Open questions** — "What are the open questions or unknowns you still need to resolve?"

### Step 4 — Synthesize the conversation

Consolidate the answers into structured sections:
- **Objectif** — concise statement of the goal
- **Fonctionnalités envisagées** — bulleted list of features/capabilities
- **Contraintes** — bulleted list of constraints
- **Questions ouvertes** — bulleted list of unresolved questions

For an existing session, merge new answers with existing content non-destructively.

### Step 5 — Write or update discussion.md

Write `directive-spec/brainstorm/YYYY-MM-DD-<slug>/discussion.md`:

```markdown
# Discussion: <slug>

> Date: YYYY-MM-DD | État: open

## Objectif

<synthesized objective>

## Fonctionnalités envisagées

- <feature>

## Contraintes

- <constraint>

## Questions ouvertes

- <question>
```

When done, inform the user:
> "Discussion saved. Invoke `directive-discuss` again to enrich it, or `directive-evaluate` to break it down into changes."

## Output

```
## Discussion saved

**Session:** YYYY-MM-DD-<slug>
**File:** directive-spec/brainstorm/YYYY-MM-DD-<slug>/discussion.md
**State:** open

Invoke `directive-evaluate` to produce an ordered changes list.
```

## Guardrails

- Locked sessions (in-progress or kicked-off) are read-only — show content and STOP
- Ask questions one at a time; wait for the full answer before asking the next
- Merge answers non-destructively — preserve items not explicitly modified or removed
- Use the date format `YYYY-MM-DD` for session directories
BODY;
return ['description' => 'Start or enrich a brainstorm session', 'body' => $body];
