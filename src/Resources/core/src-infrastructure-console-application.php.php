<?php

/** @var string $namespace */
return <<<SCRIPT
    <?php

    declare(strict_types=1);

    namespace {$namespace}\\Infrastructure;

    use Directive\\AbstractConsoleApplication;

    class ConsoleApplication extends AbstractConsoleApplication
    {
        // protected function configureContainer(): void
        // {
        //     \$this->addDefinitions([
        //         // MyInterface::class => new MyImpl(),
        //     ]);
        // }
    }
    SCRIPT . "\n";
