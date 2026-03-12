<?php

/** @var string $projectName */
$body = (string) include __DIR__ . '/../../workflows/directive-archive.php';
return "# " . $projectName . " — Directive: Archive change\n\n" . $body;
