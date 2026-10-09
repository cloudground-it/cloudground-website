<?php

/**
 * Structured data: one JSON-LD graph per page, built from the facts in Settings → CloudGround.
 *
 * Three rules.
 * - **Nothing declared to machines that the page does not show.** A fact in the graph is a
 *   fact a person can read on the site; an empty fact is an absent key, never "".
 * - **No ratings of your own.** Search engines treat a business's own reviews marked up as
 *   `Review` / `AggregateRating` as self-serving; leave them out.
 * - **JSON_HEX_TAG** is load-bearing: it turns every `<` into `<`, so a string holding
 *   `</script>` cannot close the block early.
 *
 * Change `@type` to the most specific type the business is (`LocalBusiness`, `Dentist`,
 * `LegalService`, …) through the `cloudground_schema_type` filter or here.
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

/**
 * The site's own address, the same in every language: what the graph's identifiers hang
 * from. home_url() will not do — on an English page Polylang rewrites it to `/en/`, and the
 * business would be a different node in each language.
 */
function cloudground_schema_root(): string
{
    return trailingslashit((string) get_option('home'));
}

/** @return array<string, mixed> */
function cloudground_schema_business(): array
{
    $facts = function_exists('cloudground_facts') ? cloudground_facts() : [];
    $who = cloudground_who();
    $address = array_filter([
        '@type' => 'PostalAddress',
        'streetAddress' => (string) ($facts['street'] ?? ''),
        'postalCode' => (string) ($facts['postcode'] ?? ''),
        'addressLocality' => (string) ($facts['city'] ?? ''),
        'addressRegion' => (string) ($facts['region'] ?? ''),
        'addressCountry' => (string) ($facts['country'] ?? ''),
    ]);

    return array_filter([
        '@type' => (string) apply_filters('cloudground_schema_type', 'Organization'),
        '@id' => cloudground_schema_root() . '#business',
        'name' => $who['name'],
        'url' => home_url('/'),
        'telephone' => $who['phone'],
        'email' => $who['email'],
        'address' => count($address) > 1 ? $address : null,
        'vatID' => (string) ($facts['vat'] ?? ''),
    ], static fn ($v): bool => $v !== '' && $v !== null);
}

/** @return list<array<string, mixed>> */
function cloudground_schema_graph(): array
{
    $graph = [cloudground_schema_business()];
    $graph[] = [
        '@type' => 'WebSite',
        '@id' => cloudground_schema_root() . '#website',
        'url' => home_url('/'),
        'name' => cloudground_who()['name'],
        'inLanguage' => cloudground_html_lang(),
        'publisher' => ['@id' => cloudground_schema_root() . '#business'],
    ];
    if (is_singular()) {
        $graph[] = [
            '@type' => 'WebPage',
            '@id' => cloudground_canonical() . '#webpage',
            'url' => cloudground_canonical(),
            'name' => wp_get_document_title(),
            'inLanguage' => cloudground_html_lang(),
            'isPartOf' => ['@id' => cloudground_schema_root() . '#website'],
        ];
    }

    return (array) apply_filters('cloudground_schema_graph', $graph);
}

add_action('wp_head', static function (): void {
    if (cloudground_is_noindex()) {
        return;
    }
    $json = wp_json_encode(
        ['@context' => 'https://schema.org', '@graph' => cloudground_schema_graph()],
        JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG,
    );
    if (is_string($json)) {
        echo '<script type="application/ld+json">' . $json . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON_HEX_TAG, see above.
    }
}, 8);
