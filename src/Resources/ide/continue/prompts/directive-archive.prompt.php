<?php

/** @var string $projectName */
$body = (string) include __DIR__ . '/../../workflows/directive-archive.php';
return "---\nname: \"Directive: Archive change\"\ndescription: Archive the completed change for " . $projectName . "\ninvokable: true\n---\n\n" . $body;
