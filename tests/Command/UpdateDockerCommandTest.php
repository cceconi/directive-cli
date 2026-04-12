<?php

declare(strict_types=1);

use Directive\Cli\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Creates a minimal Directive project with Docker files.
 */
function createDirectiveProjectWithDocker(string $dir): void
{
    $fs = new Filesystem();
    $fs->mkdir($dir . '/directive-spec/context');
    $fs->mkdir($dir . '/docker');
    $yaml = "version: 1\n\nproject:\n  name: test-project\n  description: A test project\n\ncontext:\n  namespace: TestProject\n  stack: directive\n  docker_container: test-project-runtime\n\nspecs:\n  path: directive-spec/specs/\n\nchanges:\n  path: directive-spec/changes/\n";
    file_put_contents($dir . '/directive-spec/context/common.yaml', $yaml);

    // Pre-existing docker files
    file_put_contents($dir . '/docker/Dockerfile', "FROM php:8.4-fpm\n");
    file_put_contents($dir . '/docker-compose.yml', "services: {}\n");
    file_put_contents($dir . '/.env.example', "APP_ENV=dev\n");
}

// ─── Test: hors projet → FAILURE ─────────────────────────────────────────────

it('update-docker returns failure when executed outside a Directive project', function (): void {
    $tmpDir = sys_get_temp_dir() . '/directive-udocker-test-' . uniqid();
    (new Filesystem())->mkdir($tmpDir);
    chdir($tmpDir);

    $app = new Application();
    $command = $app->find('update-docker');
    $tester  = new CommandTester($command);
    $tester->execute([], ['interactive' => false]);

    expect($tester->getStatusCode())->toBe(1);
    expect($tester->getDisplay())->toContain('not found');

    (new Filesystem())->remove($tmpDir);
})->afterEach(fn () => chdir(dirname(dirname(__DIR__))));

// ─── Test: projet valide → DockerGenerator invoqué ────────────────────────────

it('update-docker regenerates docker files in a valid project', function (): void {
    $tmpDir = sys_get_temp_dir() . '/directive-udocker-test-' . uniqid();
    createDirectiveProjectWithDocker($tmpDir);
    chdir($tmpDir);

    $app = new Application();
    $command = $app->find('update-docker');
    $tester  = new CommandTester($command);
    $tester->execute(['--force' => true], ['interactive' => false]);

    expect($tester->getStatusCode())->toBe(0);
    expect(file_exists($tmpDir . '/docker/Dockerfile'))->toBeTrue();
    expect(file_exists($tmpDir . '/docker-compose.yml'))->toBeTrue();
    expect(file_exists($tmpDir . '/.env.example'))->toBeTrue();

    (new Filesystem())->remove($tmpDir);
})->afterEach(fn () => chdir(dirname(dirname(__DIR__))));

// ─── Test: docker_container lu depuis common.yaml ─────────────────────────────

it('update-docker uses docker_container from common.yaml without asking', function (): void {
    $tmpDir = sys_get_temp_dir() . '/directive-udocker-test-' . uniqid();
    createDirectiveProjectWithDocker($tmpDir);
    chdir($tmpDir);

    $app = new Application();
    $command = $app->find('update-docker');
    $tester  = new CommandTester($command);
    // non-interactive — should not block on container name question
    $tester->execute(['--force' => true], ['interactive' => false]);

    expect($tester->getStatusCode())->toBe(0);

    // Verify container name was used in docker-compose.yml
    $compose = file_get_contents($tmpDir . '/docker-compose.yml');
    expect($compose)->toContain('test-project-runtime');

    (new Filesystem())->remove($tmpDir);
})->afterEach(fn () => chdir(dirname(dirname(__DIR__))));

// ─── Test: --force → pas de confirmation ──────────────────────────────────────

it('update-docker with --force overwrites without confirmation', function (): void {
    $tmpDir = sys_get_temp_dir() . '/directive-udocker-test-' . uniqid();
    createDirectiveProjectWithDocker($tmpDir);
    chdir($tmpDir);

    // Write an outdated docker-compose.yml
    file_put_contents($tmpDir . '/docker-compose.yml', "# outdated\n");

    $app = new Application();
    $command = $app->find('update-docker');
    $tester  = new CommandTester($command);
    $tester->execute(['--force' => true], ['interactive' => false]);

    expect($tester->getStatusCode())->toBe(0);

    $compose = file_get_contents($tmpDir . '/docker-compose.yml');
    expect($compose)->not()->toBe("# outdated\n");
    expect($compose)->toContain('test-project-runtime');

    (new Filesystem())->remove($tmpDir);
})->afterEach(fn () => chdir(dirname(dirname(__DIR__))));
