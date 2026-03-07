<?php

declare(strict_types=1);

use Directive\Cli\Application;

test('application name is Directive CLI', function (): void {
    $app = new Application();
    expect($app->getName())->toBe('Directive CLI');
});

test('application version is 1.0.0', function (): void {
    $app = new Application();
    expect($app->getVersion())->toBe('1.0.0');
});
