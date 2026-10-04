<?php
/**
 * Bild-Text-Abschnitt (Aicher-Stil, inc/komponenten.php): Text mit
 * Stichpunkten neben einem oder zwei Fotos.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
$image_id  = (int) ( $attributes['imageId'] ?? 0 );
$image2_id = (int) ( $attributes['image2Id'] ?? 0 );
$bullets   = array_filter( $attributes['bulletPoints'] ?? array() );
$liste     = '';
if ( $bullets ) {
	$liste = '<ul class="mt-6 grid gap-x-6 gap-y-2.5 sm:grid-cols-2">';
	foreach ( $bullets as $bullet ) {
		$liste .= '<li class="flex gap-2.5 text-foreground/85">' . elfzwo_icon( 'check', 'mt-1 h-4 w-4 shrink-0 text-signal', 2.5 ) . '<span>' . esc_html( $bullet ) . '</span></li>';
	}
	$liste .= '</ul>';
}

echo elfzwo_render_bild_text( // phpcs:ignore -- bereits escaped
	array(
		'kicker'     => $attributes['tag'] ?? '',
		'titel'      => wp_specialchars_decode( $attributes['title'] ?? '', ENT_QUOTES ),
		'text'       => wp_specialchars_decode( $attributes['description'] ?? '', ENT_QUOTES ),
		'inhalt'     => $liste,
		'bild'       => $image_id ? wp_get_attachment_image_url( $image_id, 'large' ) : ( $attributes['imageUrl'] ?? '' ),
		'bild2'      => $image2_id ? wp_get_attachment_image_url( $image2_id, 'large' ) : ( $attributes['image2Url'] ?? '' ),
		'bild_seite' => 'right' === ( $attributes['imagePosition'] ?? 'left' ) ? 'rechts' : 'links',
		'button'     => array( 'text' => $attributes['ctaText'] ?? '', 'url' => $attributes['ctaUrl'] ?? '#' ),
	)
);
