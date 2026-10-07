<?php
/**
 * Seitenkopf als Jumbotron (inc/komponenten.php); das Foto liegt als
 * Hintergrund unter dem roten Schleier.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
$bild_id = (int) ( $attributes['bildId'] ?? 0 );

echo elfzwo_render_jumbotron( // phpcs:ignore -- bereits escaped
	array(
		'titel'      => $attributes['titel'] ?? '',
		'untertitel' => $attributes['untertitel'] ?? '',
		'text'       => $attributes['text'] ?? '',
		'bild'       => $bild_id ? wp_get_attachment_image_url( $bild_id, 'full' ) : ( $attributes['bildUrl'] ?? '' ),
	)
);
