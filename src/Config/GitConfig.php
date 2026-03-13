<?php

declare(strict_types=1);

namespace Directive\Cli\Config;

final readonly class GitConfig
{
    public function __construct(
        public bool   $agentManaged,
        public string $defaultBranch,
        public string $baseBranch,
        public string $strategy = '',
        public string $branchPrefix = '',
        public string $commitMode = '',
        public string $commitPattern = '',
        public string $commitTemplate = '',
        public string $remote = '',
    ) {}
}
