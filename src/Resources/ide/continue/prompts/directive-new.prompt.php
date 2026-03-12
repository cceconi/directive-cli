<?php

/** @var string $projectName */
$body = (string) include __DIR__ . '/../../workflows/directive-new.php';
return "---\nname: \"Directive: New change\"\ndescription: Start a new Directive change for " . $projectName . "\ninvokable: true\n---\n\n" . $body;
