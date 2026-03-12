<?php

declare(strict_types=1);

namespace Directive\Cli\Generator;

use Symfony\Component\Filesystem\Filesystem;

final class IdeCursorGenerator implements GeneratorInterface
{
    public function generate(ProjectContext $context): void
    {
        if ($context->tool !== 'cursor') {
            return;
        }

        $fs = new Filesystem();
        $dir = $context->projectDir;
        $projectName = $context->projectName;
        $namespace = $context->namespace;

        // .cursor/rules
        $fs->dumpFile($dir . '/.cursor/rules', (string) include __DIR__ . '/../Resources/ide/cursor/rules.php');

        // 8 prompts
        $prompts = ['directive-new', 'directive-continue', 'directive-apply', 'directive-verify', 'directive-reflect', 'directive-learn', 'directive-project', 'directive-stack'];
        foreach ($prompts as $prompt) {
            $fs->dumpFile(
                $dir . '/.cursor/prompts/' . $prompt . '.md',
                (string) include __DIR__ . '/../Resources/ide/cursor/prompts/' . $prompt . '.md.php'
            );
        }
    }
}
