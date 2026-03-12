<?php

/** @var string $projectName */
$body = (string) include __DIR__ . '/../../workflows/directive-project.php';
return "---\ndescription: Set up project context for " . $projectName . "\n---\n\n" . $body;
