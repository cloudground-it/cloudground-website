<?php
/**
 * Everything below the page: the Theme Builder's footer where the site has built one, the
 * theme's own otherwise. A template that wants none (a 404, a step of a form) sets
 * $GLOBALS['cloudground_no_footer'].
 */

defined('ABSPATH') || exit;

if (empty($GLOBALS['cloudground_no_footer']) && !cloudground_location('footer')) {
    get_template_part('template-parts/footer');
}
?>
        <?php wp_footer(); ?>
    </body>
</html>
