<?php

declare(strict_types=1);

namespace Directive\Cli\Schema;

final class SpecDrivenSchema
{
    private const string TEMPLATES_DIR = __DIR__ . '/../Resources/schemas/spec-driven';

    /**
     * @return list<ArtifactDefinition>
     */
    public function artifacts(): array
    {
        return [
            new ArtifactDefinition(
                id: 'proposal',
                outputPath: 'proposal.md',
                deps: [],
                templatePath: self::TEMPLATES_DIR . '/proposal.md.php',
            ),
            new ArtifactDefinition(
                id: 'design',
                outputPath: 'design.md',
                deps: ['proposal'],
                templatePath: self::TEMPLATES_DIR . '/design.md.php',
            ),
            new ArtifactDefinition(
                id: 'specs',
                outputPath: 'specs/**/*.md',
                deps: ['proposal'],
                templatePath: self::TEMPLATES_DIR . '/specs.md.php',
            ),
            new ArtifactDefinition(
                id: 'tasks',
                outputPath: 'tasks.md',
                deps: ['design', 'specs'],
                templatePath: self::TEMPLATES_DIR . '/tasks.md.php',
            ),
        ];
    }
}
