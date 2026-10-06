<?php
/**
 * Seitenkopf als geschwungenes Jumbotron (inc/komponenten.php); das Bild des
 * Blocks liegt als Hintergrund unter dem roten Schleier.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
$line2     = $attributes['titleLine2'] ?? '';
$titel     = trim( ( $attributes['titleLine1'] ?? '' ) . ' ' . ( $attributes['titleHighlight'] ?? '' ) );
$titel    .= $line2 ? ( preg_match( '/^[.,!?:;]/u', $line2 ) ? '' : ' ' ) . $line2 : '';
$image_id  = (int) ( $attributes['imageId'] ?? 0 );
$image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'full' ) : ( $attributes['imageUrl'] ?? '' );

echo elfzwo_render_jumbotron( // phpcs:ignore -- bereits escaped
	array(
		'titel'      => $titel,
		'untertitel' => $attributes['badge'] ?? '',
		'text'       => $attributes['description'] ?? '',
		'bild'       => $image_url,
		'buttons'    => array( array( 'text' => $attributes['ctaPrimary'] ?? '', 'url' => $attributes['ctaUrl'] ?? elfzwo_mitmachen_url() ) ),
	)
);
