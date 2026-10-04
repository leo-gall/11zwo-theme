<?php
/**
 * Neuer Aufbau der Startseite: Hero, aktive Mannschaft, Neuigkeiten aus dem
 * Blog und Kinder- & Jugendfeuerwehr. Läuft einmalig nach dem Update
 * (auch auf dem Produktivserver); die bisherige Startseite bleibt als
 * Revision erhalten und lässt sich im Editor wiederherstellen.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function elfzwo_startseite_block( $name, $attrs ) {
	return array(
		'blockName'    => $name,
		'attrs'        => $attrs,
		'innerBlocks'  => array(),
		'innerHTML'    => '',
		'innerContent' => array(),
	);
}

function elfzwo_migrate_startseite() {
	if ( get_option( 'elfzwo_startseite_v2' ) ) {
		return;
	}
	$page = get_post( (int) get_option( 'page_on_front' ) );
	if ( ! $page ) {
		update_option( 'elfzwo_startseite_v2', 1 );
		return;
	}

	// Foto und Jugendfeuerwehr-Abschnitt der bisherigen Startseite übernehmen.
	$alt = array();
	foreach ( parse_blocks( $page->post_content ) as $block ) {
		if ( $block['blockName'] && ! isset( $alt[ $block['blockName'] ] ) ) {
			$alt[ $block['blockName'] ] = $block['attrs'];
		}
	}
	$hero_alt    = $alt['elfzwo/home-hero'] ?? array();
	$jugend      = $alt['elfzwo/feature-panel'] ?? null;
	$zeitstrahl  = $alt['elfzwo/join-timeline'] ?? null;

	$hero = array(
		'subtitle'         => '',
		'title1'           => 'Für Greifenberg',
		'title2'           => 'im Einsatz.',
		'description'      => 'Willkommen bei der Freiwilligen Feuerwehr Greifenberg! Hier erfährst du alles über uns, unsere ehrenamtliche Arbeit und wie du bei uns mitmachen oder uns unterstützen kannst.',
		'ctaPrimaryText'   => 'Mitmachen',
		'ctaSecondaryText' => 'Kinder- & Jugendfeuerwehr',
		'ctaSecondaryUrl'  => '/jugendfeuerwehr/',
		'emergencyBadge'   => '',
	);
	foreach ( array( 'image1Id', 'image1Url' ) as $key ) {
		if ( ! empty( $hero_alt[ $key ] ) ) {
			$hero[ $key ] = $hero_alt[ $key ];
		}
	}

	// Mitmachen: Zeitstrahl (aktive Mannschaft) und Jugendfeuerwehr in einem Abschnitt.
	$punkte  = array();
	$aufruf  = array( 'buttonText' => 'Mach mit!', 'buttonUrl' => '/mitmachen/' );
	foreach ( ( $zeitstrahl['steps'] ?? array() ) as $step ) {
		if ( ! empty( $step['buttonText'] ) && empty( $step['number'] ) ) {
			$aufruf = $step;
		} elseif ( ! empty( $step['title'] ) ) {
			$punkte[] = array( 'title' => $step['title'], 'body' => $step['body'] ?? '' );
		}
	}
	$jugend   = $jugend ?? array();
	$mitmachen = array(
		'titel'            => 'Mach mit',
		'intro'            => 'Wir suchen laufend Verstärkung. Vorwissen brauchst du keins – wir bilden dich aus.',
		'aktiveTitel'      => 'Aktive Mannschaft',
		'punkte'           => $punkte,
		'aktiveButtonText' => $aufruf['buttonText'] ?? 'Mach mit!',
		'aktiveButtonUrl'  => ( $aufruf['buttonUrl'] ?? '' ) ?: '/mitmachen/',
		'aktiveImageId'    => (int) ( $hero_alt['image1Id'] ?? 0 ),
		'aktiveImageUrl'   => $hero_alt['image1Url'] ?? '',
		'jugendTitel'      => str_replace( 'Jugenfeuerwehr', 'Jugendfeuerwehr', wp_specialchars_decode( $jugend['title'] ?? 'Kinder- & Jugendfeuerwehr', ENT_QUOTES ) ),
		'jugendText'       => str_replace( 'Jugenfeuerwehr', 'Jugendfeuerwehr', wp_specialchars_decode( $jugend['description'] ?? '', ENT_QUOTES ) ),
		'jugendPunkte'     => $jugend['bulletPoints'] ?? array(),
		'jugendImageId'    => (int) ( $jugend['imageId'] ?? 0 ),
		'jugendImageUrl'   => $jugend['imageUrl'] ?? '',
		'jugendButtonText' => ( $jugend['ctaText'] ?? '' ) ?: 'Alles zur Jugendfeuerwehr',
		'jugendButtonUrl'  => ( $jugend['ctaUrl'] ?? '' ) ?: '/jugendfeuerwehr/',
	);

	$blocks = array(
		elfzwo_startseite_block( 'elfzwo/home-hero', $hero ),
		elfzwo_startseite_block( 'elfzwo/mitmachen-teaser', array_merge( $mitmachen, array( 'teil' => 'aktive' ) ) ),
		elfzwo_startseite_block( 'elfzwo/aktuelles-uebersicht', array( 'teil' => 'neuigkeiten' ) ),
		elfzwo_startseite_block( 'elfzwo/mitmachen-teaser', array_merge( $mitmachen, array( 'teil' => 'jugend' ) ) ),
	);

	// Bisherigen Stand ausdrücklich als Revision sichern.
	_wp_put_post_revision( $page );
	wp_update_post(
		array(
			'ID'           => $page->ID,
			'post_content' => wp_slash( serialize_blocks( $blocks ) ),
		)
	);
	update_option( 'elfzwo_startseite_v2', 1 );
}
add_action( 'init', 'elfzwo_migrate_startseite', 40 );
