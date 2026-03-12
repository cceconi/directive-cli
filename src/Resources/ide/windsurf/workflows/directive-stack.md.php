<?php

/** @var string $projectName */
$body = (string) include __DIR__ . '/../../workflows/directive-stack.php';
return "---\nname: \"Directive: Stack context\"\ndescription: Set up stack rules for " . $projectName . "\ncategory: Workflow\ntags: [directive, workflow]\n---\n\n" . $body;
