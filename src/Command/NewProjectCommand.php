<?php

declare(strict_types=1);

namespace Directive\Cli\Command;

use Directive\Cli\Config\GitConfig;
use Directive\Cli\Generator\GeneratorInterface;
use Directive\Cli\Generator\ProjectContext;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

#[AsCommand(name: 'new', description: 'Create a new Directive project')]
final class NewProjectCommand extends Command
{
    private const array TOOLS = [
        'github-copilot',
        'cursor',
        'claude',
        'windsurf',
        'cline',
        'roocode',
        'continue',
        'codex',
        'kiro',
        'gemini',
        'antigravity',
        'none',
    ];

    /** @param GeneratorInterface[] $generators */
    public function __construct(
        private readonly array $generators = [],
        private readonly GitConfigurationHelper $gitHelper = new GitConfigurationHelper(),
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('project-name', InputArgument::REQUIRED, 'Name of the project to create (kebab-case)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $filesystem = new Filesystem();

        /** @var string $projectName */
        $projectName = $input->getArgument('project-name');
        $projectDir = getcwd() . '/' . $projectName;

        if ($filesystem->exists($projectDir)) {
            $io->error(sprintf('Directory "%s" already exists.', $projectDir));
            return Command::FAILURE;
        }

        /** @var \Symfony\Component\Console\Helper\QuestionHelper $helper */
        $helper = $this->getHelper('question');

        // Question 1 — namespace
        $defaultNamespace = $this->toNamespace($projectName);
        $namespaceQuestion = new Question(sprintf('PHP root namespace [<comment>%s</comment>]: ', $defaultNamespace), $defaultNamespace);
        /** @var string $namespace */
        $namespace = $helper->ask($input, $output, $namespaceQuestion);

        // Question 2 — AI tool
        $toolQuestion = new ChoiceQuestion('Which AI tool do you use? [<comment>none</comment>]', self::TOOLS, 'none');
        $toolQuestion->setErrorMessage('Tool "%s" is not valid.');
        /** @var string $tool */
        $tool = $helper->ask($input, $output, $toolQuestion);

        // Question 3 — Docker
        $dockerQuestion = new ConfirmationQuestion('Include Docker configuration? [<comment>yes</comment>]: ', true);
        /** @var bool $withDocker */
        $withDocker = $helper->ask($input, $output, $dockerQuestion);

        // Question 4 — container name (conditional)
        $containerName = '';
        if ($withDocker) {
            $defaultContainer = $projectName . '-runtime';
            $containerQuestion = new Question(sprintf('Docker container name [<comment>%s</comment>]: ', $defaultContainer), $defaultContainer);
            /** @var string $containerName */
            $containerName = $helper->ask($input, $output, $containerQuestion);
        }

        $context = new ProjectContext(
            projectName: $projectName,
            projectDir: $projectDir,
            namespace: $namespace,
            tool: $tool,
            withDocker: $withDocker,
            containerName: $containerName,
        );

        $filesystem->mkdir($projectDir);

        foreach ($this->generators as $generator) {
            $generator->generate($context);
        }

        // ── Git initialisation ────────────────────────────────────────────
        if (!$this->gitHelper->isGitAvailable()) {
            $io->warning(
                'Git is not available on this system. '
                . 'Install git and run `directive update-git` to configure the integration.'
            );
            $this->writeGitConfig($projectDir, new GitConfig(
                agentManaged:  false,
                defaultBranch: 'main',
                baseBranch:    'main',
            ));
        } else {
            $gitConfig = $this->gitHelper->askGitQuestions($io);
            if ($gitConfig !== null) {
                $this->gitHelper->initRepository($projectDir, $gitConfig->defaultBranch);
                $this->gitHelper->configureLocalUser($projectDir);
                $this->gitHelper->createInitialCommit($projectDir);
                if ($gitConfig->strategy === 'gitflow') {
                    $this->gitHelper->createDevelopBranch($projectDir, $gitConfig->defaultBranch);
                }
                $this->writeGitConfig($projectDir, $gitConfig);
            }
        }

        $io->success(sprintf('Project "%s" created successfully in %s', $projectName, $projectDir));

        return Command::SUCCESS;
    }

    private function writeGitConfig(string $projectDir, GitConfig $config): void
    {
        $configPath = $projectDir . '/directive-spec/context/common.yaml';

        if (!file_exists($configPath)) {
            return;
        }

        /** @var mixed $parsed */
        $parsed = Yaml::parseFile($configPath);
        /** @var array<string, mixed> $yaml */
        $yaml = is_array($parsed) ? $parsed : [];

        $yaml['git'] = $this->buildGitArray($config);

        file_put_contents($configPath, Yaml::dump($yaml, 4, 2));
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

        $data = [
            'agent_managed'  => true,
            'default_branch' => $config->defaultBranch,
            'base_branch'    => $config->baseBranch,
            'strategy'       => $config->strategy,
            'branch_prefix'  => $config->branchPrefix,
            'commit_mode'    => $config->commitMode,
            'commit_pattern' => $config->commitPattern,
            'commit_template' => $config->commitTemplate !== '' ? $config->commitTemplate : null,
            'remote'         => $config->remote,
        ];

        return $data;
    }

    private function toNamespace(string $projectName): string
    {
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $projectName)));
    }
}
