<?php

declare(strict_types=1);

namespace Directive\Cli\Command;

use Directive\Cli\Config\GitConfig;
use Symfony\Component\Console\Style\SymfonyStyle;

class GitConfigurationHelper
{
    private string $pendingUserName  = '';
    private string $pendingUserEmail = '';

    public function isGitAvailable(): bool
    {
        $command = DIRECTORY_SEPARATOR === '\\' ? 'where git' : 'which git';
        /** @var array<int,string> $out */
        $out  = [];
        $code = 0;
        exec($command . ' 2>/dev/null', $out, $code);

        return $code === 0;
    }

    public function getGlobalConfig(string $key): string
    {
        /** @var array<int,string> $out */
        $out  = [];
        $code = 0;
        exec('git config --global ' . escapeshellarg($key) . ' 2>/dev/null', $out, $code);

        if ($code !== 0 || empty($out)) {
            return '';
        }

        return trim($out[0]);
    }

    /**
     * Ask the full git question sequence. Returns null if the user declines git init.
     */
    public function askGitQuestions(SymfonyStyle $io): ?GitConfig
    {
        $this->pendingUserName  = '';
        $this->pendingUserEmail = '';

        // Check / ask for git user identity
        if ($this->getGlobalConfig('user.name') === '') {
            $result = $io->ask('Git user name (not configured globally)');
            $this->pendingUserName = is_string($result) ? $result : '';
        }
        if ($this->getGlobalConfig('user.email') === '') {
            $result = $io->ask('Git user email (not configured globally)');
            $this->pendingUserEmail = is_string($result) ? $result : '';
        }

        // Ask if user wants git
        if (!$io->confirm('Initialize a Git repository?', true)) {
            return null;
        }

        // Default branch name
        $result        = $io->ask('Default branch name', 'main');
        $defaultBranch = is_string($result) && $result !== '' ? $result : 'main';

        // Agent-managed?
        if (!$io->confirm('Let AI agents manage git (commits, branches, workflow)?', true)) {
            return new GitConfig(
                agentManaged:  false,
                defaultBranch: $defaultBranch,
                baseBranch:    $defaultBranch,
            );
        }

        // Agent-managed questions
        $result = $io->ask('Remote origin URL (optional, press Enter to skip)', null);
        $remote = is_string($result) ? $result : '';

        $strategyResult = $io->choice(
            'Branch strategy',
            ['feature-branch', 'trunk-based', 'gitflow'],
            'feature-branch'
        );
        $strategy = is_string($strategyResult) ? $strategyResult : 'feature-branch';

        $commitModeResult = $io->choice('Commit mode', ['auto', 'manual'], 'auto');
        $commitMode = is_string($commitModeResult) ? $commitModeResult : 'auto';

        $commitPatternResult = $io->choice(
            'Commit pattern',
            ['conventional', 'free', 'custom'],
            'conventional'
        );
        $commitPattern = is_string($commitPatternResult) ? $commitPatternResult : 'conventional';

        $commitTemplate = '';
        if ($commitPattern === 'custom') {
            $result = $io->ask(
                'Commit template (use {type}, {change}, {summary}, {date})',
                '{type}({change}): {summary}'
            );
            $commitTemplate = is_string($result) ? $result : '{type}({change}): {summary}';
        }

        $result       = $io->ask('Branch prefix', 'feat/');
        $branchPrefix = is_string($result) && $result !== '' ? $result : 'feat/';

        $baseBranch = $strategy === 'gitflow' ? 'develop' : $defaultBranch;

        return new GitConfig(
            agentManaged:   true,
            defaultBranch:  $defaultBranch,
            baseBranch:     $baseBranch,
            strategy:       $strategy,
            branchPrefix:   $branchPrefix,
            commitMode:     $commitMode,
            commitPattern:  $commitPattern,
            commitTemplate: $commitTemplate,
            remote:         $remote,
        );
    }

    public function initRepository(string $dir, string $defaultBranch): void
    {
        /** @var array<int,string> $out */
        $out  = [];
        $code = 0;
        exec(
            'cd ' . escapeshellarg($dir) . ' && git init -b ' . escapeshellarg($defaultBranch) . ' 2>/dev/null',
            $out,
            $code
        );
    }

    public function createInitialCommit(string $dir): void
    {
        // Skip if a commit already exists
        /** @var array<int,string> $existing */
        $existing     = [];
        $existingCode = 0;
        exec(
            'cd ' . escapeshellarg($dir) . ' && git log --oneline -1 2>/dev/null',
            $existing,
            $existingCode
        );
        if ($existingCode === 0 && !empty($existing)) {
            return;
        }

        /** @var array<int,string> $out */
        $out  = [];
        $code = 0;
        exec(
            'cd ' . escapeshellarg($dir)
                . ' && git add -A && git commit --allow-empty -m "chore: init" 2>/dev/null',
            $out,
            $code
        );
    }

    public function createDevelopBranch(string $dir, string $defaultBranch): void
    {
        /** @var array<int,string> $out */
        $out  = [];
        $code = 0;
        exec(
            'cd ' . escapeshellarg($dir) . ' && git checkout -b develop 2>/dev/null',
            $out,
            $code
        );

        /** @var array<int,string> $out2 */
        $out2  = [];
        $code2 = 0;
        exec(
            'cd ' . escapeshellarg($dir) . ' && git checkout ' . escapeshellarg($defaultBranch) . ' 2>/dev/null',
            $out2,
            $code2
        );
    }

    public function configureLocalUser(string $dir): void
    {
        if ($this->pendingUserName !== '') {
            /** @var array<int,string> $out */
            $out = [];
            exec(
                'cd ' . escapeshellarg($dir)
                    . ' && git config user.name ' . escapeshellarg($this->pendingUserName) . ' 2>/dev/null',
                $out
            );
        }

        if ($this->pendingUserEmail !== '') {
            /** @var array<int,string> $out */
            $out = [];
            exec(
                'cd ' . escapeshellarg($dir)
                    . ' && git config user.email ' . escapeshellarg($this->pendingUserEmail) . ' 2>/dev/null',
                $out
            );
        }
    }
}
