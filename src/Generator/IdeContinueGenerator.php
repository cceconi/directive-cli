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

        // 12 prompts
        $prompts = ['directive-new', 'directive-continue', 'directive-apply', 'directive-verify', 'directive-reflect', 'directive-learn', 'directive-archive', 'directive-project', 'directive-stack', 'directive-discuss', 'directive-evaluate', 'directive-kickoff'];
        foreach ($prompts as $prompt) {
            $fs->dumpFile(
                $dir . '/.continue/prompts/' . $prompt . '.prompt',
                (string) include __DIR__ . '/../Resources/ide/continue/prompts/' . $prompt . '.prompt.php'
            );
        }
    }
}
