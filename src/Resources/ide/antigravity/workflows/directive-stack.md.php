<?php

/** @var string $projectName */
$body = (string) include __DIR__ . '/../../workflows/directive-stack.php';
return "---\ndescription: Set up stack rules for " . $projectName . "\n---\n\n" . $body;
