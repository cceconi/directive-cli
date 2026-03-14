<?php

declare(strict_types=1);

namespace Directive\Cli\Schema;

final readonly class ArtifactDefinition
{
    /**
     * @param list<string> $deps
     */
    public function __construct(
        public string $id,
        public string $outputPath,
        public array $deps,
        public string $templatePath,
    ) {}
}
