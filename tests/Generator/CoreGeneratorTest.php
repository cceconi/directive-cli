<?php

declare(strict_types=1);

use Directive\Cli\Generator\CoreGenerator;
use Directive\Cli\Generator\ProjectContext;
use Symfony\Component\Filesystem\Filesystem;

it('generates core project files', function (): void {
    $fs = new Filesystem();
    $tmpDir = sys_get_temp_dir() . '/directive-core-test-' . uniqid();

    $context = new ProjectContext(
        projectName: 'my-project',
        projectDir: $tmpDir,
        namespace: 'MyProject',
        tool: 'none',
        withDocker: false,
        containerName: '',
    );

    $fs->mkdir($tmpDir);

    $generator = new CoreGenerator();
    $generator->generate($context);

    expect(file_exists($tmpDir . '/composer.json'))->toBeTrue();
    expect(file_exists($tmpDir . '/phpstan.neon'))->toBeTrue();
    expect(file_exists($tmpDir . '/.php-cs-fixer.php'))->toBeTrue();
    expect(file_exists($tmpDir . '/.gitignore'))->toBeTrue();
    expect(file_exists($tmpDir . '/.env'))->toBeTrue();
    expect(file_get_contents($tmpDir . '/.env'))->toContain('APP_ENV=dev');
    expect(file_exists($tmpDir . '/bin/app'))->toBeTrue();
    expect(file_exists($tmpDir . '/public/index.php'))->toBeTrue();
    expect(file_exists($tmpDir . '/tests/Pest.php'))->toBeTrue();
    expect(file_exists($tmpDir . '/directive-spec/context/common.yaml'))->toBeTrue();

    // Hexagonal structure
    expect(is_dir($tmpDir . '/src/Application'))->toBeTrue();
    expect(is_dir($tmpDir . '/src/Domain'))->toBeTrue();
    expect(is_dir($tmpDir . '/src/Infrastructure/Http'))->toBeTrue();
    expect(is_dir($tmpDir . '/src/Infrastructure/Console'))->toBeTrue();
    expect(is_dir($tmpDir . '/src/Infrastructure/Persistence'))->toBeTrue();
    expect(is_dir($tmpDir . '/src/Infrastructure/Security'))->toBeTrue();
    expect(file_exists($tmpDir . '/src/Application/.gitkeep'))->toBeTrue();
    expect(file_exists($tmpDir . '/src/Domain/.gitkeep'))->toBeTrue();

    // directive-spec/ structure
    expect(is_dir($tmpDir . '/directive-spec/specs'))->toBeTrue();
    expect(file_exists($tmpDir . '/directive-spec/specs/.gitkeep'))->toBeTrue();
    expect(is_dir($tmpDir . '/directive-spec/changes'))->toBeTrue();
    expect(file_exists($tmpDir . '/directive-spec/changes/.gitkeep'))->toBeTrue();
    expect(is_dir($tmpDir . '/directive-spec/changes/archive'))->toBeTrue();
    expect(file_exists($tmpDir . '/directive-spec/changes/archive/.gitkeep'))->toBeTrue();
    expect(is_dir($tmpDir . '/directive-spec/brainstorm'))->toBeTrue();
    expect(file_exists($tmpDir . '/directive-spec/brainstorm/.gitkeep'))->toBeTrue();

    // namespace + paths in common.yaml
    $commonYaml = file_get_contents($tmpDir . '/directive-spec/context/common.yaml');
    expect($commonYaml)->toContain('MyProject');
    expect($commonYaml)->toContain('directive-spec/specs/');
    expect($commonYaml)->toContain('directive-spec/changes/');

    // Infrastructure bootstrap classes
    expect(file_exists($tmpDir . '/src/Infrastructure/WebApplication.php'))->toBeTrue();
    expect(file_exists($tmpDir . '/src/Infrastructure/ConsoleApplication.php'))->toBeTrue();

    $webApp = (string) file_get_contents($tmpDir . '/src/Infrastructure/WebApplication.php');
    expect($webApp)->toContain('namespace MyProject\\Infrastructure;');
    expect($webApp)->toContain('extends AbstractWebApplication');

    $consoleApp = (string) file_get_contents($tmpDir . '/src/Infrastructure/ConsoleApplication.php');
    expect($consoleApp)->toContain('namespace MyProject\\Infrastructure;');
    expect($consoleApp)->toContain('extends AbstractConsoleApplication');

    // AppConfig
    expect(file_exists($tmpDir . '/src/Infrastructure/Config/AppConfig.php'))->toBeTrue();

    $appConfig = (string) file_get_contents($tmpDir . '/src/Infrastructure/Config/AppConfig.php');
    expect($appConfig)->toContain('namespace MyProject\\Infrastructure\\Config;');
    expect($appConfig)->toContain('extends AbstractConfiguration');

    // var/ runtime directories
    expect(is_dir($tmpDir . '/var/log'))->toBeTrue();
    expect(is_dir($tmpDir . '/var/cache'))->toBeTrue();

    // Entry points wired to Infrastructure classes
    $indexPhp = (string) file_get_contents($tmpDir . '/public/index.php');
    expect($indexPhp)->toContain('Infrastructure\\WebApplication');
    expect($indexPhp)->toContain('AppConfig::class');
    expect($indexPhp)->not->toContain('// TODO');

    $binApp = (string) file_get_contents($tmpDir . '/bin/app');
    expect($binApp)->toContain('Infrastructure\\ConsoleApplication');
    expect($binApp)->toContain('AppConfig::class');
    expect($binApp)->toContain('addCommands([])');

    $fs->remove($tmpDir);
});

it('generates composer.json without repositories in normal mode', function (): void {
    $fs = new Filesystem();
    $tmpDir = sys_get_temp_dir() . '/directive-core-test-' . uniqid();

    $context = new ProjectContext(
        projectName: 'my-project',
        projectDir: $tmpDir,
        namespace: 'MyProject',
        tool: 'none',
        withDocker: false,
        containerName: '',
        localMode: false,
    );

    $fs->mkdir($tmpDir);
    (new CoreGenerator())->generate($context);

    /** @var string $raw */
    $raw = file_get_contents($tmpDir . '/composer.json');
    /** @var array<string, mixed> $json */
    $json = json_decode($raw, true);

    expect($json)->not->toHaveKey('repositories');
    expect($json['require']['cceconi/directive'])->toBe('^1.0');

    $fs->remove($tmpDir);
});

it('generates composer.json with path repository in local mode', function (): void {
    $fs = new Filesystem();
    $tmpDir = sys_get_temp_dir() . '/directive-core-test-' . uniqid();

    $context = new ProjectContext(
        projectName: 'my-project',
        projectDir: $tmpDir,
        namespace: 'MyProject',
        tool: 'none',
        withDocker: false,
        containerName: '',
        localMode: true,
    );

    $fs->mkdir($tmpDir);
    (new CoreGenerator())->generate($context);

    /** @var string $raw */
    $raw = file_get_contents($tmpDir . '/composer.json');
    /** @var array<string, mixed> $json */
    $json = json_decode($raw, true);

    expect($json)->toHaveKey('repositories');
    /** @var array<int, array<string, string>> $repos */
    $repos = $json['repositories'];
    expect($repos[0]['type'])->toBe('path');
    expect($repos[0]['url'])->toBe('/web/directive');
    expect($json['require']['cceconi/directive'])->toBe('*@dev');

    // Verify all other keys are preserved in local mode
    expect($json)->toHaveKey('require-dev');
    expect($json)->toHaveKey('autoload');
    expect($json)->toHaveKey('autoload-dev');
    expect($json)->toHaveKey('scripts');
    expect($json)->toHaveKey('config');

    $fs->remove($tmpDir);
});

it('uses custom directive-path in local mode', function (): void {
    $fs = new Filesystem();
    $tmpDir = sys_get_temp_dir() . '/directive-core-test-' . uniqid();

    $context = new ProjectContext(
        projectName: 'my-project',
        projectDir: $tmpDir,
        namespace: 'MyProject',
        tool: 'none',
        withDocker: false,
        containerName: '',
        localMode: true,
        directivePath: '/custom/path',
    );

    $fs->mkdir($tmpDir);
    (new CoreGenerator())->generate($context);

    /** @var string $raw */
    $raw = file_get_contents($tmpDir . '/composer.json');
    /** @var array<string, mixed> $json */
    $json = json_decode($raw, true);

    /** @var array<int, array<string, string>> $repos */
    $repos = $json['repositories'];
    expect($repos[0]['url'])->toBe('/custom/path');

    $fs->remove($tmpDir);
});
