<?php

/** @var string $projectName */
/** @var string $namespace */
return "# RooCode Rules — " . $projectName . "\n\n## Project\n\nPHP 8.4 — namespace `" . $namespace . "`, Symfony Console, phpstan level 8, pest 3.\n\n## Rules\n\n- `declare(strict_types=1)` in every PHP file.\n- `readonly` classes/properties wherever possible.\n- No `mixed` types.\n- All tests via Pest 3.\n- All changes must pass phpstan level 8.\n";
