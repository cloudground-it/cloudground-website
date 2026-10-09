<?php

/**
 * Dynamic tags: the business's facts inside Elementor's own widgets — a heading with the
 * address, a button that calls, a text with the email — so a section built by hand says
 * what Settings → CloudGround says, and follows it.
 *
 * Two shapes cover almost everything: a text fact, and a link. Add a fact to
 * cloudground_fact_fields() (inc/facts.php) and it is a choice in both.
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

// phpcs:disable PSR1.Methods.CamelCapsMethodName, Generic.Files.OneObjectStructurePerFile.MultipleFound -- Elementor's method names; two small tags.

/** A fact as text: the full address is the one composed value. */
function cloudground_tag_fact(string $key): string
{
    $facts = cloudground_facts();
    if ($key === 'address') {
        $place = trim($facts['postcode'] . ' ' . $facts['city'] . ($facts['region'] !== '' ? ' (' . $facts['region'] . ')' : ''));

        return implode(', ', array_filter([$facts['street'], $place]));
    }

    return (string) ($facts[$key] ?? '');
}

class Cloudground_Tag_Fact extends \Elementor\Core\DynamicTags\Tag
{
    public function get_name(): string
    {
        return 'cloudground-fact';
    }

    public function get_title(): string
    {
        return __('Fact', 'cloudground');
    }

    public function get_group(): string
    {
        return 'cloudground';
    }

    public function get_categories(): array
    {
        return [\Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY];
    }

    protected function register_controls(): void
    {
        $this->add_control('field', [
            'label' => __('Which', 'cloudground'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => ['address' => __('Full address', 'cloudground')] + cloudground_fact_fields(),
            'default' => 'name',
        ]);
    }

    public function render(): void
    {
        echo esc_html(cloudground_tag_fact((string) $this->get_settings('field')));
    }
}

class Cloudground_Tag_Link extends \Elementor\Core\DynamicTags\Data_Tag
{
    public function get_name(): string
    {
        return 'cloudground-link';
    }

    public function get_title(): string
    {
        return __('Contact link', 'cloudground');
    }

    public function get_group(): string
    {
        return 'cloudground';
    }

    public function get_categories(): array
    {
        return [\Elementor\Modules\DynamicTags\Module::URL_CATEGORY];
    }

    protected function register_controls(): void
    {
        $this->add_control('target', [
            'label' => __('Where it leads', 'cloudground'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => ['phone' => __('Call', 'cloudground'), 'email' => __('Write', 'cloudground'), 'map' => __('The map', 'cloudground')],
            'default' => 'phone',
        ]);
    }

    public function get_value(array $options = []): string
    {
        $facts = cloudground_facts();

        return match ((string) $this->get_settings('target')) {
            'phone' => $facts['phone'] !== '' ? 'tel:' . preg_replace('/[^0-9+]/', '', $facts['phone']) : '',
            'email' => $facts['email'] !== '' ? 'mailto:' . $facts['email'] : '',
            'map' => cloudground_tag_fact('address') !== '' ? 'https://www.openstreetmap.org/search?query=' . rawurlencode(cloudground_tag_fact('address')) : '',
            default => '',
        };
    }
}
