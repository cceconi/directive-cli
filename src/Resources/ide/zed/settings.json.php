<?php

/** @var string $projectName */
/** @var string $namespace */
return json_encode([
    'assistant' => [
        'default_model' => [
            'provider' => 'anthropic',
            'model' => 'claude-opus-4-5',
        ],
        'rules' => [
            'PHP 8.4 — declare(strict_types=1) in every file',
            'Namespace: ' . $namespace,
            'readonly classes/properties wherever possible',
            'phpstan level 8 — no errors',
            'Pest 3 for tests',
        ],
    ],
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
