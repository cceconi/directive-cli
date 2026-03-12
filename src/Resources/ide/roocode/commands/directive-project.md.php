<?php

/** @var string $projectName */
$body = (string) include __DIR__ . '/../../workflows/directive-project.php';
return "# " . $projectName . " — Directive: Project context\n\n" . $body;
