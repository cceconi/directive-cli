<?php

/** @var string $projectName */
$body = (string) include __DIR__ . '/../../workflows/directive-kickoff.php';
return "# " . $projectName . " — Directive: Kickoff session\n\n" . $body;
