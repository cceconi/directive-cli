<?php

declare(strict_types=1);

namespace Directive\Cli\Generator;

use Symfony\Component\Filesystem\Filesystem;

final class IdeCodexGenerator implements IdeGeneratorInterface
{
    public function generate(ProjectContext $context): void
    {
        if ($context->tool !== 'codex') {
            return;
        }

        $fs = new Filesystem();
        $dir = $context->projectDir;
        $projectName = $context->projectName;

        foreach (ProjectContext::COMMANDS as $command) {
            /** @var array{description: string, body: string} $workflow */
            $workflow = include __DIR__ . '/../Resources/workflows/' . $command . '.php';
            $frontmatter = "---\ndescription: " . $workflow['description'] . " for " . $projectName . "\nargument-hint: \"<change-name or description>\"\n---\n\n";
            $fs->dumpFile(
                $dir . '/.codex/prompts/' . $command . '.md',
                $frontmatter . $workflow['body'],
            );
        }
    }

    public function getToolName(): string
    {
        return 'codex';
    }

    public function getOutputDir(): string
    {
        return '.codex/prompts';
    }
}
