<?php

/** @var string $projectName */
/** @var string $namespace */
/** @var bool $localMode */
/** @var string $directivePath */
return json_encode([
    ...($localMode ? ['repositories' => [['type' => 'path', 'url' => $directivePath]]] : []),
    'name' => strtolower($projectName) . '/' . strtolower($projectName),
    'type' => 'project',
    'require' => [
        'php' => '>=8.4',
        'cceconi/directive' => $localMode ? '*@dev' : '^1.0',
    ],
    'require-dev' => [
        'pestphp/pest' => '^3.0',
        'phpstan/phpstan' => '^2.0',
        'friendsofphp/php-cs-fixer' => '^3.0',
    ],
    'autoload' => [
        'psr-4' => [
            $namespace . '\\' => 'src/',
        ],
    ],
    'autoload-dev' => [
        'psr-4' => [
            $namespace . '\\Tests\\' => 'tests/',
        ],
    ],
    'scripts' => [
        'test' => 'pest',
        'analyse' => 'phpstan analyse',
        'cs-check' => 'php-cs-fixer check',
        'cs-fix' => 'php-cs-fixer fix',
    ],
    'config' => [
        'sort-packages' => true,
        'allow-plugins' => [
            'pestphp/pest-plugin' => true,
        ],
    ],
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
