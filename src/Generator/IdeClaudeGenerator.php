<?php

declare(strict_types=1);

namespace Directive\Cli\Generator;

use Symfony\Component\Filesystem\Filesystem;

final class IdeClaudeGenerator implements GeneratorInterface
{
    public function generate(ProjectContext $context): void
    {
        if ($context->tool !== 'claude') {
            return;
        }

        $fs = new Filesystem();
        $dir = $context->projectDir;

        foreach (ProjectContext::COMMANDS as $command) {
            /** @var array{description: string, body: string} $workflow */
            $workflow = include __DIR__ . '/../Resources/workflows/' . $command . '.php';
            $fs->dumpFile(
                $dir . '/.claude/commands/' . $command . '.md',
                $workflow['body'],
            );
        }
    }
}
