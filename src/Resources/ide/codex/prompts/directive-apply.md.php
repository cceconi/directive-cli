<?php

/** @var string $projectName */
$body = (string) include __DIR__ . '/../../workflows/directive-apply.php';
return "---\ndescription: Implement tasks from the active change for " . $projectName . "\nargument-hint: \"[change-name]\"\n---\n\n" . $body;
