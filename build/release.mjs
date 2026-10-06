// Baut das Upload-Zip: Tailwind kompilieren, das Theme nach dist/11zwo kopieren,
// PHP ohne Kommentare und Leerraum (php -w), JavaScript und CSS minifiziert,
// JSON kompakt, Skripte und Stylesheets unter neutralen Hash-Namen. Ergebnis: ../11zwo.wp-JJJJ-MM-TT-HHMM.zip (ältere Zips werden gelöscht).
//
//   ./zip.sh   (oder: npm run release)

import { execFileSync } from 'node:child_process';
import crypto from 'node:crypto';
import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import { transform } from 'esbuild';

const THEME = path.resolve( path.dirname( fileURLToPath( import.meta.url ) ), '..' );
const DIST = path.join( THEME, 'dist' );
const ZIEL = path.join( DIST, '11zwo' );

// Nur zum Entwickeln nötig, gehört nicht ins Theme auf dem Server.
const AUSLASSEN = new Set( [
	'.git', '.github', '.gitignore', '.DS_Store', 'node_modules', 'dist', 'build',
	'package.json', 'package-lock.json', 'tailwind.config.js', 'assets/css/tailwind.src.css', 'zip.sh',
] );

const LIZENZEN = `Verwendete Fremdbestandteile
============================

Icons: SVG Repo, Sammlung "Hand Drawn" (CC0 1.0), https://www.svgrepo.com/collection/hand-drawn/
Menü-Pfeil und RSS-Symbol: Lucide (ISC), (c) Lucide Contributors, https://lucide.dev

Schrift: Titillium Web, SIL Open Font License 1.1, siehe assets/fonts/OFL.txt

CSS: Tailwind CSS (MIT), (c) Tailwind Labs, Inc.
`;

function dateien( ordner, relativ = '' ) {
	const liste = [];
	for ( const eintrag of fs.readdirSync( path.join( ordner, relativ ), { withFileTypes: true } ) ) {
		const rel = path.posix.join( relativ, eintrag.name );
		if ( AUSLASSEN.has( eintrag.name ) || AUSLASSEN.has( rel ) || eintrag.name.endsWith( '.zip' ) ) {
			continue;
		}
		if ( eintrag.isDirectory() ) {
			liste.push( ...dateien( ordner, rel ) );
		} else {
			liste.push( rel );
		}
	}
	return liste;
}

async function verarbeite( rel ) {
	const quelle = path.join( THEME, rel );
	const ziel = path.join( ZIEL, rel );
	fs.mkdirSync( path.dirname( ziel ), { recursive: true } );

	if ( rel === 'style.css' ) {
		// Der Kommentarkopf trägt die Theme-Angaben für WordPress und muss bleiben.
		fs.copyFileSync( quelle, ziel );
	} else if ( rel.endsWith( '.php' ) ) {
		fs.writeFileSync( ziel, execFileSync( 'php', [ '-w', quelle ] ) );
		execFileSync( 'php', [ '-l', ziel ], { stdio: 'pipe' } );
	} else if ( rel.endsWith( '.js' ) ) {
		const { code } = await transform( fs.readFileSync( quelle, 'utf8' ), { loader: 'js', minify: true, legalComments: 'none', target: 'es2017' } );
		fs.writeFileSync( ziel, code );
	} else if ( rel.endsWith( '.css' ) && rel !== 'assets/css/tailwind.css' ) {
		const { code } = await transform( fs.readFileSync( quelle, 'utf8' ), { loader: 'css', minify: true, legalComments: 'none' } );
		fs.writeFileSync( ziel, code );
	} else if ( rel.endsWith( '.json' ) ) {
		fs.writeFileSync( ziel, JSON.stringify( JSON.parse( fs.readFileSync( quelle, 'utf8' ) ) ) );
	} else {
		fs.copyFileSync( quelle, ziel );
	}
}

execFileSync( 'npx', [ 'tailwindcss', '-i', 'assets/css/tailwind.src.css', '-o', 'assets/css/tailwind.css', '--minify' ], { cwd: THEME, stdio: 'inherit' } );

fs.rmSync( DIST, { recursive: true, force: true } );
const liste = dateien( THEME );
for ( const rel of liste ) {
	await verarbeite( rel );
}
fs.writeFileSync( path.join( ZIEL, 'LIZENZEN.txt' ), LIZENZEN );

// Skripte und Stylesheets bekommen neutrale Namen aus ihrem Inhalt (z. B.
// assets/js/3f9a1c2e.js); alle Verweise in PHP, JSON, JS und CSS werden mit
// umbenannt. Die Editor-Skripte der Blöcke nehmen ihre .asset.php mit.
const UMBENENNEN = /^(assets\/(js|css)\/[^/]+\.(js|css)|inc\/blocks\/[^/]+\/edit\.js)$/;
const umbenannt = {};
for ( const rel of liste.filter( ( r ) => UMBENENNEN.test( r ) ) ) {
	const datei = path.join( ZIEL, rel );
	const endung = path.extname( rel );
	const hash = crypto.createHash( 'sha1' ).update( rel ).update( fs.readFileSync( datei ) ).digest( 'hex' ).slice( 0, 10 );
	const neu = path.posix.join( path.posix.dirname( rel ), hash + endung );
	fs.renameSync( datei, path.join( ZIEL, neu ) );
	umbenannt[ rel ] = neu;
	const asset = datei.replace( /\.js$/, '.asset.php' );
	if ( endung === '.js' && fs.existsSync( asset ) ) {
		const asset_neu = path.join( ZIEL, path.posix.dirname( rel ), hash + '.asset.php' );
		fs.renameSync( asset, asset_neu );
		fs.writeFileSync( asset_neu, fs.readFileSync( asset_neu, 'utf8' ).split( 'edit.js' ).join( hash + '.js' ) );
	}
}
const TEXT = /\.(php|json|js|css)$/;
for ( const rel of dateien( ZIEL ).filter( ( r ) => TEXT.test( r ) ) ) {
	const datei = path.join( ZIEL, rel );
	let inhalt = fs.readFileSync( datei, 'utf8' );
	const vorher = inhalt;
	for ( const [ alt, neu ] of Object.entries( umbenannt ) ) {
		if ( alt.startsWith( 'inc/blocks/' ) ) {
			if ( rel === path.posix.join( path.posix.dirname( alt ), 'block.json' ) ) {
				inhalt = inhalt.split( 'file:./edit.js' ).join( 'file:./' + path.posix.basename( neu ) );
			}
		} else {
			inhalt = inhalt.split( '/' + alt ).join( '/' + neu );
		}
	}
	if ( inhalt !== vorher ) {
		fs.writeFileSync( datei, inhalt );
	}
}
const uebrig = dateien( ZIEL ).filter( ( r ) => TEXT.test( r ) ).filter( ( r ) => Object.keys( umbenannt ).some( ( alt ) => ! alt.startsWith( 'inc/blocks/' ) && fs.readFileSync( path.join( ZIEL, r ), 'utf8' ).includes( '/' + alt ) ) );
if ( uebrig.length ) {
	throw new Error( 'Alte Dateinamen noch referenziert in: ' + uebrig.join( ', ' ) );
}

const themes = path.dirname( THEME );
for ( const alt of fs.readdirSync( themes ).filter( ( n ) => /^11zwo.*\.zip$/.test( n ) ) ) {
	fs.rmSync( path.join( themes, alt ) );
}
const jetzt = new Date();
const zwei = ( n ) => String( n ).padStart( 2, '0' );
const name = `11zwo.wp-${ jetzt.getFullYear() }-${ zwei( jetzt.getMonth() + 1 ) }-${ zwei( jetzt.getDate() ) }-${ zwei( jetzt.getHours() ) }${ zwei( jetzt.getMinutes() ) }.zip`;
execFileSync( 'zip', [ '-qr', path.join( themes, name ), '11zwo' ], { cwd: DIST } );

console.log( `${ liste.length } Dateien kompiliert, ${ Object.keys( umbenannt ).length } umbenannt, Zip: wp-content/themes/${ name }` );
