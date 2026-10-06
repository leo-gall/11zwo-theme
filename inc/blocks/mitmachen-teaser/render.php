<?php
/**
 * Startseite: Mitmachen als zwei Bild-Text-Abschnitte (inc/komponenten.php)
 * — aktive Mannschaft mit zwei Fotos, darunter die Kinder- & Jugendfeuerwehr
 * mit Foto auf der anderen Seite.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
$a      = $attributes;
$teil   = $a['teil'] ?? 'beide';
$punkte = array_filter( $a['punkte'] ?? array(), function ( $p ) { return ! empty( $p['title'] ); } );
$bild   = function ( $key, $fallback = '' ) use ( $a ) {
	$id = (int) ( $a[ $key . 'Id' ] ?? 0 );
	return ( $id ? wp_get_attachment_image_url( $id, 'large' ) : ( $a[ $key . 'Url' ] ?? '' ) ) ?: $fallback;
};

$liste = '';
if ( $punkte ) {
	$liste = '<ol class="mt-7 space-y-4 border-l-2 border-signal/25 pl-6">';
	foreach ( $punkte as $p ) {
		$liste .= '<li class="relative"><span class="absolute -left-[2.0625rem] top-1.5 h-3.5 w-3.5 rounded-full border-[3px] border-background bg-signal" aria-hidden="true"></span>'
			. '<p class="font-semibold">' . esc_html( $p['title'] ) . '</p>'
			. ( ! empty( $p['body'] ) ? '<p class="mt-0.5 font-light text-foreground/75">' . esc_html( $p['body'] ) . '</p>' : '' )
			. '</li>';
	}
	$liste .= '</ol>';
}

$jugend_liste = '';
if ( ! empty( $a['jugendPunkte'] ) ) {
	$jugend_liste = '<ul class="mt-6 grid gap-x-6 gap-y-2.5 sm:grid-cols-2">';
	foreach ( $a['jugendPunkte'] as $punkt ) {
		$jugend_liste .= '<li class="flex gap-2.5 text-foreground/85">' . elfzwo_icon( 'check', 'mt-1 h-4 w-4 shrink-0 text-signal', 2.5 ) . '<span>' . esc_html( $punkt ) . '</span></li>';
	}
	$jugend_liste .= '</ul>';
}

if ( 'jugend' !== $teil ) {
	echo elfzwo_render_bild_text( // phpcs:ignore -- bereits escaped
	array(
		'titel'      => $a['aktiveTitel'] ?? '',
		'text'       => $a['intro'] ?? '',
		'inhalt'     => $liste,
		'bild'       => $bild( 'aktiveImage', get_template_directory_uri() . '/assets/images/hero-team.jpg' ),
		'bild2'      => $bild( 'aktiveImage2', get_template_directory_uri() . '/assets/images/hero-hintergrund.jpg' ),
		'bild_seite' => 'links',
		'button'     => array( 'text' => $a['aktiveButtonText'] ?? '', 'url' => $a['aktiveButtonUrl'] ?? elfzwo_mitmachen_url() ),
	)
	);
}
if ( 'aktive' !== $teil ) {
	echo elfzwo_render_bild_text( // phpcs:ignore -- bereits escaped
	array(
		'titel'      => $a['jugendTitel'] ?? '',
		'text'       => $a['jugendText'] ?? '',
		'inhalt'     => $jugend_liste,
		'bild'       => $bild( 'jugendImage' ),
		'bild_seite' => 'rechts',
		'button'     => array( 'text' => $a['jugendButtonText'] ?? '', 'url' => $a['jugendButtonUrl'] ?? '/jugendfeuerwehr/' ),
	)
);
}
