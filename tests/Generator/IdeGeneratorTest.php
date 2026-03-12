<?php

declare(strict_types=1);

use Directive\Cli\Generator\IdeAntigravityGenerator;
use Directive\Cli\Generator\IdeClaudeGenerator;
use Directive\Cli\Generator\IdeCursorGenerator;
use Directive\Cli\Generator\IdeGithubCopilotGenerator;
use Directive\Cli\Generator\IdeWindsurfGenerator;
use Directive\Cli\Generator\ProjectContext;
use Symfony\Component\Filesystem\Filesystem;

// ─── GitHub Copilot ──────────────────────────────────────────────────────────

it('generates all github copilot files for github-copilot tool', function (): void {
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

    (new IdeGithubCopilotGenerator())->generate($context);

    // Copilot instructions — namespace injected
    expect($tmpDir . '/.github/copilot-instructions.md')->toBeFile();
    expect(file_get_contents($tmpDir . '/.github/copilot-instructions.md'))->toContain('MyProject');

    // Prompts
    $prompts = [
        'directive-new', 'directive-continue', 'directive-apply', 'directive-verify',
        'directive-reflect', 'directive-learn', 'directive-archive',
        'directive-project', 'directive-stack', 'directive-discuss', 'directive-evaluate',
    ];
    foreach ($prompts as $prompt) {
        expect($tmpDir . '/.github/prompts/' . $prompt . '.prompt.md')->toBeFile($prompt);
    }

    // Skills
    $skills = [
        'directive-new-change', 'directive-continue-change', 'directive-apply-change',
        'directive-verify-change', 'directive-reflect-change', 'directive-learn-change',
        'directive-archive-change', 'directive-project-context', 'directive-stack-context', 'directive-discuss-context', 'directive-evaluate-context',
    ];
    foreach ($skills as $skill) {
        expect($tmpDir . '/.github/skills/' . $skill . '/SKILL.md')->toBeFile($skill);
    }

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

    (new IdeGithubCopilotGenerator())->generate($context);

    expect(file_exists($tmpDir . '/.github/copilot-instructions.md'))->toBeFalse();

    $fs->remove($tmpDir);
});

// ─── Cursor ───────────────────────────────────────────────────────────────────

it('generates all cursor prompt files for cursor tool', function (): void {
    $fs = new Filesystem();
    $tmpDir = sys_get_temp_dir() . '/directive-ide-test-' . uniqid();
    $fs->mkdir($tmpDir);

    $context = new ProjectContext(
        projectName: 'my-project',
        projectDir: $tmpDir,
        namespace: 'MyProject',
        tool: 'cursor',
        withDocker: false,
        containerName: '',
    );

    (new IdeCursorGenerator())->generate($context);

    $prompts = [
        'directive-new', 'directive-continue', 'directive-apply', 'directive-verify',
        'directive-reflect', 'directive-learn',
        'directive-project', 'directive-stack', 'directive-discuss', 'directive-evaluate',
    ];
    foreach ($prompts as $prompt) {
        expect($tmpDir . '/.cursor/prompts/' . $prompt . '.md')->toBeFile($prompt);
    }

    $fs->remove($tmpDir);
});

// ─── Claude ───────────────────────────────────────────────────────────────────

it('generates all claude command files for claude tool', function (): void {
    $fs = new Filesystem();
    $tmpDir = sys_get_temp_dir() . '/directive-ide-test-' . uniqid();
    $fs->mkdir($tmpDir);

    $context = new ProjectContext(
        projectName: 'my-project',
        projectDir: $tmpDir,
        namespace: 'MyProject',
        tool: 'claude',
        withDocker: false,
        containerName: '',
    );

    (new IdeClaudeGenerator())->generate($context);

    $commands = [
        'directive-new', 'directive-continue', 'directive-apply', 'directive-verify',
        'directive-reflect', 'directive-learn',
        'directive-project', 'directive-stack', 'directive-discuss', 'directive-evaluate',
    ];
    foreach ($commands as $command) {
        expect($tmpDir . '/.claude/commands/' . $command . '.md')->toBeFile($command);
    }

    $fs->remove($tmpDir);
});

// ─── Antigravity ──────────────────────────────────────────────────────────────

it('generates all antigravity files for antigravity tool', function (): void {
    $fs = new Filesystem();
    $tmpDir = sys_get_temp_dir() . '/directive-ide-test-' . uniqid();
    $fs->mkdir($tmpDir);

    $context = new ProjectContext(
        projectName: 'my-project',
        projectDir: $tmpDir,
        namespace: 'MyProject',
        tool: 'antigravity',
        withDocker: false,
        containerName: '',
    );

    (new IdeAntigravityGenerator())->generate($context);

    // Workflows
    $workflows = [
        'new-change', 'continue-change', 'apply-change', 'verify-change',
        'reflect-change', 'learn-change', 'archive-change',
        'project-context', 'stack-context', 'discuss-session', 'evaluate-session',
    ];
    foreach ($workflows as $workflow) {
        expect($tmpDir . '/.agent/workflows/' . $workflow . '.md')->toBeFile($workflow);
    }

    // Skills
    $skills = [
        'directive-new-change', 'directive-continue-change', 'directive-apply-change',
        'directive-verify-change', 'directive-reflect-change', 'directive-learn-change',
        'directive-archive-change', 'directive-project-context', 'directive-stack-context', 'directive-discuss-context', 'directive-evaluate-context',
    ];
    foreach ($skills as $skill) {
        expect($tmpDir . '/.agent/skills/' . $skill . '/SKILL.md')->toBeFile($skill);
    }

    $fs->remove($tmpDir);
});

// ─── Windsurf ─────────────────────────────────────────────────────────────────

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

    (new IdeWindsurfGenerator())->generate($context);

    expect($tmpDir . '/.windsurfrules')->toBeFile();

    $fs->remove($tmpDir);
});
