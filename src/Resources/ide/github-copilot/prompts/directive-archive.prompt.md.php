<?php

/** @var string $projectName */
$body = <<<'PROMPT'
# SKILL: directive-archive

Confirm all tasks are checked `[x]` and verification passed. Then run:
`directive change:archive "<change-name>"`

PROMPT;
return "---\nmode: agent\ndescription: Archive the completed change in " . $projectName . "\n---\n\n" . $body;
