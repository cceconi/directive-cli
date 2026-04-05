<?php

declare(strict_types=1);

namespace Directive\Cli\Generator;

final readonly class ProjectContext
{
    public const array COMMANDS = [
        'directive-new',
        'directive-continue',
        'directive-propose',
        'directive-apply',
        'directive-verify',
        'directive-reflect',
        'directive-learn',
        'directive-sync',
        'directive-archive',
        'directive-project',
        'directive-stack',
        'directive-discuss',
        'directive-evaluate',
        'directive-kickoff',
        'directive-commit',
    ];

    public const array LABELS = [
        'directive-new'      => 'New change',
        'directive-continue' => 'Continue change',
        'directive-propose'  => 'Propose change',
        'directive-apply'    => 'Apply change',
        'directive-verify'   => 'Verify change',
        'directive-reflect'  => 'Reflect change',
        'directive-learn'    => 'Learn change',
        'directive-sync'     => 'Sync specs',
        'directive-archive'  => 'Archive change',
        'directive-project'  => 'Project context',
        'directive-stack'    => 'Stack context',
        'directive-discuss'  => 'Discuss session',
        'directive-evaluate' => 'Evaluate session',
        'directive-kickoff'  => 'Kickoff session',
        'directive-commit'   => 'Commit change',
    ];

    public function __construct(
        public string $projectName,
        public string $projectDir,
        public string $namespace,
        public string $tool,
        public bool $withDocker,
        public string $containerName,
        public bool $localMode = false,
        public string $directivePath = '/web/directive',
    ) {
    }
}
