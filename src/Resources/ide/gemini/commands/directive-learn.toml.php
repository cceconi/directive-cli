<?php

/** @var string $projectName */
$body = (string) include __DIR__ . '/../../workflows/directive-learn.php';
return "description = \"Capture lessons learned after the change for " . $projectName . "\"\n"
    . "prompt = \"\"\"\n" . $body . "\n\"\"\"\n";
