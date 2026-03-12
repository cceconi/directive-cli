<?php

/** @var string $projectName */
$body = <<<'PROMPT'
# SKILL: directive-continue

Run `directive change:status --json` to identify the active change. Generate and write the next artifact (proposal → design → specs → tasks).

PROMPT;
return "---\nmode: agent\ndescription: Continue to the next artifact for the active change in " . $projectName . "\n---\n\n" . $body;
