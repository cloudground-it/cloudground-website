<?php
/**
 * The hero's oscilloscope: a WordPress site under traffic, with the cache on or off.
 *
 * An illustration, not a measurement — it says so under itself. The plugin's «Cache
 * oscilloscope» widget prints this part. Both states are in the markup, each word from
 * cloudground_s(); the state is one attribute on the root (`data-cache`), which the switch
 * (lib/scope.ts) flips and the stylesheet reads. Without JavaScript the switch is not shown
 * and the starting state is the whole of it.
 *
 * @var array $args the widget's settings: start_on
 */

defined('ABSPATH') || exit;

$on = ($args['start_on'] ?? 'yes') === 'yes';
$state = static fn (string $when, string $key): string => sprintf('<span data-when="%s">%s</span>', $when, cloudground_e(cloudground_s($key)));
$readings = [
    ['cache', 'scope.cache', 'scope.cacheOn', 'scope.cacheOff'],
    ['php', 'scope.php', 'scope.phpOn', 'scope.phpOff'],
    ['db', 'scope.db', 'scope.dbOn', 'scope.dbOff'],
];
?>
<div class="scope" data-scope data-cache="<?= $on ? 'on' : 'off' ?>"
     data-describe-on="<?= esc_attr(cloudground_s('scope.describeOn')) ?>"
     data-describe-off="<?= esc_attr(cloudground_s('scope.describeOff')) ?>">
    <div class="scope-head">
        <p class="hud"><?= cloudground_e(cloudground_s('scope.caption')) ?></p>
        <button type="button" class="scope-toggle hud" aria-pressed="<?= $on ? 'true' : 'false' ?>">
            <?= cloudground_e(cloudground_s('scope.toggle')) ?>: <?= $state('on', 'scope.on') . $state('off', 'scope.off') // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in $state. ?>
        </button>
    </div>
    <div class="scope-screen" role="img" aria-label="<?= esc_attr(cloudground_s($on ? 'scope.describeOn' : 'scope.describeOff')) ?>">
        <span class="scope-corner scope-corner--tl" aria-hidden="true"></span>
        <span class="scope-corner scope-corner--tr" aria-hidden="true"></span>
        <span class="scope-corner scope-corner--bl" aria-hidden="true"></span>
        <span class="scope-corner scope-corner--br" aria-hidden="true"></span>
        <svg class="scope-plot" viewBox="0 0 600 230" preserveAspectRatio="none" aria-hidden="true" focusable="false">
            <path class="scope-grid" d="M0 46H600M0 92H600M0 138H600M0 184H600M100 0V230M200 0V230M300 0V230M400 0V230M500 0V230"/>
            <g class="scope-wave scope-wave--on" data-when="on"><path d="M0 170 L40 168 L60 150 L80 172 L120 166 L140 158 L160 170 L200 167 L230 152 L250 171 L300 166 L340 169 L360 155 L380 171 L420 167 L440 160 L460 170 L500 166 L530 151 L550 172 L600 170 L640 168 L660 150 L680 172 L720 166 L740 158 L760 170 L800 167 L830 152 L850 171 L900 166 L940 169 L960 155 L980 171 L1020 167 L1040 160 L1060 170 L1100 166 L1130 151 L1150 172 L1200 170"/></g>
            <g class="scope-wave scope-wave--off" data-when="off"><path d="M0 120 L30 60 L50 140 L70 40 L100 110 L120 30 L150 130 L170 50 L200 100 L220 20 L250 140 L280 60 L300 120 L330 40 L350 130 L380 70 L400 110 L420 25 L450 135 L480 55 L500 115 L520 35 L550 128 L580 48 L600 120 L630 60 L650 140 L670 40 L700 110 L720 30 L750 130 L770 50 L800 100 L820 20 L850 140 L880 60 L900 120 L930 40 L950 130 L980 70 L1000 110 L1020 25 L1050 135 L1080 55 L1100 115 L1120 35 L1150 128 L1180 48 L1200 120"/></g>
        </svg>
        <span class="scope-label hud" aria-hidden="true"><?= cloudground_e(cloudground_s('scope.axis')) ?></span>
        <span class="scope-state hud" aria-hidden="true"><?= $state('on', 'scope.stable') . $state('off', 'scope.strained') // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in $state. ?></span>
    </div>
    <dl class="scope-readings">
        <?php foreach ($readings as [$name, $label, $whenOn, $whenOff]) : ?>
            <div class="scope-reading scope-reading--<?= esc_attr($name) ?>">
                <dt class="hud"><?= cloudground_e(cloudground_s($label)) ?></dt>
                <dd>
                    <span class="scope-value"><?= $state('on', $whenOn) . $state('off', $whenOff) // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in $state. ?></span>
                    <span class="scope-bar" aria-hidden="true"></span>
                </dd>
            </div>
        <?php endforeach; ?>
    </dl>
    <p class="scope-note hud"><?= cloudground_html(cloudground_s('scope.note')) ?></p>
</div>
