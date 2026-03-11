<?php

declare(strict_types=1);

namespace Directive\Cli\Generator;

final readonly class ProjectContext
{
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
