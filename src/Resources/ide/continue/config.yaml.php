<?php

/** @var string $projectName */
/** @var string $namespace */
return "# Continue config — " . $projectName . "\nmodels:\n  - provider: openai\n    model: gpt-4o\n    apiKeyEnv: OPENAI_API_KEY\n\ncontext:\n  - type: codebase\n\nrules:\n  - PHP 8.4 — declare(strict_types=1) in every file\n  - \"Namespace: " . $namespace . "\"\n  - readonly classes/properties wherever possible\n  - phpstan level 8 — no errors\n  - Pest 3 for tests\n";
