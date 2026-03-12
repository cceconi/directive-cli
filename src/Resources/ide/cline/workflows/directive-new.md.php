<?php

/** @var string $projectName */
$body = (string) include __DIR__ . '/../../workflows/directive-new.php';
return "# " . $projectName . " — Directive: New change\n\n" . $body;
