<?php

/** @var string $projectName */
$body = (string) include __DIR__ . '/../../workflows/directive-verify.php';
return "---\ndescription: Verify implementation completeness for the active change for " . $projectName . "\nargument-hint: \"[change-name]\"\n---\n\n" . $body;
