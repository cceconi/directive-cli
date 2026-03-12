<?php

/** @var string $projectName */
$body = (string) include __DIR__ . '/../../workflows/directive-evaluate.php';
return "---\nname: \"Directive: Evaluate session\"\ndescription: Evaluate a brainstorm session and produce an ordered changes list for " . $projectName . "\ncategory: Workflow\ntags: [directive, workflow]\n---\n\n" . $body;
