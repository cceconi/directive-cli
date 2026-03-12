<?php

/** @var string $projectName */
$body = (string) include __DIR__ . '/../../workflows/directive-reflect.php';
return "---\ndescription: Write a delivery reflection for the completed change for " . $projectName . "\nargument-hint: \"[change-name]\"\n---\n\n" . $body;
