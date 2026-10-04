<?php
/**
 * Kinder- & Jugendfeuerwehr: Bild-Text-Abschnitt (inc/komponenten.php) mit
 * Foto rechts und Stichpunkten.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
$a     = $attributes;
$liste = '';
if ( ! empty( $a['punkte'] ) ) {
	$liste = '<ul class="mt-6 grid gap-x-6 gap-y-2.5 sm:grid-cols-2">';
	foreach ( $a['punkte'] as $punkt ) {
		$liste .= '<li class="flex gap-2.5 text-foreground/85">' . elfzwo_icon( 'check', 'mt-1 h-4 w-4 shrink-0 text-signal', 2.5 ) . '<span>' . esc_html( $punkt ) . '</span></li>';
	}
	$liste .= '</ul>';
}

echo elfzwo_render_bild_text( // phpcs:ignore -- bereits escaped
	array(
		'titel'      => $a['titel'] ?? '',
		'text'       => $a['text'] ?? '',
		'inhalt'     => $liste,
		'bild'       => elfzwo_block_bild( $a, 'bild' ),
		'bild_seite' => 'rechts',
		'button'     => array( 'text' => $a['buttonText'] ?? '', 'url' => $a['buttonUrl'] ?? '/jugendfeuerwehr/' ),
	)
);
