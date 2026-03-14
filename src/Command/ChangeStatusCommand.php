<?php

declare(strict_types=1);

namespace Directive\Cli\Command;

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

#[AsCommand(name: 'change:status', description: 'Display the artifact status of a change')]
final class ChangeStatusCommand extends Command
{
    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'Change name');
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

        /** @var string $name */
        $name = $input->getArgument('name');
        $asJson = (bool) $input->getOption('json');

        try {
            $config = (new DirectiveConfigLoader())->load($cwd);
        } catch (ConfigNotFoundException $e) {
            $io->error($e->getMessage());
            return Command::FAILURE;
        }

        $changeDir = $cwd . '/' . $config->changesPath . '/' . $name;

        if (!is_dir($changeDir)) {
            $io->error(sprintf(
                'Change "%s" does not exist in %s.',
                $name,
                $cwd . '/' . $config->changesPath
            ));
            return Command::FAILURE;
        }

        $schema = new SpecDrivenSchema();
        $artifacts = $schema->artifacts();

        // Track which artifact ids are done
        $doneIds = [];
        foreach ($artifacts as $artifact) {
            if ($this->isSatisfied($changeDir, $artifact->outputPath)) {
                $doneIds[] = $artifact->id;
            }
        }

        $result = [];
        foreach ($artifacts as $artifact) {
            $isDone = in_array($artifact->id, $doneIds, true);
            $missingDeps = array_filter($artifact->deps, fn (string $dep) => !in_array($dep, $doneIds, true));

            if ($isDone) {
                $status = 'done';
                $entry = ['id' => $artifact->id, 'outputPath' => $artifact->outputPath, 'status' => $status];
            } elseif (count($missingDeps) === 0) {
                $status = 'ready';
                $entry = ['id' => $artifact->id, 'outputPath' => $artifact->outputPath, 'status' => $status];
            } else {
                $status = 'blocked';
                $entry = [
                    'id'          => $artifact->id,
                    'outputPath'  => $artifact->outputPath,
                    'status'      => $status,
                    'missingDeps' => array_values($missingDeps),
                ];
            }
            $result[] = $entry;
        }

        $isComplete = count(array_filter($result, fn (array $e) => $e['status'] !== 'done')) === 0;

        if ($asJson) {
            $output->writeln(json_encode([
                'changeName' => $name,
                'schemaName' => 'spec-driven',
                'isComplete' => $isComplete,
                'artifacts'  => $result,
            ], JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));
            return Command::SUCCESS;
        }

        $io->title(sprintf('Status: %s (spec-driven)', $name));
        foreach ($result as $entry) {
            $symbol = match ($entry['status']) {
                'done'    => '<info>✓</info>',
                'ready'   => '<comment>→</comment>',
                default   => '<fg=red>✗</>',
            };
            $line = sprintf('  %s %s (%s)', $symbol, $entry['id'], $entry['status']);
            if ($entry['status'] === 'blocked') {
                /** @var list<string> $missing */
                $missing = $entry['missingDeps'];
                $line .= sprintf(' — waiting for: %s', implode(', ', $missing));
            }
            $io->writeln($line);
        }

        if ($isComplete) {
            $io->success('All artifacts are done. Change is complete.');
        }

        return Command::SUCCESS;
    }

    private function isSatisfied(string $changeDir, string $outputPath): bool
    {
        // Handle glob pattern specs/**/*.md
        if (str_contains($outputPath, '**')) {
            $specsDir = $changeDir . '/specs';
            if (!is_dir($specsDir)) {
                return false;
            }
            $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($specsDir));
            foreach ($iterator as $file) {
                if ($file instanceof \SplFileInfo && $file->isFile() && $file->getExtension() === 'md') {
                    return true;
                }
            }
            return false;
        }

        return file_exists($changeDir . '/' . $outputPath);
    }
}
