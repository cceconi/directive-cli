<?php

/** @var string $projectName */
$body = (string) include __DIR__ . '/../../workflows/directive-discuss.php';
return "---\nmode: agent\ndescription: Start or enrich a brainstorm session for " . $projectName . "\n---\n\n" . $body;
