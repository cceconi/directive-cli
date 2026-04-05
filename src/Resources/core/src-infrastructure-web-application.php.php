<?php

/** @var string $namespace */
return <<<SCRIPT
    <?php

    declare(strict_types=1);

    namespace {$namespace}\\Infrastructure;

    use Directive\\AbstractWebApplication;

    class WebApplication extends AbstractWebApplication
    {
        // protected function configureContainer(): void
        // {
        //     \$this->addDefinitions([
        //         // MyInterface::class => new MyImpl(),
        //     ]);
        // }
    }
    SCRIPT . "\n";
