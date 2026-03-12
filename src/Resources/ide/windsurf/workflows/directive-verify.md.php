<?php

/** @var string $projectName */
$body = (string) include __DIR__ . '/../../workflows/directive-verify.php';
return "---\nname: \"Directive: Verify change\"\ndescription: Verify implementation completeness for the active change for " . $projectName . "\ncategory: Workflow\ntags: [directive, workflow]\n---\n\n" . $body;
