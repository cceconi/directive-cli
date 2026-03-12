<?php

/** @var string $projectName */
$body = <<<'PROMPT'
# SKILL: directive-learn

Identify 3-5 reusable lessons from the change. Append them as bullet points to `directive-spec/specs/lessons-learned.md`.

PROMPT;
return "---\nmode: agent\ndescription: Capture lessons learned after the change in " . $projectName . "\n---\n\n" . $body;
