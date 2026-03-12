<?php

/** @var string $projectName */
$body = (string) include __DIR__ . '/../../workflows/directive-evaluate.php';
return "---\ndescription: Evaluate a brainstorm session and produce an ordered changes list for " . $projectName . "\nargument-hint: \"[session-slug]\"\n---\n\n" . $body;
