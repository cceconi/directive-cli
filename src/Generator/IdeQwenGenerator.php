<?php

declare(strict_types=1);

namespace Directive\Cli\Generator;

use Symfony\Component\Filesystem\Filesystem;

final class IdeQwenGenerator implements GeneratorInterface
{
    public function generate(ProjectContext $context): void
    {
        if ($context->tool !== 'qwen') {
            return;
        }

        $fs = new Filesystem();
        $dir = $context->projectDir;
        $projectName = $context->projectName;

        foreach (ProjectContext::COMMANDS as $command) {
            /** @var array{description: string, body: string} $workflow */
            $workflow = include __DIR__ . '/../Resources/workflows/' . $command . '.php';
            $frontmatter = "---\ndescription: " . $workflow['description'] . " for " . $projectName . "\n---\n\n";
            $fs->dumpFile(
                $dir . '/.qwen/commands/' . $command . '.md',
                $frontmatter . $workflow['body'],
            );
        }
    }
}
