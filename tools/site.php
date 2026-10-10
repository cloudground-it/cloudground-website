<?php

/**
 * The CloudGround site's pages, built the way every page of a Bottega site is built.
 *
 *   bin/wp eval-file /var/www/html/tools/site.php [rebuild]
 *
 * Italian and English, linked by Polylang, the same slug in both:
 * - the home (`/`, `/en/`): native Elementor widgets in containers that carry the theme's
 *   classes (assets/src/css/components/home.css), and the plugin's three widgets where
 *   there is behaviour or data — the cache oscilloscope, the measurements table, the
 *   install terminal;
 * - the licence (`/licenza/`) and the privacy notice (`/privacy/`): placeholders, clearly
 *   marked, until the owner writes them;
 * - the facts (Settings → CloudGround), the sharing card, a menu per language.
 *
 * The words are the approved design's (design/reference/home.dc.html) and their English
 * translation. Placeholders in square brackets are printed as they are: they are facts
 * nobody has yet, and nobody guesses them.
 *
 * Idempotent: a page already built is left alone unless `rebuild`; the menus, the facts
 * this script owns and the options are set to the same values every run.
 */

if (!defined('ABSPATH')) {
    exit(1);
}

$rebuild = in_array('rebuild', (array) ($args ?? []), true);

/* The facts: only the ones the owner gave ------------------------------------------------ */

$facts = get_option('cloudground_facts');
// A fresh option, or the template demo's invented business (an address and a telephone
// this project never had): replaced whole, so none of the demo's facts survives.
if (!is_array($facts) || !array_key_exists('repository', $facts)) {
    update_option('cloudground_facts', [
        'name' => 'CloudGround',
        'docs' => 'https://docs.cloudground.it',
        'repository' => 'https://github.com/cloudground-it/cloudground',
    ]);
    echo "✓ facts\n";
}

/* The pieces an Elementor document is made of --------------------------------------------- */

$id = static fn (): string => substr(bin2hex(random_bytes(4)), 0, 7);
$box = static function (string $classes, array $children, string $tag = 'div', string $anchor = '') use ($id): array {
    $settings = ['content_width' => 'full', 'html_tag' => $tag, 'css_classes' => $classes,
        'padding' => ['unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => true],
        'margin' => ['unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => true]];
    if ($anchor !== '') {
        $settings['_element_id'] = $anchor;
    }

    return ['id' => $id(), 'elType' => 'container', 'isInner' => false, 'settings' => $settings, 'elements' => $children];
};
$widget = static fn (string $type, array $settings, string $classes = '') => [
    'id' => $id(), 'elType' => 'widget', 'widgetType' => $type,
    'settings' => $classes !== '' ? $settings + ['_css_classes' => $classes] : $settings, 'elements' => [],
];
$heading = static fn (string $title, string $tag, string $classes) => $widget('heading', ['title' => $title, 'header_size' => $tag], $classes);
$text = static fn (string $html, string $classes) => $widget('text-editor', ['editor' => $html], $classes);
// A link to a fact (the docs, the code) goes through the plugin's dynamic tag, so the
// address is kept in one place: Settings → CloudGround.
$button = static function (string $label, string $classes, string $url = '', string $fact = '') use ($widget, $id): array {
    $settings = ['text' => $label, 'link' => ['url' => $url, 'is_external' => '', 'nofollow' => '']];
    if ($fact !== '') {
        $settings['__dynamic__'] = ['link' => sprintf('[elementor-tag id="%s" name="cloudground-link" settings="%s"]', $id(), rawurlencode((string) wp_json_encode(['target' => $fact])))];
    }

    return $widget('button', $settings, $classes);
};

/* The words, per language ------------------------------------------------------------------ */

$words = [
    'it' => [
        'home' => 'Home',
        'hero' => [
            'eyebrow' => '001 / Pannello di hosting sui tuoi server',
            'title' => 'Hosting<br>che si <em>misura.</em>',
            'lede' => 'Un comando installa il pannello sul tuo server. Ogni sito nasce con la sua cache, il suo PHP, il suo certificato e backup che vengono ripristinati per prova dopo ogni copia.',
            'install' => 'Installa in un comando',
            'docs' => 'Leggi le docs',
            'tagline' => 'Gratis · Codice consultabile · Sui tuoi server',
        ],
        'features' => [
            'eyebrow' => '01 / Funzioni',
            'title' => 'Il controllo che un <em>tutorial</em> non ti dà.',
            'lede' => 'Otto cose che di solito si configurano a mano, una sera alla volta. Qui sono accese dal primo sito.',
            'cells' => [
                ['Velocità', 'Cache di pagina mirata', 'nginx serve le pagine senza toccare PHP. Quando cambi un articolo o un prezzo, si svuotano solo le pagine che lo mostrano.'],
                ['Isolamento', 'Un PHP per ogni sito', 'Utente, processo e memoria separati. Un sito compromesso non vede gli altri.'],
                ['Backup', 'Ripristino già provato', 'Ogni copia va fuori dal server e viene ripristinata per prova subito dopo.'],
                ['Staging', 'Prova prima, misura poi', 'Una copia protetta da password. Un rilascio più lento della produzione viene fermato.'],
                ['HTTPS', 'Certificati che si rinnovano', 'Let’s Encrypt, wildcard compresi, o i tuoi certificati.'],
                ['File', 'Un editor vero nel browser', 'Riconosce il linguaggio, carica trascinando, lascia scaricare i binari.'],
                ['Database', 'Il database, dentro il pannello', 'Tabelle, dati e SQL con i permessi del solo sito, senza strumenti esterni.'],
                ['Accesso', 'Ruoli e due fattori', 'Amministratori, operatori sui loro siti, sola lettura. 2FA obbligatoria se vuoi.'],
            ],
        ],
        'ownership' => [
            'eyebrow' => '02 / Proprietà',
            'title' => 'Smetti di <em>affittare</em> il tuo server.',
            'cells' => [
                ['A', 'Codice consultabile', 'Leggi ogni riga che gira come root sul tuo server. Modificala, se serve.'],
                ['B', 'Tuo per sempre', 'Nessuna licenza da rinnovare, nessun costo per server. Se il progetto si fermasse, il pannello continuerebbe a funzionare.'],
                ['C', 'Nessun intermediario', 'Il pannello parla con il tuo server e con nient’altro. I backup vanno dove scegli tu.'],
            ],
        ],
        'measures' => [
            'eyebrow' => '03 / Misure',
            'title' => 'Misurato, non <em>promesso.</em>',
            'lede' => 'Ogni impostazione è stata provata da sola, contro la sua assenza. Quelle che non vincevano non ci sono. Metodo e dati grezzi sono pubblici.',
        ],
        'install' => [
            'eyebrow' => '04 / Installazione',
            'title' => 'Un comando. <em>Davvero.</em>',
            'steps' => [
                'Un server Ubuntu appena creato, accesso root.',
                'Il comando qui accanto. Controlla tutto prima di toccare qualcosa.',
                'Apri il link monouso, crea l’amministratore, crea il primo sito.',
            ],
        ],
        'closing' => [
            'eyebrow' => 'Gratis per sempre · Nessun abbonamento',
            'title' => 'Accendi il<br>tuo <em>server.</em>',
            'install' => 'Installa CloudGround',
            'code' => 'Il codice su GitHub',
        ],
        'menu' => [['Funzioni', 'funzioni'], ['Perché', 'proprieta'], ['Misure', 'misure'], ['Installazione', 'installa'], ['Docs', 'docs']],
        'license' => ['Licenza', 'Il testo di questa pagina deve ancora essere scritto dal proprietario del sito: quale licenza copre il codice di CloudGround e che cosa permette. Fino ad allora questa pagina non dice nulla di vincolante.'],
        'privacy' => ['Privacy', 'L’informativa privacy di questo sito deve ancora essere scritta dal proprietario, a partire da quello che il codice del sito fa davvero. Fino ad allora questa pagina non dice nulla di vincolante.'],
        'placeholder' => 'Da completare',
        'ogAlt' => 'CloudGround — Hosting che si misura',
    ],
    'en' => [
        'home' => 'Home',
        'hero' => [
            'eyebrow' => '001 / Self-hosted hosting panel',
            'title' => 'Hosting<br>that’s <em>measured.</em>',
            'lede' => 'One command installs the panel on your server. Every site starts with its own cache, its own PHP, its own certificate, and backups that are test-restored after every copy.',
            'install' => 'Install in one command',
            'docs' => 'Read the docs',
            'tagline' => 'Free · Source-available · On your servers',
        ],
        'features' => [
            'eyebrow' => '01 / Features',
            'title' => 'The control a <em>tutorial</em> won’t give you.',
            'lede' => 'Eight things that are usually set up by hand, one evening at a time. Here they are on from the very first site.',
            'cells' => [
                ['Speed', 'Targeted page cache', 'nginx serves pages without touching PHP. When you change a post or a price, only the pages that show it are cleared.'],
                ['Isolation', 'One PHP per site', 'Separate user, process and memory. A compromised site cannot see the others.'],
                ['Backup', 'Restores already tested', 'Every copy leaves the server and is test-restored straight after.'],
                ['Staging', 'Try first, then measure', 'A password-protected copy. A release slower than production is stopped.'],
                ['HTTPS', 'Certificates that renew themselves', 'Let’s Encrypt, wildcards included, or your own certificates.'],
                ['Files', 'A real editor in the browser', 'It recognises the language, uploads by drag and drop, and lets you download binaries.'],
                ['Database', 'The database, inside the panel', 'Tables, data and SQL with the site’s own permissions only, no external tools.'],
                ['Access', 'Roles and two factors', 'Administrators, operators on their own sites, read-only. 2FA mandatory if you want it.'],
            ],
        ],
        'ownership' => [
            'eyebrow' => '02 / Ownership',
            'title' => 'Stop <em>renting</em> your server.',
            'cells' => [
                ['A', 'Source-available', 'Read every line that runs as root on your server. Change it, if you need to.'],
                ['B', 'Yours for good', 'No licence to renew, no cost per server. If the project stopped, the panel would keep working.'],
                ['C', 'No middleman', 'The panel talks to your server and nothing else. Backups go wherever you choose.'],
            ],
        ],
        'measures' => [
            'eyebrow' => '03 / Measurements',
            'title' => 'Measured, not <em>promised.</em>',
            'lede' => 'Every setting was tested on its own, against its absence. The ones that did not win are not there. The method and the raw data are public.',
        ],
        'install' => [
            'eyebrow' => '04 / Installation',
            'title' => 'One command. <em>Really.</em>',
            'steps' => [
                'A freshly created Ubuntu server, with root access.',
                'The command next to this. It checks everything before it touches anything.',
                'Open the one-time link, create the administrator, create the first site.',
            ],
        ],
        'closing' => [
            'eyebrow' => 'Free for good · No subscription',
            'title' => 'Switch on<br>your <em>server.</em>',
            'install' => 'Install CloudGround',
            'code' => 'The code on GitHub',
        ],
        'menu' => [['Features', 'funzioni'], ['Why', 'proprieta'], ['Measurements', 'misure'], ['Installation', 'installa'], ['Docs', 'docs']],
        'license' => ['Licence', 'The text of this page has yet to be written by the site’s owner: which licence covers CloudGround’s code, and what it allows. Until then this page says nothing binding.'],
        'privacy' => ['Privacy', 'This site’s privacy notice has yet to be written by its owner, starting from what the site’s code actually does. Until then this page says nothing binding.'],
        'placeholder' => 'To be completed',
        'ogAlt' => 'CloudGround — Hosting that’s measured',
    ],
];

/* The home, as a tree of containers and widgets ------------------------------------------- */

// A section's head: the label and the headline on the left, a sentence on the right. The
// measurements' is tighter below, with a wider sentence and an orange accent word.
$sectionHead = static function (string $eyebrow, string $title, string $lede, bool $tight = false) use ($box, $heading, $text): array {
    return $box('section-head' . ($tight ? ' section-head--tight' : ''), [
        $box('section-title', [
            $heading($eyebrow, 'p', 'hud'),
            $heading($title, 'h2', 'display' . ($tight ? ' display--warm' : '')),
        ]),
        $text('<p>' . $lede . '</p>', 'section-lede' . ($tight ? ' section-lede--wide' : '')),
    ]);
};

$marquee = [
    ['nginx', ''], ['PHP 8.5', 'blue'], ['MariaDB', ''], ['Redis', 'warm'], ['restic', ''],
    ['Let’s Encrypt', 'blue'], ['HTTP/3', ''], ['WordPress', 'warm'], ['Laravel', ''], ['systemd', 'blue'],
];

$layout = static function (array $w) use ($box, $heading, $text, $widget, $button, $sectionHead, $marquee): array {
    $h = $w['hero'];
    $hero = $box('hero frame', [
        $box('hero-copy', [
            $heading($h['eyebrow'], 'p', 'hud'),
            $heading($h['title'], 'h1', 'display display--hero'),
            $text('<p>' . $h['lede'] . '</p>', 'lede'),
            $box('actions', [
                $button($h['install'], 'cta cta--arrow', '#installa'),
                $button($h['docs'], 'cta cta--line', '', 'docs'),
            ]),
            $heading($h['tagline'], 'p', 'hud hud--faint'),
        ]),
        $widget('cloudground-cache-scope', [], 'hero-panel'),
    ], 'section', 'top');

    $words = array_map(static fn (array $m): array => $heading($m[0], 'span', $m[1] === '' ? 'marquee-word' : 'marquee-word marquee-word--serif marquee-word--' . $m[1]), $marquee);
    $technologies = $box('marquee', [$box('marquee-track', $words)]);

    $f = $w['features'];
    $cells = [];
    foreach ($f['cells'] as $i => [$tag, $title, $body]) {
        $cells[] = $box('cell cell--inv', [
            $box('cell-meta', [
                $heading($tag, 'span', 'hud cell-tag'),
                $heading(str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT), 'span', 'hud cell-num'),
            ]),
            $heading($title, 'h3', 'cell-title'),
            $text('<p>' . $body . '</p>', 'cell-text'),
        ], 'article');
    }
    $features = $box('frame', [
        $sectionHead($f['eyebrow'], $f['title'], $f['lede']),
        $box('cells', $cells),
    ], 'section', 'funzioni');

    $o = $w['ownership'];
    $ownCells = array_map(static fn (array $c): array => $box('cell cell--dark', [
        $heading($c[0], 'span', 'hud cell-letter'),
        $heading($c[1], 'h3', 'cell-title'),
        $text('<p>' . $c[2] . '</p>', 'cell-text'),
    ]), $o['cells']);
    $ownership = $box('band band--dark on-dark', [$box('frame', [
        $box('section-head section-head--stack', [
            $heading($o['eyebrow'], 'p', 'hud'),
            $heading($o['title'], 'h2', 'display display--big display--warm'),
        ]),
        $box('cells cells--dark', $ownCells),
    ])], 'section', 'proprieta');

    $m = $w['measures'];
    $measures = $box('frame', [
        $sectionHead($m['eyebrow'], $m['title'], $m['lede'], true),
        $widget('cloudground-measures', []),
    ], 'section', 'misure');

    $in = $w['install'];
    $install = $box('band band--alt', [$box('frame install', [
        $box('install-copy', [
            $heading($in['eyebrow'], 'p', 'hud'),
            $heading($in['title'], 'h2', 'display display--install'),
            $text('<ol>' . implode('', array_map(static fn (string $s): string => "<li>{$s}</li>", $in['steps'])) . '</ol>', 'steps'),
        ]),
        $widget('cloudground-install-terminal', [], 'install-panel'),
    ])], 'section', 'installa');

    $c = $w['closing'];
    $closing = $box('band band--top', [$box('frame closing', [
        $heading($c['eyebrow'], 'p', 'hud'),
        $heading($c['title'], 'h2', 'display display--closing'),
        $box('actions', [
            $button($c['install'], 'cta cta--arrow cta--large', '#installa'),
            $button($c['code'], 'cta cta--line cta--large', '', 'repository'),
        ]),
    ])], 'section');

    return [$hero, $technologies, $features, $ownership, $measures, $install, $closing];
};

/* The pages -------------------------------------------------------------------------------- */

// WordPress's own sample content, which no site keeps.
foreach ([get_page_by_path('sample-page'), get_page_by_path('hello-world', OBJECT, 'post')] as $sample) {
    if ($sample) {
        wp_delete_post($sample->ID, true);
    }
}

$page = static function (string $slug, string $title, string $lang): int {
    // By slug, then by language: under WP-CLI Polylang's query filter does not run, and a
    // `lang` argument to get_posts() is silently ignored.
    foreach (get_posts(['post_type' => 'page', 'name' => $slug, 'lang' => '', 'numberposts' => -1, 'fields' => 'ids', 'post_status' => 'any']) as $found) {
        if (!function_exists('pll_get_post_language') || pll_get_post_language((int) $found) === $lang) {
            if (get_the_title((int) $found) !== $title) {
                wp_update_post(['ID' => (int) $found, 'post_title' => $title]);
            }

            return (int) $found;
        }
    }
    $new = (int) wp_insert_post(['post_type' => 'page', 'post_status' => 'publish', 'post_title' => $title, 'post_name' => $slug]);
    if (function_exists('pll_set_post_language')) {
        pll_set_post_language($new, $lang);
        // Saved again now that it has a language, so the plugin keeps the same slug in every
        // language (inc/polylang.php): WordPress gave it `licenza-2` at insert time.
        wp_update_post(['ID' => $new, 'post_name' => $slug]);
    }

    return $new;
};

/** A page the owner has to write: one paragraph, marked as a placeholder. */
$placeholder = static fn (string $label, string $sentence): string => sprintf('<p class="placeholder"><strong>[%s]</strong> %s</p>', esc_html($label), esc_html($sentence));
// What the template's demo wrote in its privacy page: replaced, it is not this site's.
$templateSentence = 'Scrivila dal codice';

$homes = $licenses = $privacies = [];
foreach ($words as $lang => $w) {
    $homes[$lang] = $page('home', $w['home'], $lang);
    $licenses[$lang] = $page('licenza', $w['license'][0], $lang);
    $privacies[$lang] = $page('privacy', $w['privacy'][0], $lang);

    if ($rebuild || get_post_meta($homes[$lang], '_cloudground_built', true) !== 'site') {
        update_post_meta($homes[$lang], '_elementor_data', wp_slash((string) wp_json_encode($layout($w))));
        update_post_meta($homes[$lang], '_elementor_edit_mode', 'builder');
        update_post_meta($homes[$lang], '_elementor_template_type', 'wp-page');
        update_post_meta($homes[$lang], '_elementor_version', defined('ELEMENTOR_VERSION') ? ELEMENTOR_VERSION : '3.0.0');
        update_post_meta($homes[$lang], '_cloudground_built', 'site');
        echo "✓ home ($lang)\n";
    }

    foreach ([[$licenses[$lang], $w['license'][1]], [$privacies[$lang], $w['privacy'][1]]] as [$target, $sentence]) {
        $content = (string) get_post_field('post_content', $target);
        if ($content === '' || str_contains($content, $templateSentence) || str_contains($content, 'This page describes what the site does')) {
            wp_update_post(['ID' => $target, 'post_content' => $placeholder($w['placeholder'], $sentence)]);
            echo '✓ placeholder: ' . get_post_field('post_name', $target) . " ($lang)\n";
        }
    }
}
if (function_exists('pll_save_post_translations')) {
    pll_save_post_translations($homes);
    pll_save_post_translations($licenses);
    pll_save_post_translations($privacies);
}
update_option('show_on_front', 'page');
update_option('page_on_front', $homes['it']);
update_option('wp_page_for_privacy_policy', $privacies['it']);
update_option('cloudground_page_for_license', $licenses['it']);

/* The sharing card: the front page's featured image (inc/head.php reads it) ---------------- */

$card = get_posts(['post_type' => 'attachment', 'meta_key' => '_cloudground_source', 'meta_value' => 'og', 'numberposts' => 1, 'fields' => 'ids', 'lang' => '', 'post_status' => 'any']);
$cardId = (int) ($card[0] ?? 0);
if ($cardId === 0) {
    require_once ABSPATH . 'wp-admin/includes/image.php';
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';
    $tmp = wp_tempnam('cloudground-og.png');
    copy(__DIR__ . '/media/og.png', $tmp);
    $cardId = (int) media_handle_sideload(['name' => 'cloudground-og.png', 'tmp_name' => $tmp], 0, 'CloudGround');
    if ($cardId > 0) {
        update_post_meta($cardId, '_cloudground_source', 'og');
        echo "✓ sharing card\n";
    }
}
if ($cardId > 0) {
    update_post_meta($cardId, '_wp_attachment_image_alt', $words['it']['ogAlt']);
    foreach ($words as $lang => $w) {
        update_post_meta($cardId, '_cloudground_alt_' . $lang, $w['ogAlt']);
        set_post_thumbnail($homes[$lang], $cardId);
    }
}

/* A menu per language, in the «Primary» position: the home's sections and the docs ---------- */

$docs = (string) (get_option('cloudground_facts')['docs'] ?? '');
$menus = [];
foreach ($words as $lang => $w) {
    $name = 'Primary (' . strtoupper($lang) . ')';
    $menu = wp_get_nav_menu_object($name);
    $menuId = $menu ? (int) $menu->term_id : (int) wp_create_nav_menu($name);
    // Rebuilt every run: the items are this script's, and a menu half-updated is worse
    // than one written again.
    foreach ((array) wp_get_nav_menu_items($menuId) as $item) {
        wp_delete_post((int) $item->ID, true);
    }
    // Relative, so the menu survives a move to the production address unchanged.
    $home = wp_make_link_relative(home_url($lang === 'it' ? '/' : "/{$lang}/"));
    foreach ($w['menu'] as $i => [$label, $anchor]) {
        $url = $anchor === 'docs' ? $docs : $home . '#' . $anchor;
        if ($url === '') {
            continue;
        }
        wp_update_nav_menu_item($menuId, 0, ['menu-item-title' => $label, 'menu-item-url' => $url, 'menu-item-type' => 'custom', 'menu-item-status' => 'publish', 'menu-item-position' => $i + 1]);
    }
    $menus[$lang] = $menuId;
}
set_theme_mod('nav_menu_locations', ['primary' => $menus['it']] + (array) get_theme_mod('nav_menu_locations', []));
if (function_exists('PLL')) {
    $options = (array) get_option('polylang', []);
    $options['nav_menus'][get_stylesheet()]['primary'] = $menus;
    update_option('polylang', $options);
}

if (function_exists('cloudground_apply_kit')) {
    cloudground_apply_kit();
}
if (class_exists('\Elementor\Plugin')) {
    \Elementor\Plugin::$instance->files_manager->clear_cache();
}
flush_rewrite_rules();
echo "Site ready.\n";
