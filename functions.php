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
 * WordPress-Emoji-Skript abschalten: Es lädt Grafiken von s.w.org und
 * überträgt damit die IP-Adresse der Besucher an Dritte. Alle Zielbrowser
 * stellen Emojis selbst dar.
 */
function elfzwo_disable_emojis() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'embed_head', 'print_emoji_detection_script' );
	remove_action( 'wp_enqueue_scripts', 'wp_enqueue_emoji_styles' );
	remove_action( 'admin_enqueue_scripts', 'wp_enqueue_emoji_styles' );
	remove_action( 'enqueue_embed_scripts', 'wp_enqueue_emoji_styles' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	add_filter( 'emoji_svg_url', '__return_false' );
}
add_action( 'init', 'elfzwo_disable_emojis' );

function elfzwo_enqueue_assets() {
	wp_enqueue_style( 'elfzwo-style', get_stylesheet_uri(), array(), filemtime( get_stylesheet_directory() . '/style.css' ) );
	wp_enqueue_style( 'elfzwo-theme', get_template_directory_uri() . '/assets/css/theme.css', array(), filemtime( get_template_directory() . '/assets/css/theme.css' ) );
	// Nach theme.css, damit Utility-Klassen die Basis-Styles überschreiben.
	wp_enqueue_style( 'elfzwo-tailwind', get_template_directory_uri() . '/assets/css/tailwind.css', array( 'elfzwo-theme' ), filemtime( get_template_directory() . '/assets/css/tailwind.css' ) );

	wp_enqueue_script( 'elfzwo-nav', get_template_directory_uri() . '/assets/js/nav.js', array(), filemtime( get_template_directory() . '/assets/js/nav.js' ), true );

	if ( has_block( 'elfzwo/aktuelle-einsaetze' ) ) {
		wp_enqueue_script( 'elfzwo-einsaetze', get_template_directory_uri() . '/assets/js/einsaetze.js', array(), filemtime( get_template_directory() . '/assets/js/einsaetze.js' ), true );
		wp_localize_script( 'elfzwo-einsaetze', 'elfzwoEinsaetze', array( 'ajaxUrl' => admin_url( 'admin-ajax.php' ) ) );
	}

	if ( has_block( 'elfzwo/aktuelles-feed' ) ) {
		wp_enqueue_script( 'elfzwo-aktuelles-feed', get_template_directory_uri() . '/assets/js/aktuelles-feed.js', array(), filemtime( get_template_directory() . '/assets/js/aktuelles-feed.js' ), true );
		wp_localize_script( 'elfzwo-aktuelles-feed', 'elfzwoAktuellesFeed', array( 'ajaxUrl' => admin_url( 'admin-ajax.php' ) ) );
	}

	if ( has_block( 'elfzwo/mitmachen-form' ) ) {
		wp_enqueue_script( 'elfzwo-mitmachen-form', get_template_directory_uri() . '/assets/js/mitmachen-form.js', array(), filemtime( get_template_directory() . '/assets/js/mitmachen-form.js' ), true );
	}

	if ( has_block( 'elfzwo/fahrzeuge-liste' ) ) {
		wp_enqueue_script( 'elfzwo-fahrzeug-hotspots', get_template_directory_uri() . '/assets/js/fahrzeug-hotspots.js', array(), filemtime( get_template_directory() . '/assets/js/fahrzeug-hotspots.js' ), true );
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
require get_template_directory() . '/inc/komponenten.php';
require get_template_directory() . '/inc/person-wappen.php';
require get_template_directory() . '/inc/post-types.php';
require get_template_directory() . '/inc/meta-boxes.php';
require get_template_directory() . '/inc/beitrag-editor.php';
require get_template_directory() . '/inc/einsatzstichwoerter.php';
require get_template_directory() . '/inc/kategorien.php';
require get_template_directory() . '/inc/einsatz-nummern.php';
require get_template_directory() . '/inc/settings.php';
require get_template_directory() . '/inc/analytics.php';
require get_template_directory() . '/inc/nina.php';
require get_template_directory() . '/inc/einsatzliste.php';
require get_template_directory() . '/inc/aktuelles-feed.php';
require get_template_directory() . '/inc/einsatz-card.php';
require get_template_directory() . '/inc/nav-walker.php';
require get_template_directory() . '/inc/mitmachen-handler.php';
require get_template_directory() . '/inc/blocks.php';
require get_template_directory() . '/inc/comments.php';

/**
 * Beim Aktivieren des Themes: Rewrite-Regeln für die eigenen Post-Types
 * neu einlesen, damit deren URLs sofort funktionieren.
 */
function elfzwo_flush_rewrites() {
	elfzwo_register_post_types();
	elfzwo_register_taxonomies();
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'elfzwo_flush_rewrites' );

/** Hauptmenü ohne umschließenden Container ausgeben. */
function elfzwo_wp_nav_menu_args( $args ) {
	if ( 'primary' === $args['theme_location'] ) {
		$args['container'] = false;
	}
	return $args;
}
add_filter( 'wp_nav_menu_args', 'elfzwo_wp_nav_menu_args' );
