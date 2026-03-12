<?php

/** @var string $projectName */
$body = (string) include __DIR__ . '/../../workflows/directive-project.php';
return "---\ndescription: Set up project context for " . $projectName . "\nargument-hint: \"\"\n---\n\n" . $body;
