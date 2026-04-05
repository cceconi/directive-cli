<?php

/** @var string $namespace */
return <<<SCRIPT
    <?php

    declare(strict_types=1);

    foreach ([
        __DIR__ . '/../vendor/autoload.php',
        __DIR__ . '/../../../autoload.php',
    ] as \$autoload) {
        if (file_exists(\$autoload)) {
            require \$autoload;
            break;
        }
    }

    use Symfony\\Component\\Dotenv\\Dotenv;
    use Directive\\Service\\Configuration\\ConfigSourceTracker;
    use {$namespace}\\Infrastructure\\Config\\AppConfig;
    use {$namespace}\\Infrastructure\\WebApplication;

    \$basePath = dirname(__DIR__);
    if (file_exists(\$basePath . '/.env')) {
        ConfigSourceTracker::snapshotSystemVars();
        ConfigSourceTracker::loadTracked(new Dotenv(), \$basePath);
    }

    (new WebApplication())
        ->setConfig(AppConfig::class)
        ->run();
    SCRIPT . "\n";
