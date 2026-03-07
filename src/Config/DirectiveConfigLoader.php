<?php

declare(strict_types=1);

namespace Directive\Cli\Config;

use Directive\Cli\Config\Exception\ConfigNotFoundException;
use Symfony\Component\Yaml\Yaml;

final class DirectiveConfigLoader
{
    private const string CONFIG_PATH = 'directive-spec/context/common.yaml';

    public function load(string $cwd): DirectiveConfig
    {
        $configFile = $cwd . '/' . self::CONFIG_PATH;

        if (!file_exists($configFile)) {
            throw ConfigNotFoundException::missingFile($configFile);
        }

        /** @var array<string, mixed> $data */
        $data = Yaml::parseFile($configFile);

        $required = [
            'project.name'    => $data['project']['name'] ?? null,
            'context.namespace' => $data['context']['namespace'] ?? null,
            'context.stack'   => $data['context']['stack'] ?? null,
            'specs.path'      => $data['specs']['path'] ?? null,
            'changes.path'    => $data['changes']['path'] ?? null,
        ];

        foreach ($required as $key => $value) {
            if (!is_string($value) || $value === '') {
                throw ConfigNotFoundException::missingKey($key, $configFile);
            }
        }

        /** @var list<string> $stackFiles */
        $stackFiles = [];
        if (isset($data['context']['stack_files']) && is_array($data['context']['stack_files'])) {
            foreach ($data['context']['stack_files'] as $sf) {
                if (is_string($sf)) {
                    $stackFiles[] = $sf;
                }
            }
        }

        return new DirectiveConfig(
            projectName: (string) $data['project']['name'],
            namespace:   (string) $data['context']['namespace'],
            stack:       (string) $data['context']['stack'],
            specsPath:   (string) $data['specs']['path'],
            changesPath: (string) $data['changes']['path'],
            stackFiles:  $stackFiles,
        );
    }
}
