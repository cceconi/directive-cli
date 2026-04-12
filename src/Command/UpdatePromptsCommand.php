<?php

declare(strict_types=1);

namespace Directive\Cli\Command;

use Directive\Cli\Config\DirectiveConfigLoader;
use Directive\Cli\Config\Exception\ConfigNotFoundException;
use Directive\Cli\Generator\IdeGeneratorInterface;
use Directive\Cli\Generator\ProjectContext;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

#[AsCommand(name: 'update-prompts', description: 'Regenerate IDE prompt files in an existing Directive project')]
final class UpdatePromptsCommand extends Command
{
    /** @param IdeGeneratorInterface[] $generators */
    public function __construct(
        private readonly array $generators,
        private readonly UpdateHelper $helper = new UpdateHelper(),
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('force', null, null, 'Overwrite all files without confirmation');
        $this->addOption('only', null, InputOption::VALUE_REQUIRED, 'Regenerate only the prompt for the given command (e.g. directive-propose)');
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

        /** @var string|null $only */
        $only = $input->getOption('only');

        // 2. Validate --only value
        if ($only !== null && !in_array($only, ProjectContext::COMMANDS, true)) {
            $io->error(sprintf(
                'Unknown command "%s". Available commands: %s',
                $only,
                implode(', ', ProjectContext::COMMANDS),
            ));
            return Command::FAILURE;
        }

        // 3. Detect / resolve generator
        $generator = $this->resolveGenerator($cwd, $input, $output, $io);

        if ($generator === null) {
            $io->note('No tool selected. Nothing to do.');
            return Command::SUCCESS;
        }

        // 4. Generate into a temporary directory
        $tmpDir = sys_get_temp_dir() . '/directive-update-prompts-' . uniqid();
        $fs     = new Filesystem();
        $fs->mkdir($tmpDir);

        $tmpContext = new ProjectContext(
            projectName:  $config->projectName,
            projectDir:   $tmpDir,
            namespace:    $config->namespace,
            tool:         $generator->getToolName(),
            withDocker:   false,
            containerName: '',
        );

        $generator->generate($tmpContext);

        // 5. Diff-aware copy from tmpDir to real project
        $outputDir     = $generator->getOutputDir();
        $tmpOutputDir  = $tmpDir . '/' . $outputDir;
        $realOutputDir = $cwd . '/' . $outputDir;

        $updated  = 0;
        $upToDate = 0;
        $skipped  = 0;

        if (is_dir($tmpOutputDir)) {
            /** @var list<string> $files */
            $files = array_values(array_diff(scandir($tmpOutputDir) ?: [], ['.', '..']));

            foreach ($files as $filename) {
                // Apply --only filter
                if ($only !== null && !$this->matchesOnly($filename, $only)) {
                    continue;
                }

                $generatedContent = file_get_contents($tmpOutputDir . '/' . $filename);
                if ($generatedContent === false) {
                    continue;
                }

                $realDest        = $realOutputDir . '/' . $filename;
                $isNew           = !file_exists($realDest);
                $existingContent = $isNew ? null : (file_get_contents($realDest) ?: '');

                $written = $this->helper->safeDumpFile($realDest, $generatedContent, $force, $io);

                if ($written) {
                    if ($isNew) {
                        $io->writeln(sprintf('  <info>+</info> %s <fg=gray>(new)</>', $filename));
                    }
                    $updated++;
                } elseif ($existingContent === $generatedContent) {
                    $upToDate++;
                } else {
                    $skipped++;
                }
            }
        }

        $fs->remove($tmpDir);

        // 6. Summary
        $io->newLine();
        $io->success(sprintf(
            '%d file(s) updated, %d up to date, %d skipped.',
            $updated,
            $upToDate,
            $skipped,
        ));

        return Command::SUCCESS;
    }

    /**
     * Detects which IDE generator to use by scanning filesystem signatures.
     * Returns null if the user chose 'none'.
     */
    private function resolveGenerator(string $cwd, InputInterface $input, OutputInterface $output, SymfonyStyle $io): ?IdeGeneratorInterface
    {
        $detected = [];

        foreach ($this->generators as $generator) {
            if (is_dir($cwd . '/' . $generator->getOutputDir())) {
                $detected[] = $generator;
            }
        }

        if (count($detected) === 1) {
            $io->writeln(sprintf('<fg=gray>Detected tool: %s</>', $detected[0]->getToolName()));
            return $detected[0];
        }

        /** @var \Symfony\Component\Console\Helper\QuestionHelper $qHelper */
        $qHelper = $this->getHelper('question');

        if (count($detected) > 1) {
            $toolNames = array_map(fn (IdeGeneratorInterface $g): string => $g->getToolName(), $detected);
            $question  = new ChoiceQuestion(
                sprintf('Multiple tools detected (%s). Which one to update?', implode(', ', $toolNames)),
                $toolNames,
                0,
            );
            /** @var string $choice */
            $choice = $qHelper->ask($input, $output, $question);
            foreach ($detected as $g) {
                if ($g->getToolName() === $choice) {
                    return $g;
                }
            }
        }

        // No tool detected — ask interactively
        $allToolNames   = array_map(fn (IdeGeneratorInterface $g): string => $g->getToolName(), $this->generators);
        $allToolNames[] = 'none';
        $question = new ChoiceQuestion(
            'No tool detected. Which AI tool do you use? [<comment>none</comment>]',
            $allToolNames,
            'none',
        );
        /** @var string $choice */
        $choice = $qHelper->ask($input, $output, $question);

        if ($choice === 'none') {
            return null;
        }

        foreach ($this->generators as $g) {
            if ($g->getToolName() === $choice) {
                return $g;
            }
        }

        return null;
    }

    /**
     * Returns true if the given filename matches the --only command slug.
     * Strips known suffixes (.prompt.md, .prompt, .md, .toml) before comparing.
     */
    private function matchesOnly(string $filename, string $command): bool
    {
        $slug = $filename;
        foreach (['.prompt.md', '.prompt', '.toml', '.md'] as $suffix) {
            if (str_ends_with($slug, $suffix)) {
                $slug = substr($slug, 0, -strlen($suffix));
                break;
            }
        }
        return $slug === $command;
    }
}
