<?php
/**
 * The footer: the business's facts and the legal links.
 */

defined('ABSPATH') || exit;

$who = cloudground_who();
$privacy = cloudground_privacy_url();
?>
<footer class="site-footer on-dark">
    <div class="site-footer-inner">
        <p class="site-footer-name"><?= cloudground_e($who['name']) ?></p>
        <?php if ($who['street'] !== '' || $who['city'] !== '') : ?>
            <p><?= cloudground_e(trim($who['street'] . ', ' . $who['city'], ', ')) ?></p>
        <?php endif; ?>
        <p class="site-footer-legal">
            <span><?= cloudground_e(cloudground_s('footer.rights')) ?></span>
            <?php if ($privacy !== '') : ?>
                <a href="<?= esc_url($privacy) ?>"><?= cloudground_e(cloudground_s('footer.privacy')) ?></a>
            <?php endif; ?>
        </p>
    </div>
</footer>
