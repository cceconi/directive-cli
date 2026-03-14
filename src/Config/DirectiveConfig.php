<?php

declare(strict_types=1);

namespace Directive\Cli\Config;

final readonly class DirectiveConfig
{
    /**
     * @param list<string> $stackFiles
     */
    public function __construct(
        public string $projectName,
        public string $namespace,
        public string $stack,
        public string $specsPath,
        public string $changesPath,
        public array $stackFiles = [],
    ) {}
}
