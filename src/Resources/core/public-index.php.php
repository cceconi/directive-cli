<?php

return <<<'SCRIPT'
    <?php

    declare(strict_types=1);

    foreach ([
        __DIR__ . '/../vendor/autoload.php',
        __DIR__ . '/../../../autoload.php',
    ] as $autoload) {
        if (file_exists($autoload)) {
            require $autoload;
            break;
        }
    }

    use Symfony\Component\Dotenv\Dotenv;

    $envFile = __DIR__ . '/../.env';
    if (file_exists($envFile)) {
        (new Dotenv())->loadEnv($envFile);
    }

    // TODO: Initialize your WebApplication here.
    SCRIPT . "\n";
