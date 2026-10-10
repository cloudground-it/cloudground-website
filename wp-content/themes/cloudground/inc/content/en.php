<?php

/**
 * English, over the Italian tree.
 *
 * array_replace is shallow: each top-level key overridden here replaces the whole Italian
 * subtree, so every key of a section is reproduced. Placeholders in square brackets stay
 * exactly as they are in Italian ([VERSIONE], [URL INSTALLER], [REQ/S]…): they are the
 * owner's to fill, in both languages at once.
 */

declare(strict_types=1);

$base = require __DIR__ . '/it.php';

return array_replace($base, [
    'htmlLang' => 'en',
    'ogLocale' => 'en_GB',
    'endonym' => 'English',

    'meta' => [
        'title' => fn (array $who): string => "{$who['name']} — hosting that’s measured",
        'description' => 'One command installs the panel on your server. Every site starts with its own cache, its own PHP, its own certificate, and backups that are test-restored after every copy.',
    ],

    'nav' => [
        'skip' => 'Skip to content',
        'menu' => 'Main',
        'languages' => 'Language',
        'home' => fn (array $who): string => "{$who['name']}, home page",
        'repository' => 'GitHub',
        'install' => 'Install',
    ],

    'status' => [
        'label' => 'Project status',
        'release' => 'Release [VERSIONE] available',
        'systems' => 'Ubuntu 24.04 · 26.04',
        'license' => 'Elastic License 2.0',
        'price' => 'No subscription',
    ],

    'scope' => [
        'caption' => 'Simulation · a WordPress site under traffic',
        'toggle' => 'Cache',
        'on' => 'on',
        'off' => 'off',
        'axis' => 'Response time',
        'stable' => 'Stable',
        'strained' => 'Under strain',
        'describeOn' => 'With the cache, response time stays low and flat',
        'describeOff' => 'Without the cache, response time climbs and swings',
        'cache' => 'Cache',
        'cacheOn' => 'HIT',
        'cacheOff' => 'MISS',
        'php' => 'Site PHP',
        'phpOn' => 'idle',
        'phpOff' => 'at the limit',
        'db' => 'Database',
        'dbOn' => 'idle',
        'dbOff' => 'busy',
        'note' => 'An illustration, not a measurement. The real numbers are in section 03.',
    ],

    'measures' => [
        'caption' => 'Measurement results, by scenario',
        'scenario' => 'Scenario',
        'rps' => 'Requests / s',
        'p99' => 'p99',
        'errors' => 'Errors',
        'rows' => [
            'Cached page | 500 connections | [REQ/S] | [P99] | [ERRORI]',
            'Cached page | 1 connection | [REQ/S] | [P99] | [ERRORI]',
            'Static file | 1 KB | [REQ/S] | [P99] | [ERRORI]',
            'Real browsing | mix of pages | [REQ/S] | [P99] | [ERRORI]',
            'Without cache | PHP + database | [REQ/S] | [P99] | [ERRORI]',
        ],
        'notes' => [
            'Server: [CPU · RAM · SISTEMA]',
            'Load: wrk from the same region',
            'Last measured: [DATA]',
        ],
    ],

    'terminal' => [
        'user' => 'root@server',
        'shell' => 'bash',
        'copy' => 'Copy',
        'copyWhat' => 'the command',
        'copied' => 'Copied',
        'command' => 'curl -fsSL [URL INSTALLER] | bash',
        'output' => [
            '[ok] server checked: system, memory, ports',
            '[ok] nginx · PHP · MariaDB · Redis installed',
            '[ok] release signature verified',
            '[ok] panel running on https://[IP]:8443',
            '→ create the administrator: https://[IP]:8443/setup/…',
        ],
    ],

    'footer' => [
        'label' => 'Footer',
        'tagline' => 'Hosting that’s measured',
        'product' => 'Product',
        'features' => 'Features',
        'measures' => 'Measurements',
        'install' => 'Installation',
        'resources' => 'Resources',
        'docs' => 'Documentation',
        'releases' => 'What’s new',
        'repository' => 'GitHub',
        'legal' => 'Legal',
        'license' => 'Licence',
        'privacy' => 'Privacy',
    ],

    'notFound' => [
        'title' => 'This page is not here',
        'back' => 'Back to the home page',
    ],
]);
