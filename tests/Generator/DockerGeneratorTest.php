<?php

declare(strict_types=1);

use Directive\Cli\Generator\DockerGenerator;
use Directive\Cli\Generator\ProjectContext;
use Symfony\Component\Filesystem\Filesystem;

it('generates docker files when withDocker is true', function (): void {
    $fs = new Filesystem();
    $tmpDir = sys_get_temp_dir() . '/directive-docker-test-' . uniqid();
    $fs->mkdir($tmpDir);

    $context = new ProjectContext(
        projectName: 'my-project',
        projectDir: $tmpDir,
        namespace: 'MyProject',
        tool: 'none',
        withDocker: true,
        containerName: 'my-project-runtime',
    );

    $generator = new DockerGenerator();
    $generator->generate($context);

    expect(file_exists($tmpDir . '/docker/Dockerfile'))->toBeTrue();
    expect(file_exists($tmpDir . '/docker-compose.yml'))->toBeTrue();
    expect(file_exists($tmpDir . '/.env.example'))->toBeTrue();
    expect(file_exists($tmpDir . '/docker/start.sh'))->toBeTrue();
    expect(file_exists($tmpDir . '/docker/stop.sh'))->toBeTrue();

    $fs->remove($tmpDir);
});

it('skips docker files when withDocker is false', function (): void {
    $fs = new Filesystem();
    $tmpDir = sys_get_temp_dir() . '/directive-docker-test-' . uniqid();
    $fs->mkdir($tmpDir);

    $context = new ProjectContext(
        projectName: 'my-project',
        projectDir: $tmpDir,
        namespace: 'MyProject',
        tool: 'none',
        withDocker: false,
        containerName: '',
    );

    $generator = new DockerGenerator();
    $generator->generate($context);

    expect(file_exists($tmpDir . '/docker/Dockerfile'))->toBeFalse();
    expect(file_exists($tmpDir . '/docker-compose.yml'))->toBeFalse();

    $fs->remove($tmpDir);
});
