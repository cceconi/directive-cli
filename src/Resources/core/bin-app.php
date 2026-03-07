<?php

return <<<'SCRIPT'
    #!/usr/bin/env php
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

    use Symfony\Component\Console\Application;

    $app = new Application('app', '1.0.0');
    $app->run();
    SCRIPT . "\n";
