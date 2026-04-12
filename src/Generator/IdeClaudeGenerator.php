<?php

declare(strict_types=1);

namespace Directive\Cli\Generator;

use Symfony\Component\Filesystem\Filesystem;

final class IdeClaudeGenerator implements IdeGeneratorInterface
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

    public function getToolName(): string
    {
        return 'claude';
    }

    public function getOutputDir(): string
    {
        return '.claude/commands';
    }
}
