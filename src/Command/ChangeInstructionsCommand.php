<?php

declare(strict_types=1);

namespace Directive\Cli\Command;

use Directive\Cli\Config\DirectiveConfig;
use Directive\Cli\Config\DirectiveConfigLoader;
use Directive\Cli\Config\Exception\ConfigNotFoundException;
use Directive\Cli\Schema\ArtifactDefinition;
use Directive\Cli\Schema\SpecDrivenSchema;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'change:instructions', description: 'Get enriched instructions for creating an artifact')]
final class ChangeInstructionsCommand extends Command
{
    protected function configure(): void
    {
        $this->addArgument('artifact', InputArgument::REQUIRED, 'Artifact id (e.g. proposal, design, specs, tasks)');
        $this->addOption('change', null, InputOption::VALUE_REQUIRED, 'Change name');
        $this->addOption('json', null, InputOption::VALUE_NONE, 'Output as JSON');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $cwd = getcwd();
        if ($cwd === false) {
            $io->error('Cannot determine current working directory.');
            return Command::FAILURE;
        }

        /** @var string $artifactId */
        $artifactId = $input->getArgument('artifact');
        /** @var string|null $changeName */
        $changeName = $input->getOption('change');
        $asJson = (bool) $input->getOption('json');

        if ($changeName === null || $changeName === '') {
            $io->error('The --change option is required.');
            return Command::FAILURE;
        }

        try {
            $config = (new DirectiveConfigLoader())->load($cwd);
        } catch (ConfigNotFoundException $e) {
            $io->error($e->getMessage());
            return Command::FAILURE;
        }

        $schema = new SpecDrivenSchema();
        $artifacts = $schema->artifacts();

        $artifactDef = null;
        foreach ($artifacts as $a) {
            if ($a->id === $artifactId) {
                $artifactDef = $a;
                break;
            }
        }

        if ($artifactDef === null) {
            $validIds = array_map(fn (ArtifactDefinition $a) => $a->id, $artifacts);
            $io->error(sprintf(
                'Unknown artifact "%s". Valid artifacts: %s',
                $artifactId,
                implode(', ', $validIds)
            ));
            return Command::FAILURE;
        }

        $changeDir = $cwd . '/' . $config->changesPath . '/' . $changeName;

        $contextBlock = $this->buildContext($cwd, $config);
        $template = $this->loadTemplate($artifactDef->templatePath, $config->projectName, $changeName, $contextBlock);
        $dependencies = $this->buildDependencies($artifacts, $changeDir);

        if ($asJson) {
            $output->writeln(json_encode([
                'changeName'   => $changeName,
                'artifactId'   => $artifactId,
                'schemaName'   => 'spec-driven',
                'outputPath'   => $artifactDef->outputPath,
                'instruction'  => sprintf(
                    'Produce the %s artifact for change "%s". Save the result at %s/%s/%s.',
                    $artifactId,
                    $changeName,
                    $config->changesPath,
                    $changeName,
                    $artifactDef->outputPath
                ),
                'template'     => $template,
                'context'      => $contextBlock,
                'rules'        => $artifactDef->deps,
                'dependencies' => $dependencies,
            ], JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));
            return Command::SUCCESS;
        }

        $output->writeln([
            sprintf('## Instructions: %s for change "%s"', $artifactId, $changeName),
            '',
            '### Project Context',
            $contextBlock,
            '',
            '### Template',
            $template,
        ]);

        return Command::SUCCESS;
    }

    private function buildContext(string $cwd, DirectiveConfig $config): string
    {
        $lines = [
            sprintf('Project: %s', $config->projectName),
            sprintf('Namespace: %s', $config->namespace),
            sprintf('Stack: %s', $config->stack),
        ];

        foreach ($config->stackFiles as $stackFile) {
            $filePath = $cwd . '/directive-spec/context/' . $stackFile;
            if (file_exists($filePath)) {
                $lines[] = '';
                $lines[] = sprintf('--- %s ---', $stackFile);
                $lines[] = (string) file_get_contents($filePath);
            }
        }

        return implode("\n", $lines);
    }

    /**
     * @param list<ArtifactDefinition> $artifacts
     * @return list<array{id: string, done: bool, path: string}>
     */
    private function buildDependencies(array $artifacts, string $changeDir): array
    {
        $deps = [];
        foreach ($artifacts as $artifact) {
            $deps[] = [
                'id'   => $artifact->id,
                'done' => file_exists($changeDir . '/' . $artifact->outputPath),
                'path' => $artifact->outputPath,
            ];
        }
        return $deps;
    }

    private function loadTemplate(string $templatePath, string $projectName, string $changeName, string $projectContext): string
    {
        if (!file_exists($templatePath)) {
            return '<!-- template not found: ' . $templatePath . ' -->';
        }

        $result = (static function () use ($templatePath, $projectName, $changeName, $projectContext): mixed {
            // Variables are intentionally captured — templates read them from the closure scope
            /** @phpstan-ignore-next-line */
            [$projectName, $changeName, $projectContext];
            return include $templatePath;
        })();

        return is_string($result) ? $result : '';
    }
}
