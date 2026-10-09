<?php
/**
 * The header: the project's status in one line, then the sticky bar — the mark, the menu
 * (Appearance → Menus, one per language with Polylang), the source code, the languages and
 * the way to install. No JavaScript: a menu of a few links is a list of links.
 *
 * The status line sits outside the sticky `<header>` on purpose: a sticky element sticks
 * only within its parent, so the bar has to be the body's child for it to follow the
 * whole page, and the line above it is meant to scroll away.
 */

defined('ABSPATH') || exit;

$languages = cloudground_alternates();
$who = cloudground_who();
?>
<aside class="status-strip" aria-label="<?= esc_attr(cloudground_s('status.label')) ?>">
    <ul class="status-strip-inner hud">
        <li class="status-live"><span class="led" aria-hidden="true"></span><?= cloudground_e(cloudground_s('status.release')) ?></li>
        <li><?= cloudground_e(cloudground_s('status.systems')) ?></li>
        <li><?= cloudground_e(cloudground_s('status.license')) ?></li>
        <li class="status-price"><?= cloudground_e(cloudground_s('status.price')) ?></li>
    </ul>
</aside>
<header class="site-header">
    <div class="site-header-inner">
        <a class="brand" href="<?= esc_url(cloudground_home_url()) ?>" aria-label="<?= esc_attr(cloudground_s('nav.home')) ?>">
            <?= cloudground_mark(30, 'float') // phpcs:ignore WordPress.Security.EscapeOutput -- fixed SVG, attributes escaped inside. ?>
            <span class="brand-word" aria-hidden="true"><?= cloudground_e(strtolower($who['name'])) ?></span>
        </a>
        <nav class="site-nav" aria-label="<?= esc_attr(cloudground_s('nav.menu')) ?>">
            <?php if (has_nav_menu('primary')) : ?>
                <?php wp_nav_menu(['theme_location' => 'primary', 'container' => false, 'menu_class' => 'site-nav-list', 'depth' => 1, 'fallback_cb' => false]); ?>
            <?php endif; ?>
            <?php if ($who['repository'] !== '') : ?>
                <a class="site-nav-repo hud" href="<?= esc_url($who['repository']) ?>"><?= cloudground_e(cloudground_s('nav.repository')) ?> <span aria-hidden="true">↗</span></a>
            <?php endif; ?>
        </nav>
        <?php if (count($languages) > 1) : ?>
            <nav class="site-languages" aria-label="<?= esc_attr(cloudground_s('nav.languages')) ?>">
                <ul>
                    <?php foreach ($languages as $language) : ?>
                        <li>
                            <a class="hud" href="<?= esc_url($language['url']) ?>" hreflang="<?= esc_attr($language['hreflang']) ?>" lang="<?= esc_attr($language['hreflang']) ?>"<?= $language['current'] ? ' aria-current="true"' : '' ?>>
                                <span aria-hidden="true"><?= cloudground_e(strtoupper($language['locale'])) ?></span>
                                <span class="visually-hidden"><?= cloudground_e($language['endonym']) ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </nav>
        <?php endif; ?>
        <a class="site-cta hud" href="<?= esc_url(cloudground_anchor_url('installa')) ?>"><?= cloudground_e(cloudground_s('nav.install')) ?></a>
    </div>
</header>
