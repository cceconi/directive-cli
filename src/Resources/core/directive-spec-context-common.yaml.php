<?php

/** @var string $projectName */
/** @var string $namespace */
return "version: 1\n\n"
    . "project:\n"
    . "  name: " . $projectName . "\n"
    . "  description: A Directive project\n"
    . "\n"
    . "context:\n"
    . "  namespace: " . $namespace . "\n"
    . "  stack: directive\n"
    . "\n"
    . "specs:\n"
    . "  path: directive-spec/specs/\n"
    . "\n"
    . "changes:\n"
    . "  path: directive-spec/changes/\n";
