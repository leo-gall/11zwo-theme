<?php
/**
 * Seitenkopf als geschwungenes Jumbotron (inc/komponenten.php).
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
$image_id = (int) ( $attributes['imageId'] ?? 0 );

echo elfzwo_render_jumbotron( // phpcs:ignore -- bereits escaped
	array(
		'titel'      => $attributes['title'] ?? '',
		'untertitel' => $attributes['kicker'] ?? '',
		'text'       => $attributes['description'] ?? '',
		'bild'       => $image_id ? wp_get_attachment_image_url( $image_id, 'full' ) : ( $attributes['imageUrl'] ?? '' ),
		'buttons'    => array(
			array( 'text' => $attributes['ctaPrimaryText'] ?? '', 'url' => $attributes['ctaPrimaryUrl'] ?? '' ),
			array( 'text' => $attributes['ctaSecondaryText'] ?? '', 'url' => $attributes['ctaSecondaryUrl'] ?? '' ),
		),
	)
);
