<?php

/** @var string $projectName */
$body = (string) include __DIR__ . '/../../workflows/directive-verify.php';
return "description = \"Verify implementation completeness for the active change for " . $projectName . "\"\n"
    . "prompt = \"\"\"\n" . $body . "\n\"\"\"\n";
