<?php

/** @var string $projectName */
$body = (string) include __DIR__ . '/../../workflows/directive-project.php';
return "---\nname: \"Directive: Project context\"\ndescription: Set up project context for " . $projectName . "\ninvokable: true\n---\n\n" . $body;
