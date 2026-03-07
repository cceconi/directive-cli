<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->in([__DIR__ . '/src'])
    ->append([__DIR__ . '/bin/directive', __DIR__ . '/bin/directive-cli'])
    ->name('*.php');

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true,
        '@PHP84Migration' => true,
    ])
    ->setFinder($finder);
