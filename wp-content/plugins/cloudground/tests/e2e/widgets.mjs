/**
 * The plugin's widgets, end to end: the cache oscilloscope, the measurements table and the
 * install terminal.
 *
 * For each: a sentence typed into a field is what the page says, in that page's language
 * only; an empty field says the site's own words; markup a field does not allow is dropped;
 * the editor's field starts from the page's words, in its language. Then what they do: the
 * switch flips the oscilloscope's state and says so, the button copies exactly the command
 * on screen, and the table reads its rows from the field.
 *
 * Everything it changes is put back.
 *
 *   npm run test:widgets
 */
import { chromium } from 'playwright';
import { BASE, check, finish, homes, login, openEditor, setWidget, snapshot, text } from './_harness.mjs';

const { it, en } = homes();
const restore = snapshot( it );

try {
	// The oscilloscope's words.
	setWidget( it, 'cloudground-cache-scope', { t__scope__caption: 'Una prova dal campo', t__scope__note: 'Nota <em>scritta</em><script>alert(1)</script>' } );
	let page = await text( '/' );
	check( 'scope: a typed caption', page.includes( 'Una prova dal campo' ) );
	check( 'scope: allowed markup kept, a script dropped', page.includes( 'Nota <em>scritta</em>' ) && ! page.includes( '<script>alert(1)' ) );
	check( 'scope: the other language keeps its own words', ! ( await text( '/en/' ) ).includes( 'Una prova dal campo' ) );
	setWidget( it, 'cloudground-cache-scope', { t__scope__caption: '' } );
	check( 'scope: an empty field says the site’s words', ( await text( '/' ) ).includes( 'Simulazione · un sito WordPress sotto traffico' ) );

	setWidget( it, 'cloudground-cache-scope', { start_on: '' } );
	check( 'scope: the control sets the starting state', ( await text( '/' ) ).includes( 'data-cache="off"' ) );

	// The table: rows typed as paragraphs, five values each.
	setWidget( it, 'cloudground-measures', { t__measures__rows: 'Prova uno | nota | 1234 | 5 ms | 0\n\nProva due | altra | 99 | 7 ms | 1', t__measures__scenario: 'Caso <b>x</b>' } );
	page = await text( '/' );
	const rows = [ ...page.matchAll( /<tr class="measures-row">([\s\S]*?)<\/tr>/g ) ].map( ( m ) => m[ 1 ].replace( /<[^>]+>/g, ' ' ).replace( /\s+/g, ' ' ).trim() );
	check( 'measures: one row per paragraph, its values in the cells', rows.length === 2 && rows[ 0 ] === 'Prova uno nota 1234 5 ms 0', JSON.stringify( rows ) );
	check( 'measures: a one-line field takes no markup', page.includes( 'Caso x' ) && ! page.includes( '<b>x</b>' ) );
	setWidget( it, 'cloudground-measures', { t__measures__rows: '', t__measures__scenario: '' } );
	page = await text( '/' );
	check( 'measures: empty, the placeholders stay as they are', ( page.match( /\[REQ\/S\]/g ) || [] ).length === 5 && page.includes( '[P99]' ) && page.includes( '[ERRORI]' ) );

	// The terminal.
	setWidget( it, 'cloudground-install-terminal', { t__terminal__command: 'echo prova' } );
	check( 'terminal: a typed command', ( await text( '/' ) ).includes( '<code data-command>echo prova</code>' ) );
	setWidget( it, 'cloudground-install-terminal', { t__terminal__command: '' } );
	check( 'terminal: empty, the placeholder command', ( await text( '/' ) ).includes( 'curl -fsSL [URL INSTALLER] | bash' ) );
} finally {
	restore();
}
check( 'restored', ( await text( '/' ) ).includes( 'Simulazione · un sito WordPress sotto traffico' ) );

const browser = await chromium.launch();
try {
	// What the widgets do, for a visitor.
	const context = await browser.newContext( { viewport: { width: 1440, height: 900 } } );
	await context.grantPermissions( [ 'clipboard-read', 'clipboard-write' ], { origin: BASE } );
	const visitor = await context.newPage();
	await visitor.goto( `${ BASE }/`, { waitUntil: 'networkidle' } );
	const state = () => visitor.evaluate( () => {
		const scope = document.querySelector( '[data-scope]' );
		const shown = ( sel ) => [ ...scope.querySelectorAll( sel ) ].filter( ( e ) => e.offsetParent !== null ).map( ( e ) => e.textContent.trim() ).join( '|' );
		return {
			cache: scope.dataset.cache,
			pressed: scope.querySelector( '.scope-toggle' ).getAttribute( 'aria-pressed' ),
			label: scope.querySelector( '.scope-screen' ).getAttribute( 'aria-label' ),
			button: scope.querySelector( '.scope-toggle' ).innerText.trim(),
			reading: shown( '.scope-reading--cache .scope-value [data-when]' ),
			wave: [ ...scope.querySelectorAll( '.scope-wave' ) ].filter( ( g ) => getComputedStyle( g ).display !== 'none' ).map( ( g ) => g.dataset.when ).join( '|' ),
		};
	} );
	const before = await state();
	await visitor.click( '.scope-toggle' );
	const after = await state();
	check( 'scope: on at first', before.cache === 'on' && before.pressed === 'true' && /accesa/i.test( before.button ) && before.reading === 'HIT' && before.wave === 'on', JSON.stringify( before ) );
	check( 'scope: the switch turns the cache off, and says so', after.cache === 'off' && after.pressed === 'false' && /spenta/i.test( after.button ) && after.reading === 'MISS' && after.wave === 'off' && after.label.startsWith( 'Senza cache' ), JSON.stringify( after ) );
	await visitor.keyboard.press( 'Enter' );
	check( 'scope: the keyboard turns it back on', ( await state() ).cache === 'on' );

	await visitor.click( '.terminal-copy' );
	const copied = await visitor.evaluate( async () => ( {
		clip: await navigator.clipboard.readText(),
		code: document.querySelector( '[data-command]' ).textContent.trim(),
		label: document.querySelector( '[data-copy-label]' ).textContent,
		status: document.querySelector( '[data-copy-status]' ).textContent,
	} ) );
	check( 'terminal: the button copies exactly the command on screen', copied.clip === copied.code && copied.clip === 'curl -fsSL [URL INSTALLER] | bash', JSON.stringify( copied ) );
	check( 'terminal: the button says it, and so does the status region', copied.label === 'Copiato' && copied.status === 'Copiato' );
	await visitor.waitForTimeout( 2300 );
	check( 'terminal: and then goes back', ( await visitor.textContent( '[data-copy-label]' ) ) === 'Copia' );

	// No script: the switch and the button are not offered, the words are all there.
	const still = await ( await browser.newContext( { javaScriptEnabled: false } ) ).newPage();
	await still.goto( `${ BASE }/` );
	check( 'without JavaScript: no switch and no copy button, the starting state shown', await still.evaluate( () => getComputedStyle( document.querySelector( '.scope-toggle' ) ).display === 'none' && getComputedStyle( document.querySelector( '.terminal-copy' ) ).display === 'none' && document.querySelector( '.scope-reading--cache .scope-value' ).innerText.trim() === 'HIT' ) );

	// The editor's fields start from the page's own words, in its language.
	const page = await login( browser );
	for ( const [ id, lang, expected ] of [ [ it, 'it', [ 'Tempo di risposta', 'Copia', 'Scenario' ] ], [ en, 'en', [ 'Response time', 'Copy', 'Scenario' ] ] ] ) {
		if ( ! id ) {
			continue;
		}
		await openEditor( page, id );
		const values = await page.evaluate( () => {
			const find = ( c, type ) => ( c.model?.get?.( 'widgetType' ) === type ? c : ( c.children || [] ).map( ( x ) => find( x, type ) ).find( Boolean ) );
			const root = window.elementor.getPreviewContainer();
			return [
				find( root, 'cloudground-cache-scope' )?.settings?.get( 't__scope__axis' ),
				find( root, 'cloudground-install-terminal' )?.settings?.get( 't__terminal__copy' ),
				find( root, 'cloudground-measures' )?.settings?.get( 't__measures__scenario' ),
			];
		} );
		check( `editor (${ lang }): the fields start from the page's words`, JSON.stringify( values ) === JSON.stringify( expected ), JSON.stringify( values ) );
	}
} finally {
	await browser.close();
}
finish();
