<?php

/** @var string $projectName */
$body = (string) include __DIR__ . '/../../workflows/directive-discuss.php';
return "# " . $projectName . " — Directive: Discuss session\n\n" . $body;
