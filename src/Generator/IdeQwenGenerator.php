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

        // 12 commands
        $commands = ['directive-new', 'directive-continue', 'directive-apply', 'directive-verify', 'directive-reflect', 'directive-learn', 'directive-archive', 'directive-project', 'directive-stack', 'directive-discuss', 'directive-evaluate', 'directive-kickoff'];
        foreach ($commands as $command) {
            $fs->dumpFile(
                $dir . '/.qwen/commands/' . $command . '.md',
                (string) include __DIR__ . '/../Resources/ide/qwen/commands/' . $command . '.md.php'
            );
        }
    }
}
