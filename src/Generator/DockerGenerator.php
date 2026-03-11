<?php

declare(strict_types=1);

namespace Directive\Cli\Generator;

use Symfony\Component\Filesystem\Filesystem;

final class DockerGenerator implements GeneratorInterface
{
    public function generate(ProjectContext $context): void
    {
        if (!$context->withDocker) {
            return;
        }

        $fs = new Filesystem();
        $dir = $context->projectDir;
        $projectName = $context->projectName;
        $containerName = $context->containerName;

        $fs->mkdir($dir . '/docker');

        $fs->dumpFile($dir . '/docker/Dockerfile', (string) include __DIR__ . '/../Resources/docker/Dockerfile.php');
        $fs->dumpFile($dir . '/docker-compose.yml', (string) include __DIR__ . '/../Resources/docker/docker-compose.yml.php');
        $fs->dumpFile($dir . '/.env.example', (string) include __DIR__ . '/../Resources/docker/env.example.php');

        $fs->dumpFile($dir . '/docker/start.sh', (string) include __DIR__ . '/../Resources/docker/start.sh.php');
        $fs->dumpFile($dir . '/docker/stop.sh', (string) include __DIR__ . '/../Resources/docker/stop.sh.php');
        $fs->chmod($dir . '/docker/start.sh', 0o755);
        $fs->chmod($dir . '/docker/stop.sh', 0o755);
    }
}
