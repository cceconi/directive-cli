<?php

declare(strict_types=1);

namespace Directive\Cli\Command;

use Directive\Cli\Config\DirectiveConfigLoader;
use Directive\Cli\Config\Exception\ConfigNotFoundException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

#[AsCommand(name: 'change:new', description: 'Create a new change directory')]
final class ChangeNewCommand extends Command
{
    private const string KEBAB_PATTERN = '/^[a-z][a-z0-9-]*$/';

    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'Change name (kebab-case)');
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

        if (!preg_match(self::KEBAB_PATTERN, $name)) {
            $io->error(sprintf(
                'Invalid change name "%s". Name must match /^[a-z][a-z0-9-]*$/ (kebab-case).',
                $name
            ));
            return Command::FAILURE;
        }

        try {
            $config = (new DirectiveConfigLoader())->load($cwd);
        } catch (ConfigNotFoundException $e) {
            $io->error($e->getMessage());
            return Command::FAILURE;
        }

        $changeDir = $cwd . '/' . $config->changesPath . '/' . $name;
        $fs = new Filesystem();

        if ($fs->exists($changeDir)) {
            $io->error(sprintf('Change "%s" already exists at %s.', $name, $changeDir));
            return Command::FAILURE;
        }

        $fs->mkdir($changeDir);
        $io->success(sprintf('Change "%s" created at %s', $name, $changeDir));

        return Command::SUCCESS;
    }
}
