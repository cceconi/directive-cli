<?php

/** @var string $projectName */
$body = (string) include __DIR__ . '/../../workflows/directive-continue.php';
return "# " . $projectName . " — Directive: Continue change\n\n" . $body;
