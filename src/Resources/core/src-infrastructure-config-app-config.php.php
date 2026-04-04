<?php

/** @var string $namespace */
return <<<SCRIPT
    <?php

    declare(strict_types=1);

    namespace {$namespace}\\Infrastructure\\Config;

    use Directive\\Service\\Configuration\\AbstractConfiguration;

    class AppConfig extends AbstractConfiguration
    {
        protected function define(): void
        {
            // Required environment variables — the application will not boot if missing
            \$this->required('APP_ENV', allowed: ['production', 'staging', 'development', 'test']);

            // Optional environment variables — defaults shown
            // \$this->optional('LOG_LEVEL', default: 'info', allowed: ['debug', 'info', 'warning', 'error']);
        }
    }
    SCRIPT . "\n";
