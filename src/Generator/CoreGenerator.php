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
        $localMode = $context->localMode;
        $directivePath = $context->directivePath;

        // composer.json
        $fs->dumpFile($dir . '/composer.json', (string) include __DIR__ . '/../Resources/core/composer.json.php');

        // phpstan.neon
        $fs->dumpFile($dir . '/phpstan.neon', (string) include __DIR__ . '/../Resources/core/phpstan.neon.php');

        // .php-cs-fixer.php
        $fs->dumpFile($dir . '/.php-cs-fixer.php', (string) include __DIR__ . '/../Resources/core/php-cs-fixer.php.php');

        // .gitignore
        $fs->dumpFile($dir . '/.gitignore', (string) include __DIR__ . '/../Resources/core/gitignore.php');

        // .env
        $fs->dumpFile($dir . '/.env', (string) include __DIR__ . '/../Resources/core/env.php');

        // bin/app
        $fs->mkdir($dir . '/bin');
        $fs->dumpFile($dir . '/bin/app', (string) include __DIR__ . '/../Resources/core/bin-app.php');
        $fs->chmod($dir . '/bin/app', 0o755);

        // public/index.php
        $fs->mkdir($dir . '/public');
        $fs->dumpFile($dir . '/public/index.php', (string) include __DIR__ . '/../Resources/core/public-index.php.php');

        // tests/Pest.php
        $fs->mkdir($dir . '/tests');
        $fs->dumpFile($dir . '/tests/Pest.php', (string) include __DIR__ . '/../Resources/core/tests-pest.php.php');

        // directive-spec/context/common.yaml
        $fs->mkdir($dir . '/directive-spec/context');
        $fs->dumpFile($dir . '/directive-spec/context/common.yaml', (string) include __DIR__ . '/../Resources/core/directive-spec-context-common.yaml.php');

        // directive-spec/specs/, directive-spec/changes/, directive-spec/changes/archive/, directive-spec/brainstorm/
        $fs->mkdir($dir . '/directive-spec/specs');
        $fs->touch($dir . '/directive-spec/specs/.gitkeep');
        $fs->mkdir($dir . '/directive-spec/changes');
        $fs->touch($dir . '/directive-spec/changes/.gitkeep');
        $fs->mkdir($dir . '/directive-spec/changes/archive');
        $fs->touch($dir . '/directive-spec/changes/archive/.gitkeep');
        $fs->mkdir($dir . '/directive-spec/brainstorm');
        $fs->touch($dir . '/directive-spec/brainstorm/.gitkeep');

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

        // src/Infrastructure/WebApplication.php
        $fs->dumpFile(
            $dir . '/src/Infrastructure/WebApplication.php',
            (string) include __DIR__ . '/../Resources/core/src-infrastructure-web-application.php.php',
        );

        // src/Infrastructure/ConsoleApplication.php
        $fs->dumpFile(
            $dir . '/src/Infrastructure/ConsoleApplication.php',
            (string) include __DIR__ . '/../Resources/core/src-infrastructure-console-application.php.php',
        );

        // src/Infrastructure/Config/AppConfig.php
        $fs->mkdir($dir . '/src/Infrastructure/Config');
        $fs->dumpFile(
            $dir . '/src/Infrastructure/Config/AppConfig.php',
            (string) include __DIR__ . '/../Resources/core/src-infrastructure-config-app-config.php.php',
        );

        // var/log/ and var/cache/ — required by the framework at runtime.
        // 0777 so the web/CLI process (www-data, nobody, …) can write without
        // needing to match the owner of the scaffolded project.
        $fs->mkdir($dir . '/var', 0777);
        $fs->mkdir($dir . '/var/log', 0777);
        $fs->touch($dir . '/var/log/.gitkeep');
        $fs->mkdir($dir . '/var/cache', 0777);
        $fs->touch($dir . '/var/cache/.gitkeep');
    }
}
