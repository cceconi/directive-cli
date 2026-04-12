<?php

declare(strict_types=1);

namespace Directive\Cli\Generator;

use Symfony\Component\Filesystem\Filesystem;

final class IdeRoocodeGenerator implements IdeGeneratorInterface
{
    public function generate(ProjectContext $context): void
    {
        if ($context->tool !== 'roocode') {
            return;
        }

        $fs = new Filesystem();
        $dir = $context->projectDir;
        $projectName = $context->projectName;

        foreach (ProjectContext::COMMANDS as $command) {
            /** @var array{description: string, body: string} $workflow */
            $workflow = include __DIR__ . '/../Resources/workflows/' . $command . '.php';
            $label = ProjectContext::LABELS[$command];
            $header = "# " . $projectName . " — Directive: " . $label . "\n\n";
            $fs->dumpFile(
                $dir . '/.roo/commands/' . $command . '.md',
                $header . $workflow['body'],
            );
        }
    }

    public function getToolName(): string
    {
        return 'roocode';
    }

    public function getOutputDir(): string
    {
        return '.roo/commands';
    }
}
