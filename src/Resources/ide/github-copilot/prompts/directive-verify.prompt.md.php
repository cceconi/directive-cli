<?php

/** @var string $projectName */
$body = <<<'PROMPT'
# SKILL: directive-verify

Cross-check every spec against the implementation. Report any gaps. If all pass, confirm readiness to archive.

PROMPT;
return "---\nmode: agent\ndescription: Verify implementation completeness for the active change in " . $projectName . "\n---\n\n" . $body;
