<?php

declare(strict_types=1);

namespace Directive\Cli\Generator;

use Symfony\Component\Filesystem\Filesystem;

final class CoreGenerator implements GeneratorInterface
{
    public function generate(ProjectContext $context): void
    {
        $fs = new Filesystem();
        $dir = $context->projectDir;
        $projectName = $context->projectName;
        $namespace = $context->namespace;

        // composer.json
        $fs->dumpFile($dir . '/composer.json', (string) include __DIR__ . '/../Resources/core/composer.json.php');

        // phpstan.neon
        $fs->dumpFile($dir . '/phpstan.neon', (string) include __DIR__ . '/../Resources/core/phpstan.neon.php');

        // .php-cs-fixer.php
        $fs->dumpFile($dir . '/.php-cs-fixer.php', (string) include __DIR__ . '/../Resources/core/php-cs-fixer.php.php');

        // .gitignore
        $fs->dumpFile($dir . '/.gitignore', (string) include __DIR__ . '/../Resources/core/gitignore.php');

        // bin/app
        $fs->mkdir($dir . '/bin');
        $fs->dumpFile($dir . '/bin/app', (string) include __DIR__ . '/../Resources/core/bin-app.php');
        $fs->chmod($dir . '/bin/app', 0o755);

        // tests/Pest.php
        $fs->mkdir($dir . '/tests');
        $fs->dumpFile($dir . '/tests/Pest.php', (string) include __DIR__ . '/../Resources/core/tests-pest.php.php');

        // directive-spec/context/common.yaml
        $fs->mkdir($dir . '/directive-spec/context');
        $fs->dumpFile($dir . '/directive-spec/context/common.yaml', (string) include __DIR__ . '/../Resources/core/directive-spec-context-common.yaml.php');

        // directive-spec/specs/, directive-spec/changes/, directive-spec/changes/archive/
        $fs->mkdir($dir . '/directive-spec/specs');
        $fs->touch($dir . '/directive-spec/specs/.gitkeep');
        $fs->mkdir($dir . '/directive-spec/changes');
        $fs->touch($dir . '/directive-spec/changes/.gitkeep');
        $fs->mkdir($dir . '/directive-spec/changes/archive');
        $fs->touch($dir . '/directive-spec/changes/archive/.gitkeep');

        // src/ hexagonal structure
        $hexDirs = [
            $dir . '/src/Application',
            $dir . '/src/Domain',
            $dir . '/src/Infrastructure/Http',
            $dir . '/src/Infrastructure/Console',
            $dir . '/src/Infrastructure/Persistence',
            $dir . '/src/Infrastructure/Security',
        ];
        foreach ($hexDirs as $hexDir) {
            $fs->mkdir($hexDir);
            $fs->touch($hexDir . '/.gitkeep');
        }
    }
}
