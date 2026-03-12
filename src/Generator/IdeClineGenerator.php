<?php

declare(strict_types=1);

namespace Directive\Cli\Generator;

use Symfony\Component\Filesystem\Filesystem;

final class IdeClineGenerator implements GeneratorInterface
{
    public function generate(ProjectContext $context): void
    {
        if ($context->tool !== 'cline') {
            return;
        }

        $fs = new Filesystem();
        $dir = $context->projectDir;
        $projectName = $context->projectName;

        // 12 workflows
        $workflows = ['directive-new', 'directive-continue', 'directive-apply', 'directive-verify', 'directive-reflect', 'directive-learn', 'directive-archive', 'directive-project', 'directive-stack', 'directive-discuss', 'directive-evaluate', 'directive-kickoff'];
        foreach ($workflows as $workflow) {
            $fs->dumpFile(
                $dir . '/.clinerules/workflows/' . $workflow . '.md',
                (string) include __DIR__ . '/../Resources/ide/cline/workflows/' . $workflow . '.md.php'
            );
        }
    }
}
