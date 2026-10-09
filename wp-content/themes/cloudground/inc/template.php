<?php

/**
 * The helpers the templates call.
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

/** Escaped text. The one way words reach the page. */
function cloudground_e(mixed $value): string
{
    return esc_html((string) $value);
}

/**
 * Words from the tree that may hold `<em>`, `<strong>`, `<br>` or a link — never a
 * visitor's input, which goes through cloudground_e().
 */
function cloudground_html(string $value): string
{
    return wp_kses($value, ['em' => [], 'strong' => [], 'br' => [], 'a' => ['href' => true]]);
}

/**
 * Whether Elementor Pro's Theme Builder printed a location (header, footer, single,
 * archive). The theme's own markup runs when it did not.
 */
function cloudground_location(string $location): bool
{
    return function_exists('elementor_theme_do_location') && elementor_theme_do_location($location);
}

/** Whether this document was laid out in the builder: Elementor's own flag, not "has content". */
function cloudground_page_is_built(?int $id = null): bool
{
    $id ??= (int) get_the_ID();

    return $id > 0 && get_post_meta($id, '_elementor_edit_mode', true) === 'builder';
}

/**
 * The body of a page, from wherever it is: the Theme Builder's document for this kind of
 * page, then the builder's layout for this one page, then the theme's own template part.
 *
 * The third is what runs with no Elementor at all, and it is the same part the plugin's
 * widget prints — so the three are the same page, and the markup exists once.
 */
function cloudground_document(string $location, string $part, array $args = []): void
{
    if (cloudground_location($location)) {
        return;
    }
    if (cloudground_page_is_built()) {
        the_content();

        return;
    }
    get_template_part("template-parts/$part", null, $args);
}

/** The home page, in this reader's language. */
function cloudground_home_url(): string
{
    return function_exists('pll_home_url') ? (string) pll_home_url() : home_url('/');
}

/**
 * The privacy notice, in this reader's language: WordPress's privacy page and its
 * Polylang translation. A plain `/privacy/` would send every language to the default one.
 */
function cloudground_privacy_url(): string
{
    $id = (int) get_option('wp_page_for_privacy_policy');
    if ($id > 0 && function_exists('pll_get_post')) {
        $id = (int) (pll_get_post($id) ?: $id);
    }

    return $id > 0 && get_post_status($id) === 'publish' ? (string) get_permalink($id) : '';
}

/**
 * A reveal on scroll, for the theme's own markup: `data-reveal`, with the stagger as a
 * custom property next to the element it times. Native Elementor widgets get the same
 * through a container with the class `cloudground-reveal`.
 */
function cloudground_reveal(float $delay = 0.0): string
{
    return $delay > 0 ? sprintf('data-reveal style="--reveal-delay:%ss"', rtrim(rtrim(number_format($delay, 2, '.', ''), '0'), '.')) : 'data-reveal';
}
