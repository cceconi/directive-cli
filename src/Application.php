<?php

declare(strict_types=1);

namespace Directive\Cli;

use Symfony\Component\Console\Application as BaseApplication;

final class Application extends BaseApplication
{
    public function __construct()
    {
        parent::__construct('Directive CLI', '1.0.0');
    }
}
