<?php

/** @var string $projectName */
$body = (string) include __DIR__ . '/../../workflows/directive-learn.php';
return "---\nname: \"Directive: Learn change\"\ndescription: Capture lessons learned after the change for " . $projectName . "\ninvokable: true\n---\n\n" . $body;
