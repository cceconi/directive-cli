<?php

/** @var string $projectName */
$body = (string) include __DIR__ . '/../../workflows/directive-new.php';
return "---\ndescription: Start a new Directive change for " . $projectName . "\nargument-hint: \"<change-name or description>\"\n---\n\n" . $body;
