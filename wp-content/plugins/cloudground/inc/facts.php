<?php

/**
 * The business's facts: name, address, telephone, email, VAT number.
 *
 * One option, one screen (Settings → CloudGround), read on every request. They are what the
 * dynamic tags print inside native widgets, what `{name}` and the other placeholders stand
 * for in the site's sentences, what the footer shows and what the structured data declares
 * — so a changed address changes everywhere at once, and is never typed into a page.
 *
 * **Never invent a fact.** An empty field is an absent fact: the footer leaves the line
 * out and the structured data leaves the key out.
 *
 * A richer project replaces this screen with a panel of its own (a React app on the REST
 * API, as the workspace's plugins have); the option and cloudground_facts() stay the same.
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

const CLOUDGROUND_OPTION_FACTS = 'cloudground_facts';

/** @return array<string, string> key => label */
function cloudground_fact_fields(): array
{
    return [
        'name' => __('Name', 'cloudground'),
        'street' => __('Street and number', 'cloudground'),
        'postcode' => __('Postcode', 'cloudground'),
        'city' => __('City', 'cloudground'),
        'region' => __('Province or region', 'cloudground'),
        'country' => __('Country (two letters)', 'cloudground'),
        'phone' => __('Telephone', 'cloudground'),
        'email' => __('Email', 'cloudground'),
        'vat' => __('VAT number', 'cloudground'),
    ];
}

/** @return array<string, string> the facts, every key present (empty when unknown) */
function cloudground_facts(): array
{
    $stored = get_option(CLOUDGROUND_OPTION_FACTS, []);
    $stored = is_array($stored) ? $stored : [];
    $out = [];
    foreach (array_keys(cloudground_fact_fields()) as $key) {
        $out[$key] = trim((string) ($stored[$key] ?? ''));
    }
    if ($out['name'] === '') {
        $out['name'] = wp_specialchars_decode((string) get_bloginfo('name'), ENT_QUOTES);
    }

    return $out;
}

add_action('admin_init', static function (): void {
    register_setting('cloudground_facts', CLOUDGROUND_OPTION_FACTS, [
        'type' => 'array',
        'sanitize_callback' => static function ($input): array {
            $out = [];
            foreach (cloudground_fact_fields() as $key => $label) {
                $value = sanitize_text_field((string) ($input[$key] ?? ''));
                $out[$key] = match ($key) {
                    'email' => is_email($value) ? $value : '',
                    'country' => strtoupper(substr($value, 0, 2)),
                    default => $value,
                };
            }

            return $out;
        },
    ]);
    add_settings_section('cloudground_facts_main', '', '__return_false', 'cloudground');
    foreach (cloudground_fact_fields() as $key => $label) {
        add_settings_field($key, $label, static function () use ($key): void {
            printf(
                '<input type="%1$s" class="regular-text" id="cloudground-fact-%2$s" name="%3$s[%2$s]" value="%4$s" />',
                $key === 'email' ? 'email' : 'text',
                esc_attr($key),
                esc_attr(CLOUDGROUND_OPTION_FACTS),
                esc_attr(cloudground_facts()[$key] ?? ''),
            );
        }, 'cloudground', 'cloudground_facts_main', ['label_for' => 'cloudground-fact-' . $key]);
    }
});

add_action('admin_menu', static function (): void {
    add_options_page(__('CloudGround', 'cloudground'), __('CloudGround', 'cloudground'), 'manage_options', 'cloudground', static function (): void {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('CloudGround — the facts', 'cloudground'); ?></h1>
            <p><?php esc_html_e('What the site says about the business, in one place: the footer, the contact card, the dynamic tags and the structured data read it from here. Leave a field empty rather than guess.', 'cloudground'); ?></p>
            <form method="post" action="options.php">
                <?php
                settings_fields('cloudground_facts');
                do_settings_sections('cloudground');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    });
});
