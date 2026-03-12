<?php

declare(strict_types=1);

namespace Directive\Cli\Command;

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
    public function __construct(private readonly array $generators = [])
    {
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

        $io->success(sprintf('Project "%s" created successfully in %s', $projectName, $projectDir));

        return Command::SUCCESS;
    }

    private function toNamespace(string $projectName): string
    {
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $projectName)));
    }
}
