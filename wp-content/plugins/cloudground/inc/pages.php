<?php

/**
 * Which page is which, in each language.
 *
 * WordPress knows its privacy page (`wp_page_for_privacy_policy`); the licence page is this
 * site's own, kept the same way: one option holding the default language's page, and
 * Polylang's translation of it for every other language. The theme asks here and never
 * looks a page up by its slug, which is a word an editor may change.
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

const CLOUDGROUND_OPTION_LICENSE_PAGE = 'cloudground_page_for_license';

/** The licence page in this reader's language, or 0 when the site has none published. */
function cloudground_license_page_id(): int
{
    $id = (int) get_option(CLOUDGROUND_OPTION_LICENSE_PAGE, 0);
    if ($id > 0 && function_exists('pll_get_post')) {
        $id = (int) (pll_get_post($id) ?: $id);
    }

    return $id > 0 && get_post_status($id) === 'publish' ? $id : 0;
}
