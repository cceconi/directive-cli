<?php

/** @var string $projectName */
$body = (string) include __DIR__ . '/../../workflows/directive-new.php';
return "description = \"Start a new Directive change for " . $projectName . "\"\n"
    . "prompt = \"\"\"\n" . $body . "\n\"\"\"\n";
