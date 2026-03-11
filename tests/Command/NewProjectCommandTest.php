<?php

declare(strict_types=1);

use Directive\Cli\Application;
use Symfony\Component\Console\Tester\CommandTester;

it('fails when project directory already exists', function (): void {
    $tmpDir = sys_get_temp_dir() . '/directive-test-' . uniqid();
    mkdir($tmpDir);

    $app = new Application();
    $command = $app->find('new');
    $tester = new CommandTester($command);

    // Simulate running from the tmp dir parent so the project dir equals $tmpDir
    chdir(dirname($tmpDir));
    $projectName = basename($tmpDir);

    $tester->execute(['project-name' => $projectName], ['interactive' => false]);

    expect($tester->getStatusCode())->toBe(1);

    rmdir($tmpDir);
})->afterEach(fn () => chdir(dirname(dirname(__DIR__))));

// The IOException scenario (spec command-new-interactive) is covered by Symfony Console's
// default application-level exception handling (Design D5). When Filesystem throws,
// the exception bubbles up to Application::run() which formats the error and returns exit 1.
// Integration-level — not unit-testable via CommandTester without mocking.
