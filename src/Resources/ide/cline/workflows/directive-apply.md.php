<?php

/** @var string $projectName */
$body = (string) include __DIR__ . '/../../workflows/directive-apply.php';
return "# " . $projectName . " — Directive: Apply change\n\n" . $body;
