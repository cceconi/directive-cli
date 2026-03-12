<?php

/** @var string $projectName */
$body = (string) include __DIR__ . '/../../workflows/directive-learn.php';
return "---\ndescription: Capture lessons learned after the change for " . $projectName . "\nargument-hint: \"[change-name]\"\n---\n\n" . $body;
