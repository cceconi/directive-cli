<?php

/** @var string $projectName */
$body = <<<'PROMPT'
# SKILL: directive-reflect

Re-read proposal, design, specs and tasks. Write a concise reflection (what went well, what was harder, learnings) to `directive-spec/changes/<change-name>/reflect.md`.

PROMPT;
return "---\nmode: agent\ndescription: Write a delivery reflection for the completed change in " . $projectName . "\n---\n\n" . $body;
