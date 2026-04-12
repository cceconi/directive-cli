<?php

declare(strict_types=1);

namespace Directive\Cli\Command;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

final class UpdateHelper
{
    /**
     * Compares generated content with the existing file and writes if appropriate.
     *
     * - File absent      → write directly, return true
     * - Content identical → skip, inform "up to date", return false
     * - Content differs, $force=true  → overwrite, return true
     * - Content differs, $force=false → show diff summary, ask [y/N], return true/false
     */
    public function safeDumpFile(string $dest, string $generated, bool $force, SymfonyStyle $io): bool
    {
        if (!file_exists($dest)) {
            (new Filesystem())->dumpFile($dest, $generated);
            return true;
        }

        $existing = file_get_contents($dest);
        if ($existing === false) {
            $existing = '';
        }

        if ($existing === $generated) {
            return false;
        }

        if ($force) {
            (new Filesystem())->dumpFile($dest, $generated);
            return true;
        }

        $diff  = $this->countLineDiff($existing, $generated);
        $label = basename($dest);
        $io->writeln(sprintf('  <comment>~</comment> %s <fg=gray>(~%d line(s) changed)</>', $label, $diff));

        if (!$io->confirm(sprintf('Overwrite <info>%s</info>?', $label), false)) {
            return false;
        }

        (new Filesystem())->dumpFile($dest, $generated);
        return true;
    }

    /**
     * Returns the number of lines that differ between $old and $new (symmetric diff size).
     */
    public function countLineDiff(string $old, string $new): int
    {
        $oldLines = explode("\n", $old);
        $newLines = explode("\n", $new);

        $added   = count(array_diff($newLines, $oldLines));
        $removed = count(array_diff($oldLines, $newLines));

        return $added + $removed;
    }
}
