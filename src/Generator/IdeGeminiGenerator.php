<?php

declare(strict_types=1);

namespace Directive\Cli\Generator;

use Symfony\Component\Filesystem\Filesystem;

final class IdeGeminiGenerator implements GeneratorInterface
{
    public function generate(ProjectContext $context): void
    {
        if ($context->tool !== 'gemini') {
            return;
        }

        $fs = new Filesystem();
        $dir = $context->projectDir;
        $projectName = $context->projectName;
        $namespace = $context->namespace;

        $fs->dumpFile($dir . '/GEMINI.md', (string) include __DIR__ . '/../Resources/ide/gemini/GEMINI.md.php');
    }
}
