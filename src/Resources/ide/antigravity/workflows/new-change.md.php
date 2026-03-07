<?php

/** @var string $projectName */
return "---\nname: directive-new\ndescription: Start a new Directive change for " . $projectName . "\n---\n\n"
    . <<<'MD'
# SKILL: directive-new

Starting a new change requires 3 CLI commands and 4 artifacts. Follow these steps exactly.

## Step 1 — Create the change directory

Ask the user for a change name (kebab-case, e.g. `my-feature`), then run:

```
directive change:new <change-name>
```

## Step 2 — Check initial status

```
directive change:status <change-name>
```

Read the output. The first ready artifact is `proposal`.

## Step 3 — Write each artifact

For each artifact that is `ready`, fetch its instructions:

```
directive change:instructions <artifact-id> --change <change-name>
```

Read the template and project context carefully, then produce the artifact file at its `outputPath` inside the change directory.

## Step 4 — Repeat until complete

After saving each artifact, re-run `change:status <change-name>` to see which artifact becomes `ready` next. Continue until all 4 artifacts are `done`.

Artifact order: `proposal` → `design` + `specs` (parallel) → `tasks`

## Step 5 — Hand off

When `change:status` reports all artifacts as `done`, inform the user the change is ready for implementation.
MD;
