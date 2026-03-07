<?php

declare(strict_types=1);

use Directive\Cli\Application;
use Symfony\Component\Console\Tester\CommandTester;

beforeEach(function (): void {
    $this->tmpDir = sys_get_temp_dir() . '/directive-instructions-test-' . uniqid();
    mkdir($this->tmpDir . '/directive-spec/context', 0777, true);
    mkdir($this->tmpDir . '/openspec/changes/my-change', 0777, true);

    file_put_contents(
        $this->tmpDir . '/directive-spec/context/common.yaml',
        "project:\n  name: TestProject\ncontext:\n  namespace: TestNs\n  stack: php\nspecs:\n  path: openspec/specs\nchanges:\n  path: openspec/changes\n"
    );
});

afterEach(function (): void {
    chdir(dirname(dirname(__DIR__)));
});

it('returns template for proposal artifact', function (): void {
    chdir($this->tmpDir);

    $app = new Application();
    $tester = new CommandTester($app->find('change:instructions'));
    $tester->execute(['artifact' => 'proposal', '--change' => 'my-change'], ['interactive' => false]);

    expect($tester->getStatusCode())->toBe(0)
        ->and($tester->getDisplay())->toContain('proposal');
});

it('returns JSON with correct structure', function (): void {
    chdir($this->tmpDir);

    $app = new Application();
    $tester = new CommandTester($app->find('change:instructions'));
    $tester->execute(['artifact' => 'proposal', '--change' => 'my-change', '--json' => true], ['interactive' => false]);

    expect($tester->getStatusCode())->toBe(0);

    /** @var array{changeName: string, artifactId: string, schemaName: string, outputPath: string, template: string, context: string, dependencies: list<array{id: string, done: bool, path: string}>} $json */
    $json = json_decode($tester->getDisplay(), true);
    expect($json['changeName'])->toBe('my-change')
        ->and($json['artifactId'])->toBe('proposal')
        ->and($json['schemaName'])->toBe('spec-driven')
        ->and($json['outputPath'])->toBe('proposal.md')
        ->and($json)->toHaveKey('template')
        ->and($json)->toHaveKey('context')
        ->and($json)->toHaveKey('dependencies');
});

it('injects project context', function (): void {
    chdir($this->tmpDir);

    $app = new Application();
    $tester = new CommandTester($app->find('change:instructions'));
    $tester->execute(['artifact' => 'proposal', '--change' => 'my-change', '--json' => true], ['interactive' => false]);

    expect($tester->getStatusCode())->toBe(0);

    /** @var array{context: string} $json */
    $json = json_decode($tester->getDisplay(), true);
    expect($json['context'])->toContain('TestProject');
});

it('fails for unknown artifact', function (): void {
    chdir($this->tmpDir);

    $app = new Application();
    $tester = new CommandTester($app->find('change:instructions'));
    $tester->execute(['artifact' => 'nonexistent', '--change' => 'my-change'], ['interactive' => false]);

    expect($tester->getStatusCode())->toBe(1);
});

it('fails when --change is missing', function (): void {
    chdir($this->tmpDir);

    $app = new Application();
    $tester = new CommandTester($app->find('change:instructions'));
    $tester->execute(['artifact' => 'proposal'], ['interactive' => false]);

    expect($tester->getStatusCode())->toBe(1);
});
