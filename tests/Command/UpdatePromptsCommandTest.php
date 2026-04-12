<?php

declare(strict_types=1);

use Directive\Cli\Application;
use Directive\Cli\Command\UpdatePromptsCommand;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Creates a minimal Directive project with a github-copilot prompts directory.
 */
function createDirectiveProjectWithCopilot(string $dir): void
{
    $fs = new Filesystem();
    $fs->mkdir($dir . '/directive-spec/context');
    $fs->mkdir($dir . '/.github/prompts');
    $yaml = "version: 1\n\nproject:\n  name: test-project\n  description: A test project\n\ncontext:\n  namespace: TestProject\n  stack: directive\n\nspecs:\n  path: directive-spec/specs/\n\nchanges:\n  path: directive-spec/changes/\n";
    file_put_contents($dir . '/directive-spec/context/common.yaml', $yaml);
}

// ─── Test: hors projet → FAILURE ─────────────────────────────────────────────

it('returns failure when executed outside a Directive project', function (): void {
    $tmpDir = sys_get_temp_dir() . '/directive-uprompts-test-' . uniqid();
    (new Filesystem())->mkdir($tmpDir);
    chdir($tmpDir);

    $app = new Application();
    $command = $app->find('update-prompts');
    $tester  = new CommandTester($command);
    $tester->execute([], ['interactive' => false]);

    expect($tester->getStatusCode())->toBe(1);
    expect($tester->getDisplay())->toContain('not found');

    (new Filesystem())->remove($tmpDir);
})->afterEach(fn () => chdir(dirname(dirname(__DIR__))));

// ─── Test: tool détecté automatiquement (github-copilot) ─────────────────────

it('detects github-copilot from .github/prompts/ and regenerates files', function (): void {
    $tmpDir = sys_get_temp_dir() . '/directive-uprompts-test-' . uniqid();
    createDirectiveProjectWithCopilot($tmpDir);
    chdir($tmpDir);

    $app = new Application();
    $command = $app->find('update-prompts');
    $tester  = new CommandTester($command);
    $tester->execute([], ['interactive' => false]);

    expect($tester->getStatusCode())->toBe(0);
    expect($tester->getDisplay())->toContain('Detected tool: github-copilot');
    // At least one prompt file generated
    expect(is_dir($tmpDir . '/.github/prompts'))->toBeTrue();
    $files = glob($tmpDir . '/.github/prompts/*.prompt.md');
    expect($files)->not()->toBeEmpty();

    (new Filesystem())->remove($tmpDir);
})->afterEach(fn () => chdir(dirname(dirname(__DIR__))));

// ─── Test: --only filtre sur un seul fichier ──────────────────────────────────

it('regenerates only the specified command with --only', function (): void {
    $tmpDir = sys_get_temp_dir() . '/directive-uprompts-test-' . uniqid();
    createDirectiveProjectWithCopilot($tmpDir);
    chdir($tmpDir);

    $app = new Application();
    $command = $app->find('update-prompts');
    $tester  = new CommandTester($command);
    $tester->execute(['--only' => 'directive-propose'], ['interactive' => false]);

    expect($tester->getStatusCode())->toBe(0);

    // Only directive-propose.prompt.md should have been generated
    $files = glob($tmpDir . '/.github/prompts/*.prompt.md') ?: [];
    expect(count($files))->toBe(1);
    expect(basename($files[0]))->toBe('directive-propose.prompt.md');

    (new Filesystem())->remove($tmpDir);
})->afterEach(fn () => chdir(dirname(dirname(__DIR__))));

// ─── Test: --only commande inconnue → FAILURE + liste ────────────────────────

it('returns failure with unknown --only command and lists available commands', function (): void {
    $tmpDir = sys_get_temp_dir() . '/directive-uprompts-test-' . uniqid();
    createDirectiveProjectWithCopilot($tmpDir);
    chdir($tmpDir);

    $app = new Application();
    $command = $app->find('update-prompts');
    $tester  = new CommandTester($command);
    $tester->execute(['--only' => 'unknown-command'], ['interactive' => false]);

    expect($tester->getStatusCode())->toBe(1);
    expect($tester->getDisplay())->toContain('unknown-command');
    expect($tester->getDisplay())->toContain('directive-propose');

    (new Filesystem())->remove($tmpDir);
})->afterEach(fn () => chdir(dirname(dirname(__DIR__))));

// ─── Test: aucun outil détecté → choix 'none' → SUCCESS + message ────────────

it('returns success with informative message when user chooses none', function (): void {
    $tmpDir = sys_get_temp_dir() . '/directive-uprompts-test-' . uniqid();
    $fs = new Filesystem();
    $fs->mkdir($tmpDir . '/directive-spec/context');
    $yaml = "version: 1\n\nproject:\n  name: test-project\n  description: A test project\n\ncontext:\n  namespace: TestProject\n  stack: directive\n\nspecs:\n  path: directive-spec/specs/\n\nchanges:\n  path: directive-spec/changes/\n";
    file_put_contents($tmpDir . '/directive-spec/context/common.yaml', $yaml);
    chdir($tmpDir);

    $app = new Application();
    $command = $app->find('update-prompts');
    $tester  = new CommandTester($command);
    $tester->setInputs(['none']);
    $tester->execute([], ['interactive' => true]);

    expect($tester->getStatusCode())->toBe(0);
    expect($tester->getDisplay())->toContain('No tool selected');

    $fs->remove($tmpDir);
})->afterEach(fn () => chdir(dirname(dirname(__DIR__))));

// ─── Test: aucun outil détecté → question interactive → tool choisi ───────────

it('asks interactively when no tool directory is detected', function (): void {
    $tmpDir = sys_get_temp_dir() . '/directive-uprompts-test-' . uniqid();
    $fs = new Filesystem();
    $fs->mkdir($tmpDir . '/directive-spec/context');
    $yaml = "version: 1\n\nproject:\n  name: test-project\n  description: A test project\n\ncontext:\n  namespace: TestProject\n  stack: directive\n\nspecs:\n  path: directive-spec/specs/\n\nchanges:\n  path: directive-spec/changes/\n";
    file_put_contents($tmpDir . '/directive-spec/context/common.yaml', $yaml);
    chdir($tmpDir);

    $app = new Application();
    $command = $app->find('update-prompts');
    $tester  = new CommandTester($command);
    $tester->setInputs(['github-copilot']);
    $tester->execute(['--force' => true], ['interactive' => true]);

    expect($tester->getStatusCode())->toBe(0);
    expect($tester->getDisplay())->toContain('No tool detected');
    $files = glob($tmpDir . '/.github/prompts/*.prompt.md') ?: [];
    expect($files)->not()->toBeEmpty();

    $fs->remove($tmpDir);
})->afterEach(fn () => chdir(dirname(dirname(__DIR__))));

// ─── Test: plusieurs outils détectés → question + sélection ──────────────────

it('lists detected tools and asks which one to update when multiple are found', function (): void {
    $tmpDir = sys_get_temp_dir() . '/directive-uprompts-test-' . uniqid();
    $fs = new Filesystem();
    $fs->mkdir($tmpDir . '/directive-spec/context');
    $fs->mkdir($tmpDir . '/.github/prompts');
    $fs->mkdir($tmpDir . '/.cursor/prompts');
    $yaml = "version: 1\n\nproject:\n  name: test-project\n  description: A test project\n\ncontext:\n  namespace: TestProject\n  stack: directive\n\nspecs:\n  path: directive-spec/specs/\n\nchanges:\n  path: directive-spec/changes/\n";
    file_put_contents($tmpDir . '/directive-spec/context/common.yaml', $yaml);
    chdir($tmpDir);

    $app = new Application();
    $command = $app->find('update-prompts');
    $tester  = new CommandTester($command);
    $tester->setInputs(['github-copilot']);
    $tester->execute(['--force' => true], ['interactive' => true]);

    expect($tester->getStatusCode())->toBe(0);
    expect($tester->getDisplay())->toContain('Multiple tools detected');
    // github-copilot was chosen: its prompts directory should have been populated
    $files = glob($tmpDir . '/.github/prompts/*.prompt.md') ?: [];
    expect($files)->not()->toBeEmpty();

    $fs->remove($tmpDir);
})->afterEach(fn () => chdir(dirname(dirname(__DIR__))));

// ─── Test: fichiers générés sans mode: agent ─────────────────────────────────

it('generates prompt files without deprecated mode: agent frontmatter', function (): void {
    $tmpDir = sys_get_temp_dir() . '/directive-uprompts-test-' . uniqid();
    createDirectiveProjectWithCopilot($tmpDir);
    chdir($tmpDir);

    $app = new Application();
    $command = $app->find('update-prompts');
    $tester  = new CommandTester($command);
    $tester->execute(['--force' => true], ['interactive' => false]);

    expect($tester->getStatusCode())->toBe(0);

    $files = glob($tmpDir . '/.github/prompts/*.prompt.md') ?: [];
    expect($files)->not()->toBeEmpty();

    foreach ($files as $file) {
        $content = file_get_contents($file);
        expect($content)->not()->toContain('mode: agent');
        expect($content)->toContain('description:');
    }

    (new Filesystem())->remove($tmpDir);
})->afterEach(fn () => chdir(dirname(dirname(__DIR__))));
