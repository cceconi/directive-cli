<?php

/** @var string $projectName */
$body = (string) include __DIR__ . '/../../workflows/directive-apply.php';
return "---\nmode: agent\ndescription: Implement tasks from the active change for " . $projectName . "\n---\n\n" . $body;
