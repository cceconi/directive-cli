<?php

declare(strict_types=1);

namespace Directive\Cli\Generator;

use Symfony\Component\Filesystem\Filesystem;

final class IdeWindsurfGenerator implements GeneratorInterface
{
    public function generate(ProjectContext $context): void
    {
        if ($context->tool !== 'windsurf') {
            return;
        }

        $fs = new Filesystem();
        $dir = $context->projectDir;
        $projectName = $context->projectName;

        foreach (ProjectContext::COMMANDS as $command) {
            /** @var array{description: string, body: string} $workflow */
            $workflow = include __DIR__ . '/../Resources/workflows/' . $command . '.php';
            $label = ProjectContext::LABELS[$command];
            $frontmatter = "---\nname: \"Directive: " . $label . "\"\ndescription: " . $workflow['description'] . " for " . $projectName . "\ncategory: Workflow\ntags: [directive, workflow]\n---\n\n";
            $fs->dumpFile(
                $dir . '/.windsurf/workflows/' . $command . '.md',
                $frontmatter . $workflow['body'],
            );
        }
    }
}
