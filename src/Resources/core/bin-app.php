<?php

/** @var string $namespace */
return <<<SCRIPT
    #!/usr/bin/env php
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
    use {$namespace}\\Infrastructure\\Config\\AppConfig;
    use {$namespace}\\Infrastructure\\ConsoleApplication;

    \$envFile = __DIR__ . '/../.env';
    if (file_exists(\$envFile)) {
        (new Dotenv())->loadEnv(\$envFile);
    }

    (new ConsoleApplication())
        ->setConfig(AppConfig::class)
        ->addCommands([])
        ->run();
    SCRIPT . "\n";
