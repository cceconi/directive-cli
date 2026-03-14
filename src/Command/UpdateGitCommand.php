<?php

declare(strict_types=1);

namespace Directive\Cli\Command;

use Directive\Cli\Config\DirectiveConfigLoader;
use Directive\Cli\Config\Exception\ConfigNotFoundException;
use Directive\Cli\Config\GitConfig;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Yaml\Yaml;

#[AsCommand(name: 'update-git', description: 'Configure or reconfigure the git integration for this Directive project')]
final class UpdateGitCommand extends Command
{
    public function __construct(
        private readonly GitConfigurationHelper $gitHelper = new GitConfigurationHelper(),
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io  = new SymfonyStyle($input, $output);
        $cwd = getcwd();

        if ($cwd === false) {
            $io->error('Cannot determine current working directory.');
            return Command::FAILURE;
        }

        // Validate that this is a directive project
        try {
            (new DirectiveConfigLoader())->load($cwd);
        } catch (ConfigNotFoundException $e) {
            $io->error($e->getMessage());
            return Command::FAILURE;
        }

        if (!$this->gitHelper->isGitAvailable()) {
            $io->error('Git is not available. Please install git and try again.');
            return Command::FAILURE;
        }

        $gitConfig = $this->gitHelper->askGitQuestions($io);

        if ($gitConfig === null) {
            $io->note('Git initialisation skipped. No changes written.');
            return Command::SUCCESS;
        }

        $configPath = $cwd . '/directive-spec/context/common.yaml';

        /** @var mixed $parsed */
        $parsed = Yaml::parseFile($configPath);
        /** @var array<string, mixed> $yaml */
        $yaml = is_array($parsed) ? $parsed : [];

        $yaml['git'] = $this->buildGitArray($gitConfig);

        file_put_contents($configPath, Yaml::dump($yaml, 4, 2));

        $io->success('Git configuration updated in directive-spec/context/common.yaml');

        return Command::SUCCESS;
    }

    /** @return array<string, mixed> */
    private function buildGitArray(GitConfig $config): array
    {
        if (!$config->agentManaged) {
            return [
                'agent_managed'  => false,
                'default_branch' => $config->defaultBranch,
            ];
        }

        return [
            'agent_managed'   => true,
            'default_branch'  => $config->defaultBranch,
            'base_branch'     => $config->baseBranch,
            'strategy'        => $config->strategy,
            'branch_prefix'   => $config->branchPrefix,
            'commit_mode'     => $config->commitMode,
            'commit_pattern'  => $config->commitPattern,
            'commit_template' => $config->commitTemplate !== '' ? $config->commitTemplate : null,
            'remote'          => $config->remote,
        ];
    }
}
