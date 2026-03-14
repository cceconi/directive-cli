<?php

declare(strict_types=1);

namespace Directive\Cli\Config\Exception;

final class ConfigNotFoundException extends \RuntimeException
{
    public static function missingFile(string $path): self
    {
        return new self(
            sprintf(
                'Configuration file not found: %s. Run `directive new` first to initialise the project.',
                $path
            )
        );
    }

    public static function missingKey(string $key, string $path): self
    {
        return new self(sprintf('Required key "%s" is missing in %s.', $key, $path));
    }
}
