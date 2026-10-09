<?php

/**
 * The widgets, as data: what each one prints and the few things it lets the editor choose.
 *
 * Deliberately few. Every control here answers "which content", never "how should it
 * look" — the second question is the stylesheet's, and a widget that offered it would be a
 * second way to style something already styled. A section that is only words and pictures
 * is not here at all: it is built from Elementor's own widgets (docs/guidelines/04-elementor.md).
 *
 * A spec:
 *   'title'    the name in the panel
 *   'icon'     an eicon
 *   'part'     the theme's template part it prints (template-parts/{part}.php)
 *   'entry'    the theme's Vite entry a page holding it needs (inc/assets.php)
 *   'keywords' what the panel search finds it by
 *   'controls' Elementor controls, name => definition; `options` may be a callable. Control
 *              types as strings ('switcher', 'select', 'media'…), not Controls_Manager
 *              constants: the theme reads the spec on every page, Elementor loaded or not
 *   'texts'    tree path => [label, type]: every sentence the part prints, as a field
 *              t = one line, no markup · h = one line, <em>/<strong>/<br> allowed
 *              a = a few lines · p = paragraphs, a blank line between them
 *   'note'     what the Texts panel says
 *
 * Adding a widget: a spec here, a one-line class in widgets.php, the part in the theme,
 * the words in the theme's inc/content/*.php. The .claude/skills/elementor-widget skill
 * walks through it.
 *
 * @return array<string, array<string, mixed>>
 */
function cloudground_elementor_sections(): array
{
    return [
        // The hero's oscilloscope: a switch with state (the cache on or off) and the
        // readings that follow it. Behaviour, so a widget; every word is a field.
        'cache-scope' => [
            'title' => __('Cache oscilloscope', 'cloudground'),
            'icon' => 'eicon-dual-button',
            'part' => 'cache-scope',
            'entry' => 'site',
            'keywords' => ['cache', 'scope', 'toggle', 'hero'],
            'controls' => [
                'start_on' => [
                    'label' => __('Starts with the cache on', 'cloudground'),
                    'type' => 'switcher',
                    'return_value' => 'yes',
                    'default' => 'yes',
                ],
            ],
            'texts' => [
                'scope.caption' => [__('Caption above', 'cloudground'), 't'],
                'scope.toggle' => [__('Switch: what it switches', 'cloudground'), 't'],
                'scope.on' => [__('Switch: on', 'cloudground'), 't'],
                'scope.off' => [__('Switch: off', 'cloudground'), 't'],
                'scope.axis' => [__('Screen: what it shows', 'cloudground'), 't'],
                'scope.stable' => [__('Screen: state with the cache', 'cloudground'), 't'],
                'scope.strained' => [__('Screen: state without', 'cloudground'), 't'],
                'scope.describeOn' => [__('Screen described, with the cache (screen readers)', 'cloudground'), 't'],
                'scope.describeOff' => [__('Screen described, without (screen readers)', 'cloudground'), 't'],
                'scope.cache' => [__('Reading 1: name', 'cloudground'), 't'],
                'scope.cacheOn' => [__('Reading 1: with the cache', 'cloudground'), 't'],
                'scope.cacheOff' => [__('Reading 1: without', 'cloudground'), 't'],
                'scope.php' => [__('Reading 2: name', 'cloudground'), 't'],
                'scope.phpOn' => [__('Reading 2: with the cache', 'cloudground'), 't'],
                'scope.phpOff' => [__('Reading 2: without', 'cloudground'), 't'],
                'scope.db' => [__('Reading 3: name', 'cloudground'), 't'],
                'scope.dbOn' => [__('Reading 3: with the cache', 'cloudground'), 't'],
                'scope.dbOff' => [__('Reading 3: without', 'cloudground'), 't'],
                'scope.note' => [__('Note below', 'cloudground'), 'a'],
            ],
        ],

        // The measurements: a table, which no native widget draws, holding numbers that
        // change with every benchmark run. Data, so a widget.
        'measures' => [
            'title' => __('Measurements table', 'cloudground'),
            'icon' => 'eicon-table',
            'part' => 'measures',
            'entry' => 'site',
            'keywords' => ['benchmark', 'table', 'measures', 'numbers'],
            'controls' => [],
            'texts' => [
                'measures.caption' => [__('What the table is (screen readers)', 'cloudground'), 't'],
                'measures.scenario' => [__('Column 1', 'cloudground'), 't'],
                'measures.rps' => [__('Column 2', 'cloudground'), 't'],
                'measures.p99' => [__('Column 3', 'cloudground'), 't'],
                'measures.errors' => [__('Column 4', 'cloudground'), 't'],
                'measures.rows' => [__('Rows', 'cloudground'), 'p'],
                'measures.notes' => [__('Notes under the table', 'cloudground'), 'p'],
            ],
            'note' => __('Rows: one per paragraph, five values separated by « | »: scenario | note | requests per second | p99 | errors. Notes: one per paragraph. Only numbers that were measured.', 'cloudground'),
        ],

        // The installer's terminal: the command, a button that copies it, and the lines
        // the installer prints, played as the terminal comes into view. Behaviour.
        'install-terminal' => [
            'title' => __('Install terminal', 'cloudground'),
            'icon' => 'eicon-code',
            'part' => 'install-terminal',
            'entry' => 'site',
            'keywords' => ['install', 'terminal', 'command', 'copy'],
            'controls' => [],
            'texts' => [
                'terminal.user' => [__('Tab: where', 'cloudground'), 't'],
                'terminal.shell' => [__('Tab: shell', 'cloudground'), 't'],
                'terminal.copy' => [__('Button', 'cloudground'), 't'],
                'terminal.copyWhat' => [__('Button, the rest for screen readers', 'cloudground'), 't'],
                'terminal.copied' => [__('Button, once copied', 'cloudground'), 't'],
                'terminal.command' => [__('The command (what is copied)', 'cloudground'), 't'],
                'terminal.output' => [__('What the installer prints', 'cloudground'), 'p'],
            ],
            'note' => __('One line of output per paragraph; the last one is drawn brighter. The command is copied exactly as typed.', 'cloudground'),
        ],
    ];
}

/**
 * The widget classes, in the order of the panel: one per spec, named after its key
 * (`contact` → Cloudground_Section_Contact, `opening-hours` → Cloudground_Section_OpeningHours).
 *
 * @return list<class-string>
 */
function cloudground_elementor_widgets(): array
{
    return array_map(
        static fn (string $key): string => 'Cloudground_Section_' . str_replace(' ', '', ucwords(str_replace('-', ' ', $key))),
        array_keys(cloudground_elementor_sections()),
    );
}

/* The words, as fields ------------------------------------------------------------------ */

/** A tree path as a control's name: `contact.heading` → `t__contact__heading`. */
function cloudground_elementor_text_key(string $path): string
{
    return 't__' . str_replace('.', '__', $path);
}

/**
 * The language a field's words start from: the document being edited in Elementor, or
 * the page being read.
 */
function cloudground_elementor_locale(): string
{
    // phpcs:disable WordPress.Security.NonceVerification -- which document is open; nothing is changed.
    $id = (int) ($_GET['post'] ?? $_GET['elementor-preview'] ?? $_POST['editor_post_id'] ?? $_POST['initial_document_id'] ?? 0);
    // phpcs:enable WordPress.Security.NonceVerification
    // A Theme Builder document says its language itself (languages.php).
    $own = $id > 0 ? (string) get_post_meta($id, '_cloudground_lang', true) : '';
    if ($own !== '') {
        return $own;
    }
    if ($id > 0 && function_exists('pll_get_post_language')) {
        $lang = (string) pll_get_post_language($id);
        if ($lang !== '') {
            return $lang;
        }
    }

    return function_exists('cloudground_locale') ? cloudground_locale() : '';
}

/** The site's own words for a path, as the field shows them. */
function cloudground_elementor_text_default(string $path): string
{
    if (!function_exists('cloudground_tree')) {
        return '';
    }
    $node = cloudground_tree(cloudground_elementor_locale() ?: null);
    foreach (explode('.', $path) as $key) {
        if (!is_array($node) || !array_key_exists($key, $node)) {
            return '';
        }
        $node = $node[$key];
    }
    if ($node instanceof Closure) {
        $node = $node(cloudground_who());
    }
    if (is_array($node) && array_is_list($node)) {
        return implode("\n\n", array_map('strval', $node));
    }

    return is_string($node) ? $node : '';
}

/** One field on a widget. */
function cloudground_elementor_text_control(\Elementor\Widget_Base $widget, string $path, string $label, string $type): void
{
    $widget->add_control(cloudground_elementor_text_key($path), [
        'label' => $label,
        'type' => in_array($type, ['t', 'h'], true) ? \Elementor\Controls_Manager::TEXT : \Elementor\Controls_Manager::TEXTAREA,
        'rows' => $type === 'p' ? 8 : 3,
        'default' => cloudground_elementor_text_default($path),
        'label_block' => true,
        'description' => $type === 'p' ? __('A blank line between one paragraph and the next.', 'cloudground') : '',
    ]);
}

/**
 * A widget's fields as tree paths and values, cleaned. An empty field is left out, so the
 * tree answers for it in the page's language.
 *
 * @return array<string, string>
 */
function cloudground_elementor_texts(array $texts, array $settings): array
{
    $inline = ['em' => [], 'strong' => [], 'br' => [], 'a' => ['href' => true]];
    $out = [];
    foreach ($texts as $path => [$label, $type]) {
        $value = (string) ($settings[cloudground_elementor_text_key((string) $path)] ?? '');
        if (trim($value) !== '') {
            $out[(string) $path] = wp_kses($value, $type === 't' ? [] : $inline);
        }
    }

    return $out;
}
