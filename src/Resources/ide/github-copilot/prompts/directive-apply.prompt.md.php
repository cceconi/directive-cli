<?php

/** @var string $projectName */
$body = <<<'PROMPT'
# SKILL: directive-apply

Run `directive change:apply "<change-name>"` to load tasks and context. Implement all unchecked tasks, marking them `[x]` as you go.

PROMPT;
return "---\nmode: agent\ndescription: Implement tasks from the active change in " . $projectName . "\n---\n\n" . $body;
