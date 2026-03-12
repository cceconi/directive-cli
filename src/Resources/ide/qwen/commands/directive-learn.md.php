<?php

/** @var string $projectName */
$body = (string) include __DIR__ . '/../../workflows/directive-learn.php';
return "---\ndescription: Capture lessons learned after the change for " . $projectName . "\n---\n\n" . $body;
