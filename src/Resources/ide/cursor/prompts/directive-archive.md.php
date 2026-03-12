<?php

/** @var string $projectName */
$body = (string) include __DIR__ . '/../../workflows/directive-archive.php';
return "---\ndescription: Archive the completed change for " . $projectName . "\n---\n\n" . $body;
