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

        // 8 prompts
        $prompts = ['directive-new', 'directive-continue', 'directive-apply', 'directive-verify', 'directive-reflect', 'directive-learn', 'directive-archive', 'directive-project'];
        foreach ($prompts as $prompt) {
            $fs->dumpFile(
                $dir . '/.github/prompts/' . $prompt . '.prompt.md',
                (string) include __DIR__ . '/../Resources/ide/github-copilot/prompts/' . $prompt . '.prompt.md.php'
            );
        }

        // 8 skills
        $skills = ['directive-new-change', 'directive-continue-change', 'directive-apply-change', 'directive-verify-change', 'directive-reflect-change', 'directive-learn-change', 'directive-archive-change', 'directive-project-context'];
        foreach ($skills as $skill) {
            $fs->dumpFile(
                $dir . '/.github/skills/' . $skill . '/SKILL.md',
                (string) include __DIR__ . '/../Resources/ide/github-copilot/skills/' . $skill . '/SKILL.md.php'
            );
        }
    }
}
