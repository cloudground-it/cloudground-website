<?php

/**
 * Italian: the default language, and so the complete tree.
 *
 * Every other language's file `array_replace`s over this one. The replace is **shallow**:
 * a top-level key a translation overrides replaces its whole subtree, so a translated
 * section reproduces every key of its Italian one.
 *
 * Closures take the facts (`cloudground_who()`) and interpolate with braces: `{$who['name']}`.
 *
 * Words in square brackets ([VERSIONE], [URL INSTALLER], [REQ/S]…) are placeholders the
 * owner fills when the fact exists. They are printed as they are, never guessed.
 */

declare(strict_types=1);

return [
    'htmlLang' => 'it-IT',
    'ogLocale' => 'it_IT',
    'endonym' => 'Italiano',

    'meta' => [
        'title' => fn (array $who): string => "{$who['name']} — hosting che si misura",
        'description' => 'Un comando installa il pannello sul tuo server. Ogni sito nasce con la sua cache, il suo PHP, il suo certificato e backup che vengono ripristinati per prova dopo ogni copia.',
    ],

    'nav' => [
        'skip' => 'Vai al contenuto',
        'menu' => 'Principale',
        'languages' => 'Lingua',
        'home' => fn (array $who): string => "{$who['name']}, pagina iniziale",
        'repository' => 'GitHub',
        'install' => 'Installa',
    ],

    // The strip above the navigation: the state of the project, in a line.
    'status' => [
        'label' => 'Stato del progetto',
        'release' => 'Release [VERSIONE] disponibile',
        'systems' => 'Ubuntu 24.04 · 26.04',
        'license' => 'Licenza AGPL-3.0',
        'price' => 'Nessun abbonamento',
    ],

    // The hero's oscilloscope (plugin widget «Cache oscilloscope»). Each is also a field.
    'scope' => [
        'caption' => 'Simulazione · un sito WordPress sotto traffico',
        'toggle' => 'Cache',
        'on' => 'accesa',
        'off' => 'spenta',
        'axis' => 'Tempo di risposta',
        'stable' => 'Stabile',
        'strained' => 'Sotto sforzo',
        'describeOn' => 'Con la cache il tempo di risposta resta basso e piatto',
        'describeOff' => 'Senza cache il tempo di risposta sale e oscilla',
        'cache' => 'Cache',
        'cacheOn' => 'HIT',
        'cacheOff' => 'MISS',
        'php' => 'PHP del sito',
        'phpOn' => 'a riposo',
        'phpOff' => 'al limite',
        'db' => 'Database',
        'dbOn' => 'a riposo',
        'dbOff' => 'occupato',
        'note' => 'Illustrazione, non una misura. I numeri veri sono nella sezione 03.',
    ],

    // The measurements (plugin widget «Measurements table»). A row is five values
    // separated by « | »: scenario | note | requests/s | p99 | errors.
    'measures' => [
        'caption' => 'Risultati delle misure, per scenario',
        'scenario' => 'Scenario',
        'rps' => 'Richieste / s',
        'p99' => 'p99',
        'errors' => 'Errori',
        'rows' => [
            'Pagina in cache | 500 connessioni | [REQ/S] | [P99] | [ERRORI]',
            'Pagina in cache | 1 connessione | [REQ/S] | [P99] | [ERRORI]',
            'File statico | 1 KB | [REQ/S] | [P99] | [ERRORI]',
            'Navigazione reale | mix di pagine | [REQ/S] | [P99] | [ERRORI]',
            'Senza cache | PHP + database | [REQ/S] | [P99] | [ERRORI]',
        ],
        'notes' => [
            'Server: [CPU · RAM · SISTEMA]',
            'Carico: wrk dalla stessa regione',
            'Ultima misura: [DATA]',
        ],
    ],

    // The installer's terminal (plugin widget «Install terminal»).
    'terminal' => [
        'user' => 'root@server',
        'shell' => 'bash',
        'copy' => 'Copia',
        'copyWhat' => 'il comando',
        'copied' => 'Copiato',
        'command' => 'curl -fsSL [URL INSTALLER] | bash',
        'output' => [
            '[ok] server controllato: sistema, memoria, porte',
            '[ok] nginx · PHP · MariaDB · Redis installati',
            '[ok] firma della release verificata',
            '[ok] pannello attivo su https://[IP]:8443',
            '→ crea l’amministratore: https://[IP]:8443/setup/…',
        ],
    ],

    'footer' => [
        'label' => 'Piè di pagina',
        'tagline' => 'Hosting che si misura',
        'product' => 'Prodotto',
        'features' => 'Funzioni',
        'measures' => 'Misure',
        'install' => 'Installazione',
        'resources' => 'Risorse',
        'docs' => 'Documentazione',
        'releases' => 'Novità',
        'repository' => 'GitHub',
        'legal' => 'Legale',
        'license' => 'Licenza',
        'privacy' => 'Privacy',
    ],

    'notFound' => [
        'title' => 'Questa pagina non c’è',
        'back' => 'Torna alla home',
    ],
];
