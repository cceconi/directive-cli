<?php

/** @var string $projectName */
$body = <<<'PROMPT'
# SKILL: directive-project

Record the functional project context in `directive-spec/context/common.yaml`. Follow these steps exactly.

## Step 1 — Read existing context

Read `directive-spec/context/common.yaml`.

Check whether `project.context` already contains any of these keys:
- `description`
- `target_users`
- `bounded_contexts`

Show the user any values already present. Keys that already exist will be **skipped** — they will NOT be overwritten.

## Step 2 — Ask the 3 questions

For each key that is **absent or empty**, ask the user the corresponding question:

1. **description** — "Describe the functional domain of this project (what problem does it solve, for whom)?"
2. **target_users** — "Who are the target users or clients of this project?"
3. **bounded_contexts** — "What are the main bounded contexts or modules you expect in this project? (comma-separated or bullet list)"

Ask one question at a time and wait for the answer before continuing.

## Step 3 — Write project.context

Under `project.context:` in `directive-spec/context/common.yaml`, add only the keys collected in Step 2.
Use 2-space indentation. Do NOT touch any existing keys or other sections of the file.

Example result:
```yaml
project:
  name: my-app
  description: A Directive project
  context:
    description: A REST API for managing inventory in warehouses.
    target_users: Warehouse managers and logistics coordinators.
    bounded_contexts: inventory, orders, shipments
```

When done, inform the user:
> "Project context saved to `directive-spec/context/common.yaml`. Run `/dtsx-new` to start your first change."

PROMPT;
return "---\nmode: agent\ndescription: Set up project context for " . $projectName . "\n---\n\n" . $body;
