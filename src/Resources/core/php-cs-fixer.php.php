<?php

return <<<'PHP'
    <?php

    declare(strict_types=1);

    use PhpCsFixer\Config;
    use PhpCsFixer\Finder;

    $finder = Finder::create()
        ->in([__DIR__ . '/src'])
        ->name('*.php');

    return (new Config())
        ->setRules([
            '@PSR12' => true,
            '@PHP84Migration' => true,
        ])
        ->setFinder($finder);
    PHP . "\n";
