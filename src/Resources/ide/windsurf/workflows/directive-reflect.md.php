<?php

/** @var string $projectName */
$body = (string) include __DIR__ . '/../../workflows/directive-reflect.php';
return "---\nname: \"Directive: Reflect change\"\ndescription: Write a delivery reflection for the completed change for " . $projectName . "\ncategory: Workflow\ntags: [directive, workflow]\n---\n\n" . $body;
