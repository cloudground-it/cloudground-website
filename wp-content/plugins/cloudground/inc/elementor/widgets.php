<?php

/**
 * The widgets: one base class with all the behaviour, and one subclass per spec holding a
 * single constant.
 *
 * **It has to be a class each.** Elementor does not keep the instance that was registered:
 * when it renders a document it reads the element's `widgetType`, looks up the registered
 * widget and does `new $class($element_data, $args)`. A single class parameterised through
 * its constructor is rebuilt without its parameters — a TypeError on the first render, and
 * nothing at all in the editor. Everything that varies is data, in sections.php.
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

abstract class Cloudground_Section extends \Elementor\Widget_Base
{
    /** The key into cloudground_elementor_sections(). */
    protected const SECTION = '';

    /** @return array<string, mixed> */
    protected function spec(): array
    {
        return cloudground_elementor_sections()[static::SECTION] ?? [];
    }

    public function get_name(): string
    {
        return 'cloudground-' . static::SECTION;
    }

    public function get_title(): string
    {
        return (string) ($this->spec()['title'] ?? static::SECTION);
    }

    public function get_icon(): string
    {
        return (string) ($this->spec()['icon'] ?? 'eicon-text');
    }

    public function get_categories(): array
    {
        return ['cloudground'];
    }

    public function get_keywords(): array
    {
        return array_merge(['cloudground'], (array) ($this->spec()['keywords'] ?? []));
    }

    /**
     * No stylesheet of its own: the section's rules are in the page's bundle, and a
     * dependency here would make Elementor load a second copy.
     */
    public function get_style_depends(): array
    {
        return [];
    }

    protected function register_controls(): void
    {
        $spec = $this->spec();
        $controls = (array) ($spec['controls'] ?? []);
        $texts = (array) ($spec['texts'] ?? []);

        if ($controls !== []) {
            $this->start_controls_section('cloudground_content', ['label' => __('Content', 'cloudground')]);
            foreach ($controls as $name => $control) {
                // A select's options can be a list that exists only at runtime (the posts of
                // a type, say), so they may arrive as a callable.
                if (isset($control['options']) && is_callable($control['options'])) {
                    $control['options'] = ($control['options'])();
                }
                $this->add_control($name, $control);
            }
            $this->end_controls_section();
        }

        if ($texts !== []) {
            $this->start_controls_section('cloudground_texts', ['label' => __('Texts', 'cloudground')]);
            $this->add_control('cloudground_texts_note', [
                'type' => \Elementor\Controls_Manager::RAW_HTML,
                'raw' => '<p class="elementor-descriptor">' . esc_html((string) ($spec['note'] ?? __('Each field starts from the site’s words in this language; empty, it says them. {name}, {city}, {street}, {phone} and {email} become the facts in Settings → CloudGround.', 'cloudground'))) . '</p>',
            ]);
            foreach ($texts as $path => [$label, $type]) {
                cloudground_elementor_text_control($this, (string) $path, (string) $label, (string) $type);
            }
            $this->end_controls_section();
        }

        if ($controls === [] && $texts === []) {
            // A widget with nothing to choose still gets a panel, so nobody wonders whether
            // the click registered.
            $this->start_controls_section('cloudground_about', ['label' => __('Section', 'cloudground')]);
            $this->add_control('cloudground_note', [
                'type' => \Elementor\Controls_Manager::RAW_HTML,
                'raw' => '<p>' . esc_html((string) ($spec['note'] ?? __('Nothing to set here: the content comes from the site.', 'cloudground'))) . '</p>',
            ]);
            $this->end_controls_section();
        }
    }

    /**
     * The part exactly as the theme prints it, with this widget's words over the site's:
     * they sit on the theme's text stack while the part renders (cloudground_t()).
     */
    protected function render(): void
    {
        $settings = $this->get_settings_for_display();
        $words = cloudground_elementor_texts((array) ($this->spec()['texts'] ?? []), $settings);
        $push = function_exists('cloudground_text_push') && $words !== [];
        if ($push) {
            cloudground_text_push($words);
        }
        get_template_part('template-parts/' . (string) ($this->spec()['part'] ?? static::SECTION), null, $settings);
        if ($push) {
            cloudground_text_pop();
        }
    }

    /**
     * Nothing renders in the browser. Elementor can draw a widget client-side from a JS
     * template, which would be a second implementation of every part in another language.
     * There is not one, so the editor asks the server — slower to drag, and correct.
     */
    protected function content_template(): void
    {
    }
}

class Cloudground_Section_CacheScope extends Cloudground_Section
{
    protected const SECTION = 'cache-scope';
}

class Cloudground_Section_Measures extends Cloudground_Section
{
    protected const SECTION = 'measures';
}

class Cloudground_Section_InstallTerminal extends Cloudground_Section
{
    protected const SECTION = 'install-terminal';
}
