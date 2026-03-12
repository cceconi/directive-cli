<?php

/** @var string $projectName */
$body = (string) include __DIR__ . '/../../workflows/directive-stack.php';
return "# " . $projectName . " — Directive: Stack context\n\n" . $body;
