<?php
/**
 * Klassischer Editor bei Beiträgen: statt des "Medien hinzufügen"-Buttons
 * gibt es in der Werkzeugleiste (neben Fett, Kursiv, Link, …) einen
 * Foto-Button. Fotos werden dort immer im Format 16:9 eingefügt — passt ein
 * Bild nicht, wird es vorher im Media-Dialog auf 16:9 zugeschnitten (als
 * neue Datei, das Original bleibt erhalten). Siehe assets/js/beitrag-foto.js.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function elfzwo_is_beitrag_editor() {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	return $screen && 'post' === $screen->base && 'post' === $screen->post_type;
}

function elfzwo_beitrag_editor_settings( $settings, $editor_id ) {
	if ( 'content' === $editor_id && elfzwo_is_beitrag_editor() ) {
		$settings['media_buttons'] = false;
	}
	return $settings;
}
add_filter( 'wp_editor_settings', 'elfzwo_beitrag_editor_settings', 10, 2 );

function elfzwo_beitrag_mce_plugins( $plugins ) {
	if ( elfzwo_is_beitrag_editor() ) {
		$plugins['elfzwo_foto'] = get_template_directory_uri() . '/assets/js/beitrag-foto.js?ver=' . filemtime( get_template_directory() . '/assets/js/beitrag-foto.js' );
	}
	return $plugins;
}
add_filter( 'mce_external_plugins', 'elfzwo_beitrag_mce_plugins' );

function elfzwo_beitrag_mce_buttons( $buttons ) {
	if ( ! elfzwo_is_beitrag_editor() ) {
		return $buttons;
	}
	$pos = array_search( 'link', $buttons, true );
	array_splice( $buttons, false === $pos ? count( $buttons ) : $pos + 1, 0, array( 'elfzwo_foto' ) );
	return $buttons;
}
add_filter( 'mce_buttons', 'elfzwo_beitrag_mce_buttons' );

/** Media-Dialog und Zuschneide-Werkzeug auch ohne "Medien hinzufügen"-Button laden. */
function elfzwo_beitrag_editor_assets() {
	if ( ! elfzwo_is_beitrag_editor() ) {
		return;
	}
	wp_enqueue_media();
	wp_enqueue_script( 'imgareaselect' );
	wp_enqueue_style( 'imgareaselect' );
}
add_action( 'admin_enqueue_scripts', 'elfzwo_beitrag_editor_assets' );

/** Fotos im Editor genauso abgerundet zeigen wie auf der Website (.prose img in theme.css). */
function elfzwo_beitrag_mce_content_style( $init ) {
	if ( elfzwo_is_beitrag_editor() ) {
		$style                 = 'img { max-width: 100%; height: auto; border-radius: 1rem; }';
		$init['content_style'] = trim( ( $init['content_style'] ?? '' ) . ' ' . $style );
	}
	return $init;
}
add_filter( 'tiny_mce_before_init', 'elfzwo_beitrag_mce_content_style' );
