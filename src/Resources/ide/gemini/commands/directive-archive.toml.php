<?php

/** @var string $projectName */
$body = (string) include __DIR__ . '/../../workflows/directive-archive.php';
return "description = \"Archive the completed change for " . $projectName . "\"\n"
    . "prompt = \"\"\"\n" . $body . "\n\"\"\"\n";
