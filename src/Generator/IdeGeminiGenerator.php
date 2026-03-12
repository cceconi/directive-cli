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

        foreach (ProjectContext::COMMANDS as $command) {
            /** @var array{description: string, body: string} $workflow */
            $workflow = include __DIR__ . '/../Resources/workflows/' . $command . '.php';
            $content = "description = \"" . $workflow['description'] . " for " . $projectName . "\"\n"
                . "prompt = \"\"\"\n" . $workflow['body'] . "\n\"\"\"\n";
            $fs->dumpFile(
                $dir . '/.gemini/commands/dtsx/' . $command . '.toml',
                $content,
            );
        }
    }
}
