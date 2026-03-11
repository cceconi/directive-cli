<?php

declare(strict_types=1);

namespace Directive\Cli;

use Directive\Cli\Command\ChangeNewCommand;
use Directive\Cli\Command\ChangeInstructionsCommand;
use Directive\Cli\Command\ChangeStatusCommand;
use Directive\Cli\Command\NewProjectCommand;
use Directive\Cli\Generator\CoreGenerator;
use Directive\Cli\Generator\DockerGenerator;
use Directive\Cli\Generator\IdeAntigravityGenerator;
use Directive\Cli\Generator\IdeClineGenerator;
use Directive\Cli\Generator\IdeClaudeGenerator;
use Directive\Cli\Generator\IdeCodexGenerator;
use Directive\Cli\Generator\IdeContinueGenerator;
use Directive\Cli\Generator\IdeCursorGenerator;
use Directive\Cli\Generator\IdeGeminiGenerator;
use Directive\Cli\Generator\IdeGithubCopilotGenerator;
use Directive\Cli\Generator\IdeKiroGenerator;
use Directive\Cli\Generator\IdeRoocodeGenerator;
use Directive\Cli\Generator\IdeWindsurfGenerator;
use Directive\Cli\Generator\IdeZedGenerator;
use Symfony\Component\Console\Application as BaseApplication;

final class Application extends BaseApplication
{
    public function __construct()
    {
        parent::__construct('Directive CLI', '1.0.0');
        $this->addCommand(new ChangeNewCommand());
        $this->addCommand(new ChangeStatusCommand());
        $this->addCommand(new ChangeInstructionsCommand());
        $this->addCommand(new NewProjectCommand([
            new CoreGenerator(),
            new DockerGenerator(),
            new IdeGithubCopilotGenerator(),
            new IdeCursorGenerator(),
            new IdeClaudeGenerator(),
            new IdeAntigravityGenerator(),
            new IdeWindsurfGenerator(),
            new IdeClineGenerator(),
            new IdeRoocodeGenerator(),
            new IdeContinueGenerator(),
            new IdeCodexGenerator(),
            new IdeKiroGenerator(),
            new IdeGeminiGenerator(),
            new IdeZedGenerator(),
        ]));
    }
}
