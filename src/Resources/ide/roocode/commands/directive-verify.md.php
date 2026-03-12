<?php

/** @var string $projectName */
$body = (string) include __DIR__ . '/../../workflows/directive-verify.php';
return "# " . $projectName . " — Directive: Verify change\n\n" . $body;
