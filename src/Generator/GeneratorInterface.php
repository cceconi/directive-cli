<?php

declare(strict_types=1);

namespace Directive\Cli\Generator;

interface GeneratorInterface
{
    public function generate(ProjectContext $context): void;
}
