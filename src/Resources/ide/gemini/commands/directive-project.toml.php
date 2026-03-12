<?php

/** @var string $projectName */
$body = (string) include __DIR__ . '/../../workflows/directive-project.php';
return "description = \"Set up project context for " . $projectName . "\"\n"
    . "prompt = \"\"\"\n" . $body . "\n\"\"\"\n";
