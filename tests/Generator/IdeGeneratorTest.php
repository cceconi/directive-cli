<?php

declare(strict_types=1);

use Directive\Cli\Generator\IdeGithubCopilotGenerator;
use Directive\Cli\Generator\IdeWindsurfGenerator;
use Directive\Cli\Generator\ProjectContext;
use Symfony\Component\Filesystem\Filesystem;

it('generates github copilot files for github-copilot tool', function (): void {
    $fs = new Filesystem();
    $tmpDir = sys_get_temp_dir() . '/directive-ide-test-' . uniqid();
    $fs->mkdir($tmpDir);

    $context = new ProjectContext(
        projectName: 'my-project',
        projectDir: $tmpDir,
        namespace: 'MyProject',
        tool: 'github-copilot',
        withDocker: false,
        containerName: '',
    );

    $generator = new IdeGithubCopilotGenerator();
    $generator->generate($context);

    expect(file_exists($tmpDir . '/.github/copilot-instructions.md'))->toBeTrue();
    expect(file_exists($tmpDir . '/.github/prompts/directive-new.prompt.md'))->toBeTrue();
    expect(file_exists($tmpDir . '/.github/skills/directive-apply-change/SKILL.md'))->toBeTrue();

    // namespace is mentioned in copilot-instructions.md
    expect(file_get_contents($tmpDir . '/.github/copilot-instructions.md'))->toContain('MyProject');

    $fs->remove($tmpDir);
});

it('skips github copilot files for other tools', function (): void {
    $fs = new Filesystem();
    $tmpDir = sys_get_temp_dir() . '/directive-ide-test-' . uniqid();
    $fs->mkdir($tmpDir);

    $context = new ProjectContext(
        projectName: 'my-project',
        projectDir: $tmpDir,
        namespace: 'MyProject',
        tool: 'windsurf',
        withDocker: false,
        containerName: '',
    );

    $generator = new IdeGithubCopilotGenerator();
    $generator->generate($context);

    expect(file_exists($tmpDir . '/.github/copilot-instructions.md'))->toBeFalse();

    $fs->remove($tmpDir);
});

it('generates windsurfrules for windsurf tool', function (): void {
    $fs = new Filesystem();
    $tmpDir = sys_get_temp_dir() . '/directive-ide-test-' . uniqid();
    $fs->mkdir($tmpDir);

    $context = new ProjectContext(
        projectName: 'my-project',
        projectDir: $tmpDir,
        namespace: 'MyProject',
        tool: 'windsurf',
        withDocker: false,
        containerName: '',
    );

    $generator = new IdeWindsurfGenerator();
    $generator->generate($context);

    expect(file_exists($tmpDir . '/.windsurfrules'))->toBeTrue();

    $fs->remove($tmpDir);
});
