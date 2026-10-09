<?php
/**
 * The measurements table. The plugin's «Measurements table» widget prints this part.
 *
 * A row is one paragraph of the widget's field (or one entry of the tree), five values
 * separated by « | »: scenario | note | requests per second | p99 | errors. Until a
 * benchmark has been run on CloudGround itself the numbers are placeholders, printed as
 * they are: a number nobody measured is not written here.
 *
 * The table scrolls sideways on a narrow screen rather than squeezing its numbers; the
 * scrolling box is a focusable, named region so a keyboard can scroll it too.
 */

defined('ABSPATH') || exit;

$rows = array_values(array_filter(array_map(
    static fn (string $line): array => array_pad(array_map('trim', explode('|', $line)), 5, ''),
    array_map('strval', (array) cloudground_t('measures.rows', [])),
), static fn (array $cells): bool => $cells[0] !== ''));
$notes = array_map('strval', (array) cloudground_t('measures.notes', []));
$caption = cloudground_s('measures.caption');
?>
<div class="measures">
    <div class="measures-scroll" role="region" aria-label="<?= esc_attr($caption) ?>" tabindex="0">
        <table class="measures-table">
            <caption class="visually-hidden"><?= cloudground_e($caption) ?></caption>
            <thead>
                <tr>
                    <th scope="col" class="hud"><?= cloudground_e(cloudground_s('measures.scenario')) ?></th>
                    <th scope="col" class="hud"><?= cloudground_e(cloudground_s('measures.rps')) ?></th>
                    <th scope="col" class="hud"><?= cloudground_e(cloudground_s('measures.p99')) ?></th>
                    <th scope="col" class="hud"><?= cloudground_e(cloudground_s('measures.errors')) ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as [$scenario, $note, $rps, $p99, $errors]) : ?>
                    <tr class="measures-row">
                        <th scope="row">
                            <span class="measures-scenario"><?= cloudground_e($scenario) ?></span>
                            <?php if ($note !== '') : ?><span class="measures-note hud"><?= cloudground_e($note) ?></span><?php endif; ?>
                        </th>
                        <td class="measures-number"><?= cloudground_e($rps) ?></td>
                        <td class="measures-number"><?= cloudground_e($p99) ?></td>
                        <td class="measures-errors hud"><?= cloudground_e($errors) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php if ($notes !== []) : ?>
        <ul class="measures-notes hud">
            <?php foreach ($notes as $line) : ?>
                <li><?= cloudground_e($line) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
