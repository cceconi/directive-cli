<?php

/** @var string $projectName */
$body = (string) include __DIR__ . '/../../workflows/directive-new.php';
return "---\nmode: agent\ndescription: Start a new Directive change for " . $projectName . "\n---\n\n" . $body;
