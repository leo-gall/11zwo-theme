// Baut das Upload-Zip: Tailwind kompilieren, das Theme nach dist/11zwo kopieren,
// PHP ohne Kommentare und Leerraum (php -w), JavaScript und CSS minifiziert,
// JSON kompakt. Ergebnis: ../11zwo.wp-JJJJ-MM-TT-HHMM.zip (ältere Zips werden gelöscht).
//
//   ./zip.sh   (oder: npm run release)

import { execFileSync } from 'node:child_process';
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

Icons: sketchyicons (MIT), https://github.com/Fantomiald/sketchyicons
Formen abgeleitet von Lucide (ISC), (c) Lucide Icons and Contributors, https://lucide.dev

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

const themes = path.dirname( THEME );
for ( const alt of fs.readdirSync( themes ).filter( ( n ) => /^11zwo.*\.zip$/.test( n ) ) ) {
	fs.rmSync( path.join( themes, alt ) );
}
const jetzt = new Date();
const zwei = ( n ) => String( n ).padStart( 2, '0' );
const name = `11zwo.wp-${ jetzt.getFullYear() }-${ zwei( jetzt.getMonth() + 1 ) }-${ zwei( jetzt.getDate() ) }-${ zwei( jetzt.getHours() ) }${ zwei( jetzt.getMinutes() ) }.zip`;
execFileSync( 'zip', [ '-qr', path.join( themes, name ), '11zwo' ], { cwd: DIST } );

console.log( `${ liste.length } Dateien kompiliert, Zip: wp-content/themes/${ name }` );
