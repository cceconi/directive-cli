<?php

declare(strict_types=1);

namespace Directive\Cli\Command;

use Directive\Cli\Config\DirectiveConfigLoader;
use Directive\Cli\Config\Exception\ConfigNotFoundException;
use Directive\Cli\Generator\DockerGenerator;
use Directive\Cli\Generator\ProjectContext;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

#[AsCommand(name: 'update-docker', description: 'Regenerate Docker configuration files in an existing Directive project')]
final class UpdateDockerCommand extends Command
{
    /**
     * Docker files produced by DockerGenerator, relative to projectDir.
     *
     * @var list<string>
     */
    private const array DOCKER_FILES = [
        'docker/Dockerfile',
        'docker-compose.yml',
        '.env.example',
        'docker/start.sh',
        'docker/stop.sh',
    ];

    public function __construct(
        private readonly UpdateHelper $helper = new UpdateHelper(),
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('force', null, null, 'Overwrite all files without confirmation');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io  = new SymfonyStyle($input, $output);
        $cwd = getcwd();

        if ($cwd === false) {
            $io->error('Cannot determine current working directory.');
            return Command::FAILURE;
        }

        // 1. Validate project
        try {
            $config = (new DirectiveConfigLoader())->load($cwd);
        } catch (ConfigNotFoundException $e) {
            $io->error($e->getMessage());
            return Command::FAILURE;
        }

        $force = (bool) $input->getOption('force');

        // 2. Read containerName from common.yaml if present; otherwise ask
        $configPath = $cwd . '/directive-spec/context/common.yaml';

        /** @var mixed $parsed */
        $parsed = Yaml::parseFile($configPath);
        /** @var array<string, mixed> $yaml */
        $yaml = is_array($parsed) ? $parsed : [];

        /** @var string|null $containerName */
        $containerName = isset($yaml['context']['docker_container']) && is_string($yaml['context']['docker_container'])
            ? $yaml['context']['docker_container']
            : null;

        if ($containerName === null) {
            $default = $config->projectName . '-runtime';
            $io->warning(sprintf(
                'No "context.docker_container" found in common.yaml. Using default: %s',
                $default,
            ));

            /** @var \Symfony\Component\Console\Helper\QuestionHelper $qHelper */
            $qHelper = $this->getHelper('question');
            $question = new Question(
                sprintf('Docker container name [<comment>%s</comment>]: ', $default),
                $default,
            );
            /** @var string $containerName */
            $containerName = $qHelper->ask($input, $output, $question);
        }

        // 3. Generate into a temporary directory
        $tmpDir = sys_get_temp_dir() . '/directive-update-docker-' . uniqid();
        $fs     = new Filesystem();
        $fs->mkdir($tmpDir);

        $tmpContext = new ProjectContext(
            projectName:  $config->projectName,
            projectDir:   $tmpDir,
            namespace:    $config->namespace,
            tool:         'none',
            withDocker:   true,
            containerName: $containerName,
        );

        (new DockerGenerator())->generate($tmpContext);

        // 4. Diff-aware copy
        $updated  = 0;
        $upToDate = 0;
        $skipped  = 0;

        foreach (self::DOCKER_FILES as $relPath) {
            $tmpSrc  = $tmpDir . '/' . $relPath;
            $realDest = $cwd . '/' . $relPath;

            if (!file_exists($tmpSrc)) {
                continue;
            }

            $generatedContent = file_get_contents($tmpSrc);
            if ($generatedContent === false) {
                continue;
            }

            $isNew           = !file_exists($realDest);
            $existingContent = $isNew ? null : (file_get_contents($realDest) ?: '');

            $written = $this->helper->safeDumpFile($realDest, $generatedContent, $force, $io);

            if ($written) {
                if ($isNew) {
                    $io->writeln(sprintf('  <info>+</info> %s <fg=gray>(new)</>', $relPath));
                }
                $updated++;
            } elseif ($existingContent === $generatedContent) {
                $upToDate++;
            } else {
                $skipped++;
            }
        }

        $fs->remove($tmpDir);

        // 5. Summary
        $io->newLine();
        $io->success(sprintf(
            '%d file(s) updated, %d up to date, %d skipped.',
            $updated,
            $upToDate,
            $skipped,
        ));

        return Command::SUCCESS;
    }
}
