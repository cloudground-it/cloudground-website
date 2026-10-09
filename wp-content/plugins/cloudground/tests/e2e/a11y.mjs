/**
 * Accessibility with axe, on every kind of page the site has, in both languages and on a
 * phone: WCAG 2.1 AA and best practice, serious and critical violations fail.
 *
 * The home is audited twice: as it loads, and with the oscilloscope switched off, whose
 * words and colours (orange on the dark screen) only exist in that state.
 *
 *   npm run test:a11y
 */
import { chromium } from 'playwright';
import { BASE, audit, finish } from './_harness.mjs';

const browser = await chromium.launch();
try {
	for ( const [ width, label ] of [ [ 1440, 'desktop' ], [ 390, 'phone' ] ] ) {
		const page = await ( await browser.newContext( { viewport: { width, height: 900 }, reducedMotion: 'reduce' } ) ).newPage();
		for ( const url of [ '/', '/en/', '/licenza/', '/en/licenza/', '/privacy/', '/en/privacy/', '/not-a-page/' ] ) {
			await page.goto( BASE + url, { waitUntil: 'networkidle' } );
			await audit( page, `${ url } (${ label })` );
		}
		for ( const url of [ '/', '/en/' ] ) {
			await page.goto( BASE + url, { waitUntil: 'networkidle' } );
			await page.click( '.scope-toggle' );
			await audit( page, `${ url } cache off (${ label })` );
		}
	}
} finally {
	await browser.close();
}
finish();
