<?php

declare(strict_types=1);

namespace Directive\Cli\Generator;

use Symfony\Component\Filesystem\Filesystem;

final class IdeGithubCopilotGenerator implements GeneratorInterface
{
    public function generate(ProjectContext $context): void
    {
        if ($context->tool !== 'github-copilot') {
            return;
        }

        $fs = new Filesystem();
        $dir = $context->projectDir;
        $projectName = $context->projectName;

        foreach (ProjectContext::COMMANDS as $command) {
            /** @var array{description: string, body: string} $workflow */
            $workflow = include __DIR__ . '/../Resources/workflows/' . $command . '.php';
            $frontmatter = "---\nmode: agent\ndescription: " . $workflow['description'] . " for " . $projectName . "\n---\n\n";
            $fs->dumpFile(
                $dir . '/.github/prompts/' . $command . '.prompt.md',
                $frontmatter . $workflow['body'],
            );
        }
    }
}
