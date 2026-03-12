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
        $namespace = $context->namespace;

        // .github/copilot-instructions.md
        $fs->dumpFile(
            $dir . '/.github/copilot-instructions.md',
            (string) include __DIR__ . '/../Resources/ide/github-copilot/copilot-instructions.md.php'
        );

        // 10 prompts
        $prompts = ['directive-new', 'directive-continue', 'directive-apply', 'directive-verify', 'directive-reflect', 'directive-learn', 'directive-archive', 'directive-project', 'directive-stack', 'directive-discuss', 'directive-evaluate', 'directive-kickoff'];
        foreach ($prompts as $prompt) {
            $fs->dumpFile(
                $dir . '/.github/prompts/' . $prompt . '.prompt.md',
                (string) include __DIR__ . '/../Resources/ide/github-copilot/prompts/' . $prompt . '.prompt.md.php'
            );
        }

    }
}
