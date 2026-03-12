<?php

/** @var string $projectName */
$body = (string) include __DIR__ . '/../../workflows/directive-apply.php';
return "---\nname: \"Directive: Apply change\"\ndescription: Implement tasks from the active change for " . $projectName . "\ninvokable: true\n---\n\n" . $body;
