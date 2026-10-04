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
	wp_enqueue_script( 'elfzwo-umami-events', get_template_directory_uri() . '/assets/js/umami-events.js', array(), filemtime( get_template_directory() . '/assets/js/umami-events.js' ), true );
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

/**
 * Abschnitt zu Umami für die Datenschutzerklärung. Steht vor "Weitergabe von
 * Daten an Dritte", sonst am Ende; ist Umami schon erwähnt, bleibt der Text
 * unverändert.
 */
function elfzwo_datenschutz_mit_umami( $content ) {
	if ( false !== stripos( $content, 'umami' ) ) {
		return $content;
	}
	$abschnitt = "<h3>Webanalyse mit Umami</h3>\n"
		. "<p>Wir nutzen Umami Cloud (Umami Software, Inc.), um anonym zu zählen, welche Seiten wie oft aufgerufen werden. Die Verarbeitung erfolgt auf Servern in der EU. Umami setzt keine Cookies und speichert weder IP-Adressen noch sonstige personenbezogene Daten dauerhaft. Beim Laden des Skripts wird Ihre IP-Adresse technisch bedingt an den Server von Umami übertragen. Rechtsgrundlage ist unser berechtigtes Interesse an einer bedarfsgerechten Gestaltung unserer Website (Art. 6 Abs. 1 lit. f DSGVO).</p>\n\n";
	if ( has_blocks( $content ) ) {
		$abschnitt = "<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">Webanalyse mit Umami</h3>\n<!-- /wp:heading -->\n\n"
			. "<!-- wp:paragraph -->\n" . substr( $abschnitt, strpos( $abschnitt, '<p>' ), -2 ) . "\n<!-- /wp:paragraph -->\n\n";
	}
	if ( preg_match( '#(<!-- wp:heading[^>]*-->\s*)?<h[2-4][^>]*>\s*Weitergabe von Daten#i', $content, $m, PREG_OFFSET_CAPTURE ) ) {
		return substr( $content, 0, $m[0][1] ) . $abschnitt . substr( $content, $m[0][1] );
	}
	return rtrim( $content ) . "\n\n" . $abschnitt;
}

function elfzwo_datenschutz_umami_ergaenzen() {
	if ( get_option( 'elfzwo_datenschutz_umami' ) ) {
		return;
	}
	update_option( 'elfzwo_datenschutz_umami', 1 );
	$page = get_post( (int) get_option( 'wp_page_for_privacy_policy' ) );
	foreach ( array( 'datenschutzerklaerung', 'datenschutzerklarung', 'datenschutz' ) as $slug ) {
		if ( $page && 'publish' === $page->post_status ) {
			break;
		}
		$page = get_page_by_path( $slug );
	}
	if ( ! $page || 'publish' !== $page->post_status ) {
		return;
	}
	$neu = elfzwo_datenschutz_mit_umami( $page->post_content );
	if ( $neu !== $page->post_content ) {
		_wp_put_post_revision( $page );
		wp_update_post( array( 'ID' => $page->ID, 'post_content' => wp_slash( $neu ) ) );
	}
}
add_action( 'init', 'elfzwo_datenschutz_umami_ergaenzen', 50 );
