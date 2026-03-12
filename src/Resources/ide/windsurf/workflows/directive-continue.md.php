<?php

/** @var string $projectName */
$body = (string) include __DIR__ . '/../../workflows/directive-continue.php';
return "---\nname: \"Directive: Continue change\"\ndescription: Continue to the next artifact for the active change for " . $projectName . "\ncategory: Workflow\ntags: [directive, workflow]\n---\n\n" . $body;
