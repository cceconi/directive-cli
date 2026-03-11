<?php

declare(strict_types=1);

namespace Directive\Cli\Generator;

use Symfony\Component\Filesystem\Filesystem;

final class IdeZedGenerator implements GeneratorInterface
{
    public function generate(ProjectContext $context): void
    {
        if ($context->tool !== 'zed') {
            return;
        }

        $fs = new Filesystem();
        $dir = $context->projectDir;
        $projectName = $context->projectName;
        $namespace = $context->namespace;

        $fs->dumpFile($dir . '/.zed/settings.json', (string) include __DIR__ . '/../Resources/ide/zed/settings.json.php');
    }
}
