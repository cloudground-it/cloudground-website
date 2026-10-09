/**
 * The home, built from native widgets: what a visitor gets, and what the editor shows.
 *
 * - A visitor with motion: the design's animations run — the traces, the marquee (its words
 *   doubled once, the copy hidden from screen readers), the caret — and the installer's
 *   lines wait until the terminal is scrolled to, then arrive.
 * - With reduced motion: no animation at all, nothing held back, nothing doubled.
 * - The editor: every section is visible in the preview, nothing animates or waits there,
 *   and a word changed in a native Heading shows in the preview at once and not on the
 *   published page until saved. Nothing is saved.
 *
 *   npm run test:editor
 */
import { chromium } from 'playwright';
import { BASE, check, finish, homes, login, openEditor, text } from './_harness.mjs';

const { it } = homes();
/**
 * Every section on the page has height, and nothing in it is held at opacity 0. In the
 * editor Elementor puts a `.elementor-section-wrap` between the document and its sections.
 */
const SECTIONS = '[data-elementor-type="wp-page"] > .elementor-element, [data-elementor-type="wp-page"] > .elementor-section-wrap > .elementor-element';
const visible = ( frame ) => frame.evaluate( ( selector ) => [ ...document.querySelectorAll( selector ) ].map( ( s ) => {
	const hidden = [ ...s.querySelectorAll( '*' ) ].filter( ( w ) => getComputedStyle( w ).opacity === '0' ).length;
	return s.getBoundingClientRect().height > 40 && hidden === 0;
} ), SECTIONS );
/** The names of the animations running on the page. */
const running = ( frame ) => frame.evaluate( () => [ ...new Set( document.getAnimations().map( ( a ) => a.animationName ).filter( Boolean ) ) ].sort() );

const browser = await chromium.launch();
try {
	const visitor = await ( await browser.newContext( { viewport: { width: 1440, height: 900 } } ) ).newPage();
	await visitor.goto( `${ BASE }/`, { waitUntil: 'networkidle' } );
	const names = await running( visitor );
	check( 'motion: the design’s animations run', [ 'cg-wave', 'cg-marquee', 'cg-pulse', 'cg-bar', 'cg-blink', 'cg-float' ].every( ( n ) => names.includes( n ) ), names.join( ' ' ) );
	const marquee = await visitor.evaluate( () => {
		const track = document.querySelector( '.marquee-track' );
		const kids = [ ...track.children ];
		return { total: kids.length, hidden: kids.filter( ( k ) => k.getAttribute( 'aria-hidden' ) === 'true' && k.inert ).length };
	} );
	check( 'motion: the marquee’s words doubled once, the copy hidden and inert', marquee.total === 20 && marquee.hidden === 10, JSON.stringify( marquee ) );
	const held = await visitor.evaluate( () => [ ...document.querySelectorAll( '.terminal-line' ) ].map( ( l ) => getComputedStyle( l ).opacity ) );
	check( 'motion: the installer’s lines wait below the fold', held.length === 5 && held.every( ( o ) => o === '0' ), held.join( ',' ) );
	await visitor.locator( '.terminal' ).scrollIntoViewIfNeeded();
	await visitor.waitForTimeout( 4600 );
	const played = await visitor.evaluate( () => [ ...document.querySelectorAll( '.terminal-line' ) ].map( ( l ) => getComputedStyle( l ).opacity ) );
	check( 'motion: and arrive once the terminal is on screen', played.every( ( o ) => o === '1' ), played.join( ',' ) );

	const calm = await ( await browser.newContext( { reducedMotion: 'reduce' } ) ).newPage();
	await calm.goto( `${ BASE }/`, { waitUntil: 'networkidle' } );
	check( 'reduced motion: no animation at all', ( await running( calm ) ).length === 0, ( await running( calm ) ).join( ' ' ) );
	check( 'reduced motion: nothing held back', ( await visible( calm ) ).every( Boolean ) );
	check( 'reduced motion: the marquee is not doubled', ( await calm.evaluate( () => document.querySelector( '.marquee-track' ).children.length ) ) === 10 );

	const page = await login( browser );
	const frame = await openEditor( page, it );
	const shown = await visible( frame );
	check( 'editor: every section visible in the preview', shown.length === 7 && shown.every( Boolean ), JSON.stringify( shown ) );
	check( 'editor: the preview is marked as editing, and nothing animates', ( await frame.evaluate( () => document.documentElement.classList.contains( 'editing' ) ) ) && ( await running( frame ) ).length === 0 );

	await page.evaluate( () => {
		const find = ( c ) => ( c.model?.get?.( 'widgetType' ) === 'heading' && ( c.model.get( 'settings' ).get( '_css_classes' ) || '' ).includes( 'display' ) ) ? c : ( c.children || [] ).map( find ).find( Boolean );
		window.$e.run( 'document/elements/settings', { container: find( window.elementor.getPreviewContainer() ), settings: { title: 'Dall’editor' } } );
	} );
	await page.waitForTimeout( 1200 );
	check( 'editor: the preview follows the panel', ( await frame.locator( 'h1' ).first().textContent() ).includes( 'Dall’editor' ) );
} finally {
	await browser.close();
}
check( 'nothing saved from the editor', ! ( await text( '/' ) ).includes( 'Dall’editor' ) );
finish();
