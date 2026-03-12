<?php

/** @var string $projectName */
$body = (string) include __DIR__ . '/../../workflows/directive-verify.php';
return "---\nmode: agent\ndescription: Verify implementation completeness for the active change for " . $projectName . "\n---\n\n" . $body;
