<?php

/** @var string $projectName */
$body = (string) include __DIR__ . '/../../workflows/directive-kickoff.php';
return "---\nname: \"Directive: Kickoff session\"\ndescription: Kick off a brainstorm session by bulk-creating all changes for " . $projectName . "\ninvokable: true\n---\n\n" . $body;
