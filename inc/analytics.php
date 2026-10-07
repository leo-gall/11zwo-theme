<?php
/**
 * Umami Analytics (cookielos, Umami Cloud). Gezählt wird nur auf der echten
 * Domain (data-domains) — lokale Umgebungen und die Staging-Adresse bleiben
 * außen vor — und nicht für angemeldete Redakteure, damit deren eigene
 * Aufrufe die Statistik nicht verfälschen. Eigene Events: assets/js/umami-events.js.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ELFZWO_UMAMI_WEBSITE_ID', 'c499d835-f4a2-4413-9a90-3fd6251b5733' );
define( 'ELFZWO_UMAMI_DOMAINS', 'feuerwehr-greifenberg.info,www.feuerwehr-greifenberg.info' );

function elfzwo_umami_enabled() {
	return ! is_admin() && ! is_user_logged_in() && ! is_customize_preview() && ! is_preview();
}

function elfzwo_enqueue_umami() {
	if ( ! elfzwo_umami_enabled() ) {
		return;
	}
	wp_enqueue_script( 'elfzwo-umami', 'https://cloud.umami.is/script.js', array(), null, array( 'in_footer' => false, 'strategy' => 'defer' ) );
	wp_enqueue_script( 'elfzwo-umami-events', get_template_directory_uri() . '/assets/js/umami-events.js', array(), filemtime( get_template_directory() . '/assets/js/umami-events.js' ), false );
}
add_action( 'wp_enqueue_scripts', 'elfzwo_enqueue_umami' );

function elfzwo_umami_script_attributes( $tag, $handle ) {
	if ( 'elfzwo-umami' !== $handle ) {
		return $tag;
	}
	return str_replace(
		' src=',
		sprintf( ' data-website-id="%s" data-domains="%s" src=', esc_attr( ELFZWO_UMAMI_WEBSITE_ID ), esc_attr( ELFZWO_UMAMI_DOMAINS ) ),
		$tag
	);
}
add_filter( 'script_loader_tag', 'elfzwo_umami_script_attributes', 10, 2 );
