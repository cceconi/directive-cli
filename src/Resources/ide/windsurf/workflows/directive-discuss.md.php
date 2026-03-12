<?php

/** @var string $projectName */
$body = (string) include __DIR__ . '/../../workflows/directive-discuss.php';
return "---\nname: \"Directive: Discuss session\"\ndescription: Start or enrich a brainstorm session for " . $projectName . "\ncategory: Workflow\ntags: [directive, workflow]\n---\n\n" . $body;
