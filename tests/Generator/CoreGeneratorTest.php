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
    expect(file_exists($tmpDir . '/bin/app'))->toBeTrue();
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

    // namespace + paths in common.yaml
    $commonYaml = file_get_contents($tmpDir . '/directive-spec/context/common.yaml');
    expect($commonYaml)->toContain('MyProject');
    expect($commonYaml)->toContain('directive-spec/specs/');
    expect($commonYaml)->toContain('directive-spec/changes/');

    $fs->remove($tmpDir);
});
