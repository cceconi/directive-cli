<?php

/** @var string $projectName */
$body = (string) include __DIR__ . '/../../workflows/directive-archive.php';
return "---\ndescription: Archive the completed change for " . $projectName . "\nargument-hint: \"[change-name]\"\n---\n\n" . $body;
