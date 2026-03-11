<?php

declare(strict_types=1);

namespace Directive\Cli\Generator;

use Symfony\Component\Filesystem\Filesystem;

final class IdeRoocodeGenerator implements GeneratorInterface
{
    public function generate(ProjectContext $context): void
    {
        if ($context->tool !== 'roocode') {
            return;
        }

        $fs = new Filesystem();
        $dir = $context->projectDir;
        $projectName = $context->projectName;
        $namespace = $context->namespace;

        $fs->dumpFile($dir . '/.roo/rules/project.md', (string) include __DIR__ . '/../Resources/ide/roocode/rules-project.md.php');
    }
}
