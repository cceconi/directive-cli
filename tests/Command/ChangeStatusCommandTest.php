<?php

declare(strict_types=1);

use Directive\Cli\Application;
use Symfony\Component\Console\Tester\CommandTester;

beforeEach(function (): void {
    $this->tmpDir = sys_get_temp_dir() . '/directive-status-test-' . uniqid();
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

it('reports proposal as ready and others as blocked when change is empty', function (): void {
    chdir($this->tmpDir);

    $app = new Application();
    $tester = new CommandTester($app->find('change:status'));
    $tester->execute(['name' => 'my-change', '--json' => true], ['interactive' => false]);

    expect($tester->getStatusCode())->toBe(0);

    /** @var array{changeName: string, schemaName: string, isComplete: bool, artifacts: list<array{id: string, status: string}>} $json */
    $json = json_decode($tester->getDisplay(), true);
    expect($json['changeName'])->toBe('my-change')
        ->and($json['schemaName'])->toBe('spec-driven')
        ->and($json['isComplete'])->toBeFalse();

    $statuses = array_column($json['artifacts'], 'status', 'id');
    expect($statuses['proposal'])->toBe('ready')
        ->and($statuses['design'])->toBe('blocked')
        ->and($statuses['specs'])->toBe('blocked')
        ->and($statuses['tasks'])->toBe('blocked');
});

it('reports design and specs as ready once proposal is done', function (): void {
    file_put_contents($this->tmpDir . '/openspec/changes/my-change/proposal.md', '# Proposal');
    chdir($this->tmpDir);

    $app = new Application();
    $tester = new CommandTester($app->find('change:status'));
    $tester->execute(['name' => 'my-change', '--json' => true], ['interactive' => false]);

    /** @var array{artifacts: list<array{id: string, status: string}>} $json */
    $json = json_decode($tester->getDisplay(), true);
    $statuses = array_column($json['artifacts'], 'status', 'id');
    expect($statuses['proposal'])->toBe('done')
        ->and($statuses['design'])->toBe('ready')
        ->and($statuses['specs'])->toBe('ready')
        ->and($statuses['tasks'])->toBe('blocked');
});

it('reports complete when all artifacts are done', function (): void {
    file_put_contents($this->tmpDir . '/openspec/changes/my-change/proposal.md', '# Proposal');
    file_put_contents($this->tmpDir . '/openspec/changes/my-change/design.md', '# Design');
    mkdir($this->tmpDir . '/openspec/changes/my-change/specs', 0777, true);
    file_put_contents($this->tmpDir . '/openspec/changes/my-change/specs/specs.md', '# Specs');
    file_put_contents($this->tmpDir . '/openspec/changes/my-change/tasks.md', '# Tasks');
    chdir($this->tmpDir);

    $app = new Application();
    $tester = new CommandTester($app->find('change:status'));
    $tester->execute(['name' => 'my-change', '--json' => true], ['interactive' => false]);

    /** @var array{isComplete: bool} $json */
    $json = json_decode($tester->getDisplay(), true);
    expect($json['isComplete'])->toBeTrue();
});

it('fails when change directory does not exist', function (): void {
    chdir($this->tmpDir);

    $app = new Application();
    $tester = new CommandTester($app->find('change:status'));
    $tester->execute(['name' => 'unknown-change'], ['interactive' => false]);

    expect($tester->getStatusCode())->toBe(1);
});
