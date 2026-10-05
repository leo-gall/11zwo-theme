<?php
/**
 * Aktive Mannschaft: Bild-Text-Abschnitt (inc/komponenten.php) mit einem
 * Foto links und einem kurzen Zeitstrahl unter dem Text.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
$a      = $attributes;
$punkte = array_filter( $a['punkte'] ?? array(), function ( $p ) { return ! empty( $p['title'] ); } );
$liste  = '';
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

echo elfzwo_render_bild_text( // phpcs:ignore -- bereits escaped
	array(
		'titel'       => $a['titel'] ?? '',
		'text'        => $a['text'] ?? '',
		'inhalt'      => $liste,
		'bild'        => elfzwo_block_bild( $a, 'bild', get_template_directory_uri() . '/assets/images/hero-team.jpg' ),
		'bild_seite'  => 'links',
		// Querformat wie das Foto selbst, damit links und rechts keine Fahrzeuge abgeschnitten werden.
		'bild_format' => 'aspect-[3/2]',
		'button'      => array( 'text' => $a['buttonText'] ?? '', 'url' => $a['buttonUrl'] ?? '/mitmachen/' ),
	)
);
