<?php

/** @var string $projectName */
$body = (string) include __DIR__ . '/../../workflows/directive-stack.php';
return "description = \"Set up stack rules for " . $projectName . "\"\n"
    . "prompt = \"\"\"\n" . $body . "\n\"\"\"\n";
