/**
 * What the pages tell search engines, sharing cards and the browser, and what they ask of
 * other servers: read-only, so it is also safe against production (BASE=…).
 *
 * - Title, description, canonical and `hreflang` per language; the same `hreflang` set on
 *   both versions of a page, plus `x-default`.
 * - The sharing card (1200×630), described in the page's language; the tile as the icon.
 * - The faces preloaded from this site, and no request to any other host: the fonts are
 *   self-hosted so that no visitor's address goes to Google, and the privacy notice will
 *   promise it.
 *
 *   npm run test:head
 */
import { chromium } from 'playwright';
import { BASE, check, finish, text } from './_harness.mjs';

const tags = ( html ) => ( {
	lang: ( html.match( /<html lang="([^"]+)"/ ) || [] )[ 1 ],
	title: ( html.match( /<title>([^<]*)<\/title>/ ) || [] )[ 1 ],
	description: ( html.match( /<meta name="description" content="([^"]*)"/ ) || [] )[ 1 ],
	canonical: ( html.match( /<link rel="canonical" href="([^"]+)"/ ) || [] )[ 1 ],
	alternates: [ ...html.matchAll( /<link rel="alternate" hreflang="([^"]+)" href="([^"]+)"/g ) ].map( ( m ) => `${ m[ 1 ] }=${ m[ 2 ] }` ).sort(),
	og: Object.fromEntries( [ ...html.matchAll( /<meta property="og:([a-z_:]+)" content="([^"]*)"/g ) ].map( ( m ) => [ m[ 1 ], m[ 2 ] ] ) ),
	icon: /<link rel="icon" href="[^"]+favicon\.svg" type="image\/svg\+xml"/.test( html ),
	preloads: [ ...html.matchAll( /<link rel="preload" href="([^"]+\.woff2)" as="font" type="font\/woff2" crossorigin/g ) ].length,
	robots: ( html.match( /<meta name='robots' content='([^']+)'/ ) || [] )[ 1 ] || '',
} );

const pages = {
	'/': { lang: 'it-IT', title: 'CloudGround — hosting che si misura', locale: 'it_IT', alt: 'CloudGround — Hosting che si misura' },
	'/en/': { lang: 'en', title: 'CloudGround — hosting that’s measured', locale: 'en_GB', alt: 'CloudGround — Hosting that’s measured' },
};
const sets = [];
for ( const [ url, want ] of Object.entries( pages ) ) {
	const t = tags( await text( url ) );
	check( `${ url }: language, title and description`, t.lang === want.lang && t.title === want.title && t.description.length > 60, `${ t.lang } · ${ t.title } · ${ t.description.length }` );
	check( `${ url }: canonical is its own address`, t.canonical === BASE + url, t.canonical );
	check( `${ url }: hreflang it, en and x-default`, t.alternates.length === 3 && t.alternates.includes( `x-default=${ BASE }/` ) && t.alternates.includes( `en=${ BASE }/en/` ), t.alternates.join( ' ' ) );
	sets.push( t.alternates.join( ' ' ) );
	check( `${ url }: sharing card 1200×630, described in its language`, /\.png\?v=\d+$/.test( t.og.image || '' ) && t.og[ 'image:width' ] === '1200' && t.og[ 'image:height' ] === '630' && t.og[ 'image:alt' ] === want.alt && t.og.locale === want.locale, JSON.stringify( t.og ) );
	check( `${ url }: the tile as the icon, the faces preloaded`, t.icon && t.preloads === 3 );
	check( `${ url }: indexed`, ! t.robots.includes( 'noindex' ), t.robots );
}
check( 'the same hreflang set in both languages', sets[ 0 ] === sets[ 1 ] );

for ( const url of [ '/licenza/', '/en/licenza/', '/privacy/', '/en/privacy/' ] ) {
	const t = tags( await text( url ) );
	check( `${ url }: own canonical, hreflang pair`, t.canonical === BASE + url && t.alternates.length === 3, `${ t.canonical } ${ t.alternates.join( ' ' ) }` );
}
const missing = tags( await text( '/not-a-page/' ) );
check( '404: noindex, no hreflang', missing.robots.includes( 'noindex' ) && missing.alternates.length === 0 );

const card = await fetch( tags( await text( '/' ) ).og.image );
check( 'the sharing card answers, as a PNG', card.ok && card.headers.get( 'content-type' ) === 'image/png' );

// Nothing fetched from anywhere but this site, on any kind of page.
const browser = await chromium.launch();
try {
	const page = await browser.newPage();
	const elsewhere = new Set();
	const origin = new URL( BASE ).origin;
	page.on( 'request', ( r ) => {
		if ( ! r.url().startsWith( origin ) && ! r.url().startsWith( 'data:' ) ) {
			elsewhere.add( new URL( r.url() ).host );
		}
	} );
	for ( const url of [ '/', '/en/', '/licenza/', '/privacy/' ] ) {
		await page.goto( BASE + url, { waitUntil: 'networkidle' } );
	}
	check( 'no request to another host (fonts included)', elsewhere.size === 0, [ ...elsewhere ].join( ' ' ) );
	// On the home, which sets words in all three (a page that uses no serif never loads it).
	await page.goto( BASE + '/', { waitUntil: 'networkidle' } );
	const fonts = await page.evaluate( async () => {
		await document.fonts.ready;
		return [ ...document.fonts ].filter( ( f ) => f.status === 'loaded' ).map( ( f ) => f.family.replace( /"/g, '' ) ).sort();
	} );
	check( 'the three faces load from this site', [ 'Archivo', 'Geist Mono', 'Instrument Serif' ].every( ( f ) => fonts.includes( f ) ), fonts.join( ', ' ) );
} finally {
	await browser.close();
}
finish();
