<?php

declare(strict_types=1);

use Directive\Cli\Application;
use Symfony\Component\Console\Tester\CommandTester;

$tmpDir = '';

beforeEach(function () use (&$tmpDir): void {
    $tmpDir = sys_get_temp_dir() . '/directive-new-test-' . uniqid();
    mkdir($tmpDir . '/directive-spec/context', 0777, true);
    mkdir($tmpDir . '/openspec/changes', 0777, true);

    file_put_contents(
        $tmpDir . '/directive-spec/context/common.yaml',
        "project:\n  name: TestProject\ncontext:\n  namespace: TestNs\n  stack: php\nspecs:\n  path: openspec/specs\nchanges:\n  path: openspec/changes\n"
    );
});

afterEach(function () use (&$tmpDir): void {
    chdir(dirname(dirname(__DIR__)));
});

it('creates a new change directory', function () use (&$tmpDir): void {
    chdir($tmpDir);

    $app = new Application();
    $tester = new CommandTester($app->find('change:new'));
    $tester->execute(['name' => 'my-feature'], ['interactive' => false]);

    expect($tester->getStatusCode())->toBe(0)
        ->and(is_dir($tmpDir . '/openspec/changes/my-feature'))->toBeTrue();
});

it('fails when change directory already exists', function () use (&$tmpDir): void {
    mkdir($tmpDir . '/openspec/changes/existing-change');
    chdir($tmpDir);

    $app = new Application();
    $tester = new CommandTester($app->find('change:new'));
    $tester->execute(['name' => 'existing-change'], ['interactive' => false]);

    expect($tester->getStatusCode())->toBe(1);
});

it('fails when name is not kebab-case', function () use (&$tmpDir): void {
    chdir($tmpDir);

    $app = new Application();
    $tester = new CommandTester($app->find('change:new'));
    $tester->execute(['name' => 'My_Feature'], ['interactive' => false]);

    expect($tester->getStatusCode())->toBe(1);
});

it('fails when common.yaml is missing', function () use (&$tmpDir): void {
    $emptyDir = sys_get_temp_dir() . '/directive-new-empty-' . uniqid();
    mkdir($emptyDir);
    chdir($emptyDir);

    $app = new Application();
    $tester = new CommandTester($app->find('change:new'));
    $tester->execute(['name' => 'my-feature'], ['interactive' => false]);

    expect($tester->getStatusCode())->toBe(1);

    rmdir($emptyDir);
});
