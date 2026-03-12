<?php

declare(strict_types=1);

namespace Directive\Cli\Generator;

final readonly class ProjectContext
{
    public const array COMMANDS = [
        'directive-new',
        'directive-continue',
        'directive-apply',
        'directive-verify',
        'directive-reflect',
        'directive-learn',
        'directive-archive',
        'directive-project',
        'directive-stack',
        'directive-discuss',
        'directive-evaluate',
        'directive-kickoff',
    ];

    public const array LABELS = [
        'directive-new'      => 'New change',
        'directive-continue' => 'Continue change',
        'directive-apply'    => 'Apply change',
        'directive-verify'   => 'Verify change',
        'directive-reflect'  => 'Reflect change',
        'directive-learn'    => 'Learn change',
        'directive-archive'  => 'Archive change',
        'directive-project'  => 'Project context',
        'directive-stack'    => 'Stack context',
        'directive-discuss'  => 'Discuss session',
        'directive-evaluate' => 'Evaluate session',
        'directive-kickoff'  => 'Kickoff session',
    ];

    public function __construct(
        public string $projectName,
        public string $projectDir,
        public string $namespace,
        public string $tool,
        public bool $withDocker,
        public string $containerName,
    ) {
    }
}
