<?php

/** @var string $projectName */
$body = (string) include __DIR__ . '/../../workflows/directive-stack.php';
return "---\nname: \"Directive: Stack context\"\ndescription: Set up stack rules for " . $projectName . "\ninvokable: true\n---\n\n" . $body;
