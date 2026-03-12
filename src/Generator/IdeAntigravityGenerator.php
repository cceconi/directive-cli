<?php

declare(strict_types=1);

namespace Directive\Cli\Generator;

use Symfony\Component\Filesystem\Filesystem;

final class IdeAntigravityGenerator implements GeneratorInterface
{
    public function generate(ProjectContext $context): void
    {
        if ($context->tool !== 'antigravity') {
            return;
        }

        $fs = new Filesystem();
        $dir = $context->projectDir;
        $projectName = $context->projectName;

        $workflowFiles = ['new-change', 'continue-change', 'apply-change', 'verify-change', 'reflect-change', 'learn-change', 'archive-change', 'project-context'];

        foreach ($workflowFiles as $workflow) {
            $fs->dumpFile(
                $dir . '/.agent/workflows/' . $workflow . '.md',
                (string) include __DIR__ . '/../Resources/ide/antigravity/workflows/' . $workflow . '.md.php'
            );
        }

        $skillDirs = ['directive-new-change', 'directive-continue-change', 'directive-apply-change', 'directive-verify-change', 'directive-reflect-change', 'directive-learn-change', 'directive-archive-change', 'directive-project-context'];

        foreach ($skillDirs as $skill) {
            $fs->dumpFile(
                $dir . '/.agent/skills/' . $skill . '/SKILL.md',
                (string) include __DIR__ . '/../Resources/ide/antigravity/skills/' . $skill . '/SKILL.md.php'
            );
        }
    }
}
