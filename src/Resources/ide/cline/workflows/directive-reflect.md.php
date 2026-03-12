<?php

/** @var string $projectName */
$body = (string) include __DIR__ . '/../../workflows/directive-reflect.php';
return "# " . $projectName . " — Directive: Reflect change\n\n" . $body;
