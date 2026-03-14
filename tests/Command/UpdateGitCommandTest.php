<?php

declare(strict_types=1);

use Directive\Cli\Application;
use Directive\Cli\Command\GitConfigurationHelper;
use Directive\Cli\Command\UpdateGitCommand;
use Directive\Cli\Config\GitConfig;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

/**
 * Creates a minimal valid directive project in $dir.
 */
function createDirectiveProject(string $dir): void
{
    $fs = new Filesystem();
    $fs->mkdir($dir . '/directive-spec/context');
    $yaml = "version: 1\n\nproject:\n  name: test-project\n  description: A test project\n\ncontext:\n  namespace: TestProject\n  stack: directive\n\nspecs:\n  path: directive-spec/specs/\n\nchanges:\n  path: directive-spec/changes/\n";
    file_put_contents($dir . '/directive-spec/context/common.yaml', $yaml);
}

// ─── Test 9.1: git absent → FAILURE ─────────────────────────────────────────

it('returns failure when git is not available', function (): void {
    $tmpDir = sys_get_temp_dir() . '/directive-update-git-test-' . uniqid();
    (new Filesystem())->mkdir($tmpDir);
    createDirectiveProject($tmpDir);
    chdir($tmpDir);

    $fakeHelper = new class extends GitConfigurationHelper {
        public function isGitAvailable(): bool
        {
            return false;
        }
    };

    $app = new Application();
    $app->addCommand(new UpdateGitCommand($fakeHelper));
    $command = $app->find('update-git');
    $tester  = new CommandTester($command);
    $tester->execute([], ['interactive' => false]);

    expect($tester->getStatusCode())->toBe(1);
    expect($tester->getDisplay())->toContain('Git is not available');

    (new Filesystem())->remove($tmpDir);
})->afterEach(fn () => chdir(dirname(dirname(__DIR__))));

// ─── Test 9.2: existing config keys preserved, git section replaced ───────────

it('preserves existing config keys and replaces only the git section', function (): void {
    $tmpDir = sys_get_temp_dir() . '/directive-update-git-test-' . uniqid();
    (new Filesystem())->mkdir($tmpDir);
    createDirectiveProject($tmpDir);

    // Write a config with an existing git section
    $configPath = $tmpDir . '/directive-spec/context/common.yaml';
    $existing = Yaml::parseFile($configPath);
    /** @var array<string, mixed> $existing */
    $existing['git'] = ['agent_managed' => false, 'default_branch' => 'old-branch'];
    file_put_contents($configPath, Yaml::dump($existing, 4, 2));

    chdir($tmpDir);

    $fakeHelper = new class extends GitConfigurationHelper {
        public function isGitAvailable(): bool
        {
            return true;
        }

        public function askGitQuestions(SymfonyStyle $io): ?GitConfig
        {
            return new GitConfig(
                agentManaged:  true,
                defaultBranch: 'main',
                baseBranch:    'main',
                strategy:      'feature-branch',
                branchPrefix:  'feat/',
                commitMode:    'auto',
                commitPattern: 'conventional',
            );
        }
    };

    $app = new Application();
    $app->addCommand(new UpdateGitCommand($fakeHelper));
    $command = $app->find('update-git');
    $tester  = new CommandTester($command);
    $tester->execute([], ['interactive' => false]);

    expect($tester->getStatusCode())->toBe(0);

    /** @var array<string, mixed> $config */
    $config = Yaml::parseFile($configPath);

    // Original keys preserved
    expect($config)->toHaveKey('project');
    expect($config)->toHaveKey('context');
    expect($config)->toHaveKey('specs');
    expect($config)->toHaveKey('changes');

    // Git section replaced
    /** @var array<string, mixed> $git */
    $git = $config['git'];
    expect($git['agent_managed'])->toBeTrue();
    expect($git['default_branch'])->toBe('main');

    (new Filesystem())->remove($tmpDir);
})->afterEach(fn () => chdir(dirname(dirname(__DIR__))));

// ─── Test 9.3: config without git section gets git key added ─────────────────

it('adds git section to config that has none', function (): void {
    $tmpDir = sys_get_temp_dir() . '/directive-update-git-test-' . uniqid();
    (new Filesystem())->mkdir($tmpDir);
    createDirectiveProject($tmpDir);

    $configPath = $tmpDir . '/directive-spec/context/common.yaml';
    chdir($tmpDir);

    $fakeHelper = new class extends GitConfigurationHelper {
        public function isGitAvailable(): bool
        {
            return true;
        }

        public function askGitQuestions(SymfonyStyle $io): ?GitConfig
        {
            return new GitConfig(agentManaged: false, defaultBranch: 'trunk', baseBranch: 'trunk');
        }
    };

    $app = new Application();
    $app->addCommand(new UpdateGitCommand($fakeHelper));
    $command = $app->find('update-git');
    $tester  = new CommandTester($command);
    $tester->execute([], ['interactive' => false]);

    expect($tester->getStatusCode())->toBe(0);

    /** @var array<string, mixed> $config */
    $config = Yaml::parseFile($configPath);

    // Original keys still present
    expect($config)->toHaveKey('project');
    expect($config)->toHaveKey('context');

    // New git key added
    expect($config)->toHaveKey('git');
    /** @var array<string, mixed> $git */
    $git = $config['git'];
    expect($git['agent_managed'])->toBeFalse();
    expect($git['default_branch'])->toBe('trunk');

    (new Filesystem())->remove($tmpDir);
})->afterEach(fn () => chdir(dirname(dirname(__DIR__))));
