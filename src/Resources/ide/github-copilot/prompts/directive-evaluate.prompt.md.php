<?php

/** @var string $projectName */
$body = (string) include __DIR__ . '/../../workflows/directive-evaluate.php';
return "---\nmode: agent\ndescription: Evaluate a brainstorm session and produce an ordered changes list for " . $projectName . "\n---\n\n" . $body;
