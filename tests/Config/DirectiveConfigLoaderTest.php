<?php

declare(strict_types=1);

use Directive\Cli\Config\DirectiveConfigLoader;
use Directive\Cli\Config\Exception\ConfigNotFoundException;

$tmpDir = '';

beforeEach(function () use (&$tmpDir): void {
    $tmpDir = sys_get_temp_dir() . '/directive-config-test-' . uniqid();
    mkdir($tmpDir . '/directive-spec/context', 0777, true);
});

afterEach(function () use (&$tmpDir): void {
    chdir(dirname(dirname(__DIR__)));
});

it('loads common.yaml successfully', function () use (&$tmpDir): void {
   file_put_contents(
        $tmpDir . '/directive-spec/context/common.yaml',
        "project:\n  name: TestProject\ncontext:\n  namespace: TestNs\n  stack: php\nspecs:\n  path: openspec/specs\nchanges:\n  path: openspec/changes\n"
    );

    $config = (new DirectiveConfigLoader())->load($tmpDir);

    expect($config->projectName)->toBe('TestProject')
        ->and($config->namespace)->toBe('TestNs')
        ->and($config->stack)->toBe('php')
        ->and($config->specsPath)->toBe('openspec/specs')
        ->and($config->changesPath)->toBe('openspec/changes')
        ->and($config->stackFiles)->toBe([]);
});

it('throws ConfigNotFoundException when file is missing', function () use (&$tmpDir): void {
    expect(fn () => (new DirectiveConfigLoader())->load($tmpDir))
        ->toThrow(ConfigNotFoundException::class);
});

it('throws ConfigNotFoundException when required key is missing', function () use (&$tmpDir): void {
    file_put_contents(
        $tmpDir . '/directive-spec/context/common.yaml',
        "project:\n  name: TestProject\ncontext:\n  namespace: TestNs\nspecs:\n  path: openspec/specs\nchanges:\n  path: openspec/changes\n"
    );

    expect(fn () => (new DirectiveConfigLoader())->load($tmpDir))
        ->toThrow(ConfigNotFoundException::class);
});
