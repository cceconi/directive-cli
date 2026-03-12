<?php

/** @var string $projectName */
$body = (string) include __DIR__ . '/../../workflows/directive-reflect.php';
return "description = \"Write a delivery reflection for the completed change for " . $projectName . "\"\n"
    . "prompt = \"\"\"\n" . $body . "\n\"\"\"\n";
