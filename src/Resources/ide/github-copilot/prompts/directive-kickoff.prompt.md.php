<?php

/** @var string $projectName */
$body = (string) include __DIR__ . '/../../workflows/directive-kickoff.php';
return "---\nmode: agent\ndescription: Kick off a brainstorm session by bulk-creating all changes for " . $projectName . "\n---\n\n" . $body;
