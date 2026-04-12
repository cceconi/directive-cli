<?php

declare(strict_types=1);

use Directive\Cli\Command\UpdateHelper;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

// ─── Test: file absent → written directly ────────────────────────────────────

it('writes the file directly when it does not exist', function (): void {
    $tmpDir = sys_get_temp_dir() . '/directive-helper-test-' . uniqid();
    (new Filesystem())->mkdir($tmpDir);

    $dest    = $tmpDir . '/prompt.md';
    $content = "# Hello\n";
    $io      = new SymfonyStyle(new ArrayInput([]), new NullOutput());

    $written = (new UpdateHelper())->safeDumpFile($dest, $content, false, $io);

    expect($written)->toBeTrue();
    expect(file_get_contents($dest))->toBe($content);

    (new Filesystem())->remove($tmpDir);
});

// ─── Test: content identical → not written ───────────────────────────────────

it('skips writing when the generated content is identical to the existing file', function (): void {
    $tmpDir = sys_get_temp_dir() . '/directive-helper-test-' . uniqid();
    (new Filesystem())->mkdir($tmpDir);

    $dest    = $tmpDir . '/prompt.md';
    $content = "# Hello\n";
    file_put_contents($dest, $content);
    $io = new SymfonyStyle(new ArrayInput([]), new NullOutput());

    $written = (new UpdateHelper())->safeDumpFile($dest, $content, false, $io);

    expect($written)->toBeFalse();
    // File is unchanged
    expect(file_get_contents($dest))->toBe($content);

    (new Filesystem())->remove($tmpDir);
});

// ─── Test: content differs + --force → overwritten ───────────────────────────

it('overwrites without confirmation when force is true', function (): void {
    $tmpDir = sys_get_temp_dir() . '/directive-helper-test-' . uniqid();
    (new Filesystem())->mkdir($tmpDir);

    $dest    = $tmpDir . '/prompt.md';
    file_put_contents($dest, "old content\n");
    $new = "new content\n";
    $io  = new SymfonyStyle(new ArrayInput([]), new NullOutput());

    $written = (new UpdateHelper())->safeDumpFile($dest, $new, true, $io);

    expect($written)->toBeTrue();
    expect(file_get_contents($dest))->toBe($new);

    (new Filesystem())->remove($tmpDir);
});

// ─── Test: content differs without --force → user confirms no → not written ──

it('does not write when user declines confirmation', function (): void {
    $tmpDir = sys_get_temp_dir() . '/directive-helper-test-' . uniqid();
    (new Filesystem())->mkdir($tmpDir);

    $dest    = $tmpDir . '/prompt.md';
    $old     = "old content\n";
    file_put_contents($dest, $old);
    $new = "new content\n";

    // Fake a SymfonyStyle that always answers "no" to confirm()
    $io = new class (new ArrayInput([]), new NullOutput()) extends SymfonyStyle {
        public function confirm(string $question, bool $default = true): bool
        {
            return false;
        }
    };

    $written = (new UpdateHelper())->safeDumpFile($dest, $new, false, $io);

    expect($written)->toBeFalse();
    expect(file_get_contents($dest))->toBe($old);

    (new Filesystem())->remove($tmpDir);
});

// ─── Test: countLineDiff ─────────────────────────────────────────────────────

it('counts the number of differing lines between two strings', function (): void {
    $helper = new UpdateHelper();

    $old = "line1\nline2\nline3\n";
    $new = "line1\nline2-changed\nline3\n";

    // 1 line removed, 1 line added → 2
    expect($helper->countLineDiff($old, $new))->toBe(2);
});

it('returns 0 for identical strings', function (): void {
    $helper = new UpdateHelper();

    $content = "same\ncontent\n";
    expect($helper->countLineDiff($content, $content))->toBe(0);
});
