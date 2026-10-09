<?php
/**
 * The installer's terminal: the command, a button that copies it, and what the installer
 * prints. The plugin's «Install terminal» widget prints this part; lib/terminal.ts copies
 * the command and plays the lines once the terminal is on screen.
 *
 * What is copied is the `<code>`'s own text, so the button can never copy something other
 * than what the reader sees. Without JavaScript the button is not shown and the command
 * can be selected like any text.
 */

defined('ABSPATH') || exit;

$output = array_values(array_filter(array_map('strval', (array) cloudground_t('terminal.output', [])), static fn (string $l): bool => $l !== ''));
?>
<div class="terminal on-dark" data-terminal>
    <div class="terminal-bar">
        <span class="terminal-tab hud"><?= cloudground_e(cloudground_s('terminal.user')) ?></span>
        <span class="terminal-tab terminal-tab--quiet hud"><?= cloudground_e(cloudground_s('terminal.shell')) ?></span>
        <button type="button" class="terminal-copy hud" data-copied="<?= esc_attr(cloudground_s('terminal.copied')) ?>">
            <span data-copy-label><?= cloudground_e(cloudground_s('terminal.copy')) ?></span><span class="visually-hidden" data-copy-what> <?= cloudground_e(cloudground_s('terminal.copyWhat')) ?></span>
        </button>
        <span class="visually-hidden" role="status" data-copy-status></span>
    </div>
    <div class="terminal-body">
        <p class="terminal-command"><span class="terminal-prompt" aria-hidden="true">#</span> <code data-command><?= cloudground_e(cloudground_s('terminal.command')) ?></code><span class="caret" aria-hidden="true"></span></p>
        <?php foreach ($output as $i => $line) : ?>
            <p class="terminal-line" style="--line: <?= (int) $i ?>"><?= cloudground_e($line) ?></p>
        <?php endforeach; ?>
    </div>
</div>
