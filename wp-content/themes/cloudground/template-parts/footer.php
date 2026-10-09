<?php
/**
 * The footer: the mark and the line it stands for, three short columns of links, and the
 * name drawn as large as the page allows and cut off by its bottom edge.
 *
 * The columns are this template's and not a menu: they group links by kind (the home's
 * sections, the product's other homes, the legal pages), and every address comes from
 * where it is kept — the facts for the docs and the code, the plugin for the licence page,
 * WordPress for the privacy page — so none of them is typed twice.
 */

defined('ABSPATH') || exit;

$who = cloudground_who();
$columns = [
    'product' => [
        ['features', cloudground_anchor_url('funzioni')],
        ['measures', cloudground_anchor_url('misure')],
        ['install', cloudground_anchor_url('installa')],
    ],
    'resources' => [
        ['docs', $who['docs']],
        // The releases are the repository's own page: GitHub keeps one for every repository.
        ['releases', $who['repository'] !== '' ? untrailingslashit($who['repository']) . '/releases' : ''],
        ['repository', $who['repository']],
    ],
    'legal' => [
        ['license', cloudground_license_url()],
        ['privacy', cloudground_privacy_url()],
    ],
];
?>
<footer class="site-footer on-dark">
    <div class="site-footer-inner">
        <div class="site-footer-brand">
            <?= cloudground_mark(40, 'inverse') // phpcs:ignore WordPress.Security.EscapeOutput -- fixed SVG. ?>
            <p class="hud"><?= cloudground_e(cloudground_s('footer.tagline')) ?></p>
        </div>
        <nav class="site-footer-nav" aria-label="<?= esc_attr(cloudground_s('footer.label')) ?>">
            <?php foreach ($columns as $group => $links) : ?>
                <?php $links = array_filter($links, static fn (array $l): bool => $l[1] !== ''); ?>
                <?php if ($links !== []) : ?>
                    <div class="site-footer-col">
                        <p class="hud"><?= cloudground_e(cloudground_s("footer.$group")) ?></p>
                        <ul>
                            <?php foreach ($links as [$key, $url]) : ?>
                                <li><a href="<?= esc_url($url) ?>"><?= cloudground_e(cloudground_s("footer.$key")) ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </nav>
    </div>
    <?php /* The name as a picture: drawn from an attribute, so it is neither read aloud nor
             measured as text, which at this contrast it is not meant to be. */ ?>
    <div class="site-footer-wordmark" aria-hidden="true" data-word="<?= esc_attr(strtolower($who['name'])) ?>"></div>
</footer>
