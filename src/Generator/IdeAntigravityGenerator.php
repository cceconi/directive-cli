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

        $workflowFiles = ['directive-new', 'directive-continue', 'directive-apply', 'directive-verify', 'directive-reflect', 'directive-learn', 'directive-archive', 'directive-project', 'directive-stack', 'directive-discuss', 'directive-evaluate', 'directive-kickoff'];

        foreach ($workflowFiles as $workflow) {
            $fs->dumpFile(
                $dir . '/.agent/workflows/' . $workflow . '.md',
                (string) include __DIR__ . '/../Resources/ide/antigravity/workflows/' . $workflow . '.md.php'
            );
        }
    }
}
