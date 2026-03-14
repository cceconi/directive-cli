<?php

declare(strict_types=1);

use Directive\Cli\Application;
use Directive\Cli\Command\GitConfigurationHelper;
use Directive\Cli\Command\NewProjectCommand;
use Directive\Cli\Config\GitConfig;
use Directive\Cli\Generator\CoreGenerator;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Yaml\Yaml;

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

// ─── Git tests ────────────────────────────────────────────────────────────────

it('shows warning and writes minimal git config when git is absent', function (): void {
    $tmpDir = sys_get_temp_dir() . '/directive-test-' . uniqid();
    chdir(sys_get_temp_dir());

    $fakeHelper = new class extends GitConfigurationHelper {
        public function isGitAvailable(): bool
        {
            return false;
        }
    };

    $app = new Application();
    $app->addCommand(new NewProjectCommand([new CoreGenerator()], $fakeHelper));
    $command = $app->find('new');
    $tester  = new CommandTester($command);
    $tester->execute(['project-name' => basename($tmpDir)], ['interactive' => false]);

    expect($tester->getStatusCode())->toBe(0);
    expect($tester->getDisplay())->toContain('Git is not available');

    $configPath = $tmpDir . '/directive-spec/context/common.yaml';
    expect($configPath)->toBeFile();
    /** @var array<string, mixed> $config */
    $config = Yaml::parseFile($configPath);
    expect($config)->toHaveKey('git');
    /** @var array<string, mixed> $git */
    $git = $config['git'];
    expect($git['agent_managed'])->toBeFalse();

    (new Symfony\Component\Filesystem\Filesystem())->remove($tmpDir);
})->afterEach(fn () => chdir(dirname(dirname(__DIR__))));

it('skips git section in config when user refuses git init', function (): void {
    $tmpDir = sys_get_temp_dir() . '/directive-test-' . uniqid();
    chdir(sys_get_temp_dir());

    $fakeHelper = new class extends GitConfigurationHelper {
        public function isGitAvailable(): bool
        {
            return true;
        }

        public function askGitQuestions(SymfonyStyle $io): ?GitConfig
        {
            return null;
        }
    };

    $app = new Application();
    $app->addCommand(new NewProjectCommand([new CoreGenerator()], $fakeHelper));
    $command = $app->find('new');
    $tester  = new CommandTester($command);
    $tester->execute(['project-name' => basename($tmpDir)], ['interactive' => false]);

    expect($tester->getStatusCode())->toBe(0);

    $configPath = $tmpDir . '/directive-spec/context/common.yaml';
    /** @var array<string, mixed> $config */
    $config = Yaml::parseFile($configPath);
    expect($config)->not->toHaveKey('git');

    (new Symfony\Component\Filesystem\Filesystem())->remove($tmpDir);
})->afterEach(fn () => chdir(dirname(dirname(__DIR__))));

it('writes non-agent git config when user selects git without agent management', function (): void {
    $tmpDir = sys_get_temp_dir() . '/directive-test-' . uniqid();
    chdir(sys_get_temp_dir());

    $fakeHelper = new class extends GitConfigurationHelper {
        public function isGitAvailable(): bool
        {
            return true;
        }

        public function askGitQuestions(SymfonyStyle $io): ?GitConfig
        {
            return new GitConfig(agentManaged: false, defaultBranch: 'main', baseBranch: 'main');
        }

        public function initRepository(string $dir, string $defaultBranch): void {}

        public function configureLocalUser(string $dir): void {}

        public function createInitialCommit(string $dir): void {}
    };

    $app = new Application();
    $app->addCommand(new NewProjectCommand([new CoreGenerator()], $fakeHelper));
    $command = $app->find('new');
    $tester  = new CommandTester($command);
    $tester->execute(['project-name' => basename($tmpDir)], ['interactive' => false]);

    expect($tester->getStatusCode())->toBe(0);

    /** @var array<string, mixed> $config */
    $config = Yaml::parseFile($tmpDir . '/directive-spec/context/common.yaml');
    /** @var array<string, mixed> $git */
    $git = $config['git'];
    expect($git['agent_managed'])->toBeFalse();
    expect($git['default_branch'])->toBe('main');
    expect($git)->not->toHaveKey('strategy');

    (new Symfony\Component\Filesystem\Filesystem())->remove($tmpDir);
})->afterEach(fn () => chdir(dirname(dirname(__DIR__))));

it('writes full agent git config with base_branch equal to default_branch for feature-branch strategy', function (): void {
    $tmpDir = sys_get_temp_dir() . '/directive-test-' . uniqid();
    chdir(sys_get_temp_dir());

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

        public function initRepository(string $dir, string $defaultBranch): void {}

        public function configureLocalUser(string $dir): void {}

        public function createInitialCommit(string $dir): void {}
    };

    $app = new Application();
    $app->addCommand(new NewProjectCommand([new CoreGenerator()], $fakeHelper));
    $command = $app->find('new');
    $tester  = new CommandTester($command);
    $tester->execute(['project-name' => basename($tmpDir)], ['interactive' => false]);

    expect($tester->getStatusCode())->toBe(0);

    /** @var array<string, mixed> $config */
    $config = Yaml::parseFile($tmpDir . '/directive-spec/context/common.yaml');
    /** @var array<string, mixed> $git */
    $git = $config['git'];
    expect($git['agent_managed'])->toBeTrue();
    expect($git['default_branch'])->toBe('main');
    expect($git['base_branch'])->toBe('main');
    expect($git['strategy'])->toBe('feature-branch');
    expect($git['commit_mode'])->toBe('auto');
    expect($git['commit_pattern'])->toBe('conventional');

    (new Symfony\Component\Filesystem\Filesystem())->remove($tmpDir);
})->afterEach(fn () => chdir(dirname(dirname(__DIR__))));

it('writes base_branch as develop for gitflow strategy', function (): void {
    $tmpDir = sys_get_temp_dir() . '/directive-test-' . uniqid();
    chdir(sys_get_temp_dir());

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
                baseBranch:    'develop',
                strategy:      'gitflow',
                branchPrefix:  'feat/',
                commitMode:    'auto',
                commitPattern: 'conventional',
            );
        }

        public function initRepository(string $dir, string $defaultBranch): void {}

        public function configureLocalUser(string $dir): void {}

        public function createInitialCommit(string $dir): void {}

        public function createDevelopBranch(string $dir, string $defaultBranch): void {}
    };

    $app = new Application();
    $app->addCommand(new NewProjectCommand([new CoreGenerator()], $fakeHelper));
    $command = $app->find('new');
    $tester  = new CommandTester($command);
    $tester->execute(['project-name' => basename($tmpDir)], ['interactive' => false]);

    expect($tester->getStatusCode())->toBe(0);

    /** @var array<string, mixed> $config */
    $config = Yaml::parseFile($tmpDir . '/directive-spec/context/common.yaml');
    /** @var array<string, mixed> $git */
    $git = $config['git'];
    expect($git['base_branch'])->toBe('develop');
    expect($git['strategy'])->toBe('gitflow');

    (new Symfony\Component\Filesystem\Filesystem())->remove($tmpDir);
})->afterEach(fn () => chdir(dirname(dirname(__DIR__))));
