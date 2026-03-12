<?php

/** @var string $projectName */
$body = (string) include __DIR__ . '/../../workflows/directive-kickoff.php';
return "---\ndescription: Kick off a brainstorm session by bulk-creating all changes for " . $projectName . "\nargument-hint: \"[session-slug]\"\n---\n\n" . $body;
