<?php

declare(strict_types=1);

use Directive\Cli\Generator\IdeAntigravityGenerator;
use Directive\Cli\Generator\IdeClaudeGenerator;
use Directive\Cli\Generator\IdeClineGenerator;
use Directive\Cli\Generator\IdeCodexGenerator;
use Directive\Cli\Generator\IdeContinueGenerator;
use Directive\Cli\Generator\IdeCursorGenerator;
use Directive\Cli\Generator\IdeGeminiGenerator;
use Directive\Cli\Generator\IdeGithubCopilotGenerator;
use Directive\Cli\Generator\IdeKiroGenerator;
use Directive\Cli\Generator\IdeQwenGenerator;
use Directive\Cli\Generator\IdeRoocodeGenerator;
use Directive\Cli\Generator\IdeWindsurfGenerator;
use Directive\Cli\Generator\ProjectContext;
use Symfony\Component\Filesystem\Filesystem;

// ─── GitHub Copilot ──────────────────────────────────────────────────────────

it('generates github copilot prompts for github-copilot tool', function (): void {
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

    // 12 prompts
    $prompts = [
        'directive-new', 'directive-continue', 'directive-apply', 'directive-verify',
        'directive-reflect', 'directive-learn', 'directive-archive',
        'directive-project', 'directive-stack', 'directive-discuss', 'directive-evaluate', 'directive-kickoff',
    ];
    foreach ($prompts as $prompt) {
        expect($tmpDir . '/.github/prompts/' . $prompt . '.prompt.md')->toBeFile($prompt);
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

    expect(is_dir($tmpDir . '/.github/prompts'))->toBeFalse();

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
        'directive-reflect', 'directive-learn', 'directive-archive',
        'directive-project', 'directive-stack', 'directive-discuss', 'directive-evaluate', 'directive-kickoff',
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
        'directive-reflect', 'directive-learn', 'directive-archive',
        'directive-project', 'directive-stack', 'directive-discuss', 'directive-evaluate', 'directive-kickoff',
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

    // Workflows (directive-* naming)
    $workflows = [
        'directive-new', 'directive-continue', 'directive-apply', 'directive-verify',
        'directive-reflect', 'directive-learn', 'directive-archive',
        'directive-project', 'directive-stack', 'directive-discuss', 'directive-evaluate', 'directive-kickoff',
    ];
    foreach ($workflows as $workflow) {
        expect($tmpDir . '/.agent/workflows/' . $workflow . '.md')->toBeFile($workflow);
    }

    $fs->remove($tmpDir);
});

// ─── Windsurf ─────────────────────────────────────────────────────────────────

it('generates windsurf workflows for windsurf tool', function (): void {
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

    $workflows = [
        'directive-new', 'directive-continue', 'directive-apply', 'directive-verify',
        'directive-reflect', 'directive-learn', 'directive-archive',
        'directive-project', 'directive-stack', 'directive-discuss', 'directive-evaluate', 'directive-kickoff',
    ];
    foreach ($workflows as $workflow) {
        expect($tmpDir . '/.windsurf/workflows/' . $workflow . '.md')->toBeFile($workflow);
    }

    $fs->remove($tmpDir);
});

// ─── Kiro ─────────────────────────────────────────────────────────────────────

it('generates kiro prompts for kiro tool', function (): void {
    $fs = new Filesystem();
    $tmpDir = sys_get_temp_dir() . '/directive-ide-test-' . uniqid();
    $fs->mkdir($tmpDir);

    $context = new ProjectContext(
        projectName: 'my-project',
        projectDir: $tmpDir,
        namespace: 'MyProject',
        tool: 'kiro',
        withDocker: false,
        containerName: '',
    );

    (new IdeKiroGenerator())->generate($context);

    $prompts = [
        'directive-new', 'directive-continue', 'directive-apply', 'directive-verify',
        'directive-reflect', 'directive-learn', 'directive-archive',
        'directive-project', 'directive-stack', 'directive-discuss', 'directive-evaluate', 'directive-kickoff',
    ];
    foreach ($prompts as $prompt) {
        expect($tmpDir . '/.kiro/prompts/' . $prompt . '.prompt.md')->toBeFile($prompt);
    }

    $fs->remove($tmpDir);
});

// ─── Roocode ──────────────────────────────────────────────────────────────────

it('generates roocode commands for roocode tool', function (): void {
    $fs = new Filesystem();
    $tmpDir = sys_get_temp_dir() . '/directive-ide-test-' . uniqid();
    $fs->mkdir($tmpDir);

    $context = new ProjectContext(
        projectName: 'my-project',
        projectDir: $tmpDir,
        namespace: 'MyProject',
        tool: 'roocode',
        withDocker: false,
        containerName: '',
    );

    (new IdeRoocodeGenerator())->generate($context);

    $commands = [
        'directive-new', 'directive-continue', 'directive-apply', 'directive-verify',
        'directive-reflect', 'directive-learn', 'directive-archive',
        'directive-project', 'directive-stack', 'directive-discuss', 'directive-evaluate', 'directive-kickoff',
    ];
    foreach ($commands as $command) {
        expect($tmpDir . '/.roo/commands/' . $command . '.md')->toBeFile($command);
    }

    $fs->remove($tmpDir);
});

// ─── Cline ────────────────────────────────────────────────────────────────────

it('generates cline workflows for cline tool', function (): void {
    $fs = new Filesystem();
    $tmpDir = sys_get_temp_dir() . '/directive-ide-test-' . uniqid();
    $fs->mkdir($tmpDir);

    $context = new ProjectContext(
        projectName: 'my-project',
        projectDir: $tmpDir,
        namespace: 'MyProject',
        tool: 'cline',
        withDocker: false,
        containerName: '',
    );

    (new IdeClineGenerator())->generate($context);

    $workflows = [
        'directive-new', 'directive-continue', 'directive-apply', 'directive-verify',
        'directive-reflect', 'directive-learn', 'directive-archive',
        'directive-project', 'directive-stack', 'directive-discuss', 'directive-evaluate', 'directive-kickoff',
    ];
    foreach ($workflows as $workflow) {
        expect($tmpDir . '/.clinerules/workflows/' . $workflow . '.md')->toBeFile($workflow);
    }

    $fs->remove($tmpDir);
});

// ─── Codex ────────────────────────────────────────────────────────────────────

it('generates codex prompts for codex tool', function (): void {
    $fs = new Filesystem();
    $tmpDir = sys_get_temp_dir() . '/directive-ide-test-' . uniqid();
    $fs->mkdir($tmpDir);

    $context = new ProjectContext(
        projectName: 'my-project',
        projectDir: $tmpDir,
        namespace: 'MyProject',
        tool: 'codex',
        withDocker: false,
        containerName: '',
    );

    (new IdeCodexGenerator())->generate($context);

    $homeDir = (string) getenv('HOME');
    $prompts = [
        'directive-new', 'directive-continue', 'directive-apply', 'directive-verify',
        'directive-reflect', 'directive-learn', 'directive-archive',
        'directive-project', 'directive-stack', 'directive-discuss', 'directive-evaluate', 'directive-kickoff',
    ];
    foreach ($prompts as $prompt) {
        expect($homeDir . '/.codex/prompts/' . $prompt . '.md')->toBeFile($prompt);
    }

    $fs->remove($tmpDir);
});

// ─── Continue ─────────────────────────────────────────────────────────────────

it('generates continue prompts for continue tool', function (): void {
    $fs = new Filesystem();
    $tmpDir = sys_get_temp_dir() . '/directive-ide-test-' . uniqid();
    $fs->mkdir($tmpDir);

    $context = new ProjectContext(
        projectName: 'my-project',
        projectDir: $tmpDir,
        namespace: 'MyProject',
        tool: 'continue',
        withDocker: false,
        containerName: '',
    );

    (new IdeContinueGenerator())->generate($context);

    $prompts = [
        'directive-new', 'directive-continue', 'directive-apply', 'directive-verify',
        'directive-reflect', 'directive-learn', 'directive-archive',
        'directive-project', 'directive-stack', 'directive-discuss', 'directive-evaluate', 'directive-kickoff',
    ];
    foreach ($prompts as $prompt) {
        expect($tmpDir . '/.continue/prompts/' . $prompt . '.prompt')->toBeFile($prompt);
    }

    $fs->remove($tmpDir);
});

// ─── Gemini ───────────────────────────────────────────────────────────────────

it('generates gemini commands for gemini tool', function (): void {
    $fs = new Filesystem();
    $tmpDir = sys_get_temp_dir() . '/directive-ide-test-' . uniqid();
    $fs->mkdir($tmpDir);

    $context = new ProjectContext(
        projectName: 'my-project',
        projectDir: $tmpDir,
        namespace: 'MyProject',
        tool: 'gemini',
        withDocker: false,
        containerName: '',
    );

    (new IdeGeminiGenerator())->generate($context);

    $commands = [
        'directive-new', 'directive-continue', 'directive-apply', 'directive-verify',
        'directive-reflect', 'directive-learn', 'directive-archive',
        'directive-project', 'directive-stack', 'directive-discuss', 'directive-evaluate', 'directive-kickoff',
    ];
    foreach ($commands as $command) {
        expect($tmpDir . '/.gemini/commands/dtsx/' . $command . '.toml')->toBeFile($command);
    }

    $fs->remove($tmpDir);
});

// ─── Qwen ─────────────────────────────────────────────────────────────────────

it('generates qwen commands for qwen tool', function (): void {
    $fs = new Filesystem();
    $tmpDir = sys_get_temp_dir() . '/directive-ide-test-' . uniqid();
    $fs->mkdir($tmpDir);

    $context = new ProjectContext(
        projectName: 'my-project',
        projectDir: $tmpDir,
        namespace: 'MyProject',
        tool: 'qwen',
        withDocker: false,
        containerName: '',
    );

    (new IdeQwenGenerator())->generate($context);

    $commands = [
        'directive-new', 'directive-continue', 'directive-apply', 'directive-verify',
        'directive-reflect', 'directive-learn', 'directive-archive',
        'directive-project', 'directive-stack', 'directive-discuss', 'directive-evaluate', 'directive-kickoff',
    ];
    foreach ($commands as $command) {
        expect($tmpDir . '/.qwen/commands/' . $command . '.md')->toBeFile($command);
    }

    $fs->remove($tmpDir);
});
