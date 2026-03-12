<?php

/** @var string $projectName */
$body = (string) include __DIR__ . '/../../workflows/directive-apply.php';
return "description = \"Implement tasks from the active change for " . $projectName . "\"\n"
    . "prompt = \"\"\"\n" . $body . "\n\"\"\"\n";
