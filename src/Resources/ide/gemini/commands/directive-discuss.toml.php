<?php

/** @var string $projectName */
$body = (string) include __DIR__ . '/../../workflows/directive-discuss.php';
return "description = \"Start or enrich a brainstorm session for " . $projectName . "\"\n"
    . "prompt = \"\"\"\n" . $body . "\n\"\"\"\n";
