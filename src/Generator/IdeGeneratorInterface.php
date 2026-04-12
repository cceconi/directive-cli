<?php

declare(strict_types=1);

namespace Directive\Cli\Generator;

interface IdeGeneratorInterface extends GeneratorInterface
{
    /** The tool slug (e.g. 'github-copilot') */
    public function getToolName(): string;

    /** Relative path within projectDir containing the generated prompt files */
    public function getOutputDir(): string;
}
