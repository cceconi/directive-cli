<?php

/** @var string $projectName */
$body = (string) include __DIR__ . '/../../workflows/directive-kickoff.php';
return "description = \"Kick off a brainstorm session by bulk-creating all changes for " . $projectName . "\"\n"
    . "prompt = \"\"\"\n" . $body . "\n\"\"\"\n";
