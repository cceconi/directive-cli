<?php

/** @var string $namespace */
return <<<SCRIPT
    <?php

    declare(strict_types=1);

    namespace {$namespace}\\Infrastructure\\Config;

    use Directive\\Service\\Configuration\\Configuration;
    use Directive\\Service\\Configuration\\ConfigProviderInterface;

    class AppConfig implements ConfigProviderInterface
    {
        public function define(Configuration \$config): void
        {
            // Required environment variables — the application will not boot if missing
            \$config->required('APP_ENV', allowed: ['production', 'staging', 'development', 'test']);

            // Optional environment variables — defaults shown
            // \$config->optional('LOG_LEVEL', default: 'info', allowed: ['debug', 'info', 'warning', 'error']);
        }
    }
    SCRIPT . "\n";
