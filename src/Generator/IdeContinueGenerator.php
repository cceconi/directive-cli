<?php

declare(strict_types=1);

namespace Directive\Cli\Generator;

use Symfony\Component\Filesystem\Filesystem;

final class IdeContinueGenerator implements GeneratorInterface
{
    public function generate(ProjectContext $context): void
    {
        if ($context->tool !== 'continue') {
            return;
        }

        $fs = new Filesystem();
        $dir = $context->projectDir;
        $projectName = $context->projectName;

        foreach (ProjectContext::COMMANDS as $command) {
            /** @var array{description: string, body: string} $workflow */
            $workflow = include __DIR__ . '/../Resources/workflows/' . $command . '.php';
            $label = ProjectContext::LABELS[$command];
            $frontmatter = "---\nname: \"Directive: " . $label . "\"\ndescription: " . $workflow['description'] . " for " . $projectName . "\ninvokable: true\n---\n\n";
            $fs->dumpFile(
                $dir . '/.continue/prompts/' . $command . '.prompt',
                $frontmatter . $workflow['body'],
            );
        }
    }
}
