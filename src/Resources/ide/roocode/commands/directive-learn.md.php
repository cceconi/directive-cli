<?php

/** @var string $projectName */
$body = (string) include __DIR__ . '/../../workflows/directive-learn.php';
return "# " . $projectName . " — Directive: Learn change\n\n" . $body;
