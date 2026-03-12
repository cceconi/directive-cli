<?php

/** @var string $projectName */
$body = (string) include __DIR__ . '/../../workflows/directive-evaluate.php';
return "description = \"Evaluate a brainstorm session and produce an ordered changes list for " . $projectName . "\"\n"
    . "prompt = \"\"\"\n" . $body . "\n\"\"\"\n";
