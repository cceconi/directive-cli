<?php

/** @var string $projectName */
$body = (string) include __DIR__ . '/../../workflows/directive-continue.php';
return "---\ndescription: Continue to the next artifact for the active change for " . $projectName . "\nargument-hint: \"[change-name]\"\n---\n\n" . $body;
