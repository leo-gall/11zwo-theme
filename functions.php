<?php
/**
 * Theme setup for Feuerwehr Greifenberg.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function elfzwo_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption' ) );
	add_theme_support( 'custom-logo' );

	register_nav_menus(
		array(
			'primary'           => __( 'Hauptmenü', '11zwo' ),
			'footer-ueber-uns'  => __( 'Footer: Über uns', '11zwo' ),
			'footer-sonstige'   => __( 'Footer: Sonstige', '11zwo' ),
		)
	);
}
add_action( 'after_setup_theme', 'elfzwo_setup' );

/**
 * Menü-Ort "footer-einsatz" wurde in "footer-sonstige" umbenannt: bestehende
 * Zuordnung (und den Namen des zugeordneten Menüs) einmalig übernehmen.
 */
function elfzwo_migrate_footer_sonstige_location() {
	$locations = get_theme_mod( 'nav_menu_locations', array() );
	if ( ! isset( $locations['footer-einsatz'] ) ) {
		return;
	}
	$menu_id = (int) $locations['footer-einsatz'];
	if ( empty( $locations['footer-sonstige'] ) ) {
		$locations['footer-sonstige'] = $menu_id;
	}
	unset( $locations['footer-einsatz'] );
	set_theme_mod( 'nav_menu_locations', $locations );

	$menu = wp_get_nav_menu_object( $menu_id );
	if ( $menu && 'Footer: Einsatz' === $menu->name ) {
		wp_update_nav_menu_object( $menu_id, array( 'menu-name' => 'Footer: Sonstige' ) );
	}
}
add_action( 'after_setup_theme', 'elfzwo_migrate_footer_sonstige_location', 20 );

function elfzwo_enqueue_assets() {
	// Tailwind Play CDN — kein Build-Schritt im Theme vorhanden, siehe README-Hinweis im Repo.
	wp_enqueue_script( 'tailwind-cdn', 'https://cdn.tailwindcss.com', array(), null, false );

	wp_enqueue_style(
		'elfzwo-google-fonts',
		'https://fonts.googleapis.com/css2?family=Nunito:wght@600;700;800;900&family=Source+Sans+3:wght@400;500;600;700&family=Caveat:wght@500;700&display=swap',
		array(),
		null
	);

	wp_enqueue_style( 'elfzwo-style', get_stylesheet_uri(), array(), filemtime( get_stylesheet_directory() . '/style.css' ) );
	wp_enqueue_style( 'elfzwo-theme', get_template_directory_uri() . '/assets/css/theme.css', array(), filemtime( get_template_directory() . '/assets/css/theme.css' ) );

	wp_enqueue_script( 'elfzwo-tailwind-config', get_template_directory_uri() . '/assets/js/tailwind-config.js', array( 'tailwind-cdn' ), filemtime( get_template_directory() . '/assets/js/tailwind-config.js' ), false );
	wp_enqueue_script( 'elfzwo-nav', get_template_directory_uri() . '/assets/js/nav.js', array(), filemtime( get_template_directory() . '/assets/js/nav.js' ), true );

	if ( has_block( 'elfzwo/aktuelle-einsaetze' ) ) {
		wp_enqueue_script( 'elfzwo-einsatzliste', get_template_directory_uri() . '/assets/js/einsatzliste.js', array(), filemtime( get_template_directory() . '/assets/js/einsatzliste.js' ), true );
		wp_localize_script( 'elfzwo-einsatzliste', 'elfzwoEinsatzliste', array( 'ajaxUrl' => admin_url( 'admin-ajax.php' ) ) );
	}

	if ( has_block( 'elfzwo/aktuelles-feed' ) ) {
		wp_enqueue_script( 'elfzwo-aktuelles-feed', get_template_directory_uri() . '/assets/js/aktuelles-feed.js', array(), filemtime( get_template_directory() . '/assets/js/aktuelles-feed.js' ), true );
		wp_localize_script( 'elfzwo-aktuelles-feed', 'elfzwoAktuellesFeed', array( 'ajaxUrl' => admin_url( 'admin-ajax.php' ) ) );
	}

	if ( has_block( 'elfzwo/faq' ) ) {
		wp_enqueue_script( 'elfzwo-faq-accordion', get_template_directory_uri() . '/assets/js/faq-accordion.js', array(), filemtime( get_template_directory() . '/assets/js/faq-accordion.js' ), true );
	}

	if ( has_block( 'elfzwo/section-heading' ) ) {
		wp_enqueue_script( 'elfzwo-nina-warnungen', get_template_directory_uri() . '/assets/js/nina-warnungen.js', array(), filemtime( get_template_directory() . '/assets/js/nina-warnungen.js' ), true );
	}
}
add_action( 'wp_enqueue_scripts', 'elfzwo_enqueue_assets' );

require get_template_directory() . '/inc/icons.php';
require get_template_directory() . '/inc/helpers.php';
require get_template_directory() . '/inc/person-wappen.php';
require get_template_directory() . '/inc/post-types.php';
require get_template_directory() . '/inc/meta-boxes.php';
require get_template_directory() . '/inc/einsatzstichwoerter.php';
require get_template_directory() . '/inc/einsatz-nummern.php';
require get_template_directory() . '/inc/settings.php';
require get_template_directory() . '/inc/nina.php';
require get_template_directory() . '/inc/einsatzliste.php';
require get_template_directory() . '/inc/aktuelles-feed.php';
require get_template_directory() . '/inc/einsatz-card.php';
require get_template_directory() . '/inc/nav-walker.php';
require get_template_directory() . '/inc/mitmachen-handler.php';
require get_template_directory() . '/inc/blocks.php';
require get_template_directory() . '/inc/comments.php';

/**
 * Beim Aktivieren des Themes: Rewrite-Regeln für die neuen Post-Types
 * neu einlesen, damit deren URLs sofort funktionieren.
 */
function elfzwo_flush_rewrites() {
	elfzwo_register_post_types();
	elfzwo_register_taxonomies();
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'elfzwo_flush_rewrites' );

/**
 * Menüpunkt "Mannschaft"-Icon etc. für die Post-Types im Adminmenü.
 */
function elfzwo_wp_nav_menu_args( $args ) {
	if ( 'primary' === $args['theme_location'] ) {
		$args['container'] = false;
	}
	return $args;
}
add_filter( 'wp_nav_menu_args', 'elfzwo_wp_nav_menu_args' );
