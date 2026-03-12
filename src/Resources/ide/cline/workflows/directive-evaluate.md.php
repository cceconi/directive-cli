<?php

/** @var string $projectName */
$body = (string) include __DIR__ . '/../../workflows/directive-evaluate.php';
return "# " . $projectName . " — Directive: Evaluate session\n\n" . $body;
