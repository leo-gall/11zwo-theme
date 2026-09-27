<?php
/**
 * Registrierung aller eigenen Gutenberg-Blöcke (inc/blocks/*) sowie der
 * Editor-Assets, die sie ohne Build-Step brauchen (Tailwind, Icon-/Personen-
 * Listen als JS-Daten, gemeinsame Editor-Controls).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function elfzwo_block_category( $categories ) {
	return array_merge(
		array(
			array(
				'slug'  => 'elfzwo-blocks',
				'title' => 'Feuerwehr Greifenberg',
				'icon'  => 'shield',
			),
		),
		$categories
	);
}
add_filter( 'block_categories_all', 'elfzwo_block_category' );

function elfzwo_register_blocks() {
	foreach ( glob( get_template_directory() . '/inc/blocks/*/block.json' ) as $block_json ) {
		register_block_type( dirname( $block_json ) );
	}
}
add_action( 'init', 'elfzwo_register_blocks' );

/**
 * Gemeinsames Editor-Script (Icon-/Personen-Picker, Media-Helfer) für alle
 * elfzwo/*-Blöcke. Jeder Block hängt sich per "viewScript"/"editorScript"-
 * Dependency in seiner block.json daran.
 */
function elfzwo_register_block_common_assets() {
	wp_register_script(
		'elfzwo-blocks-common',
		get_template_directory_uri() . '/assets/js/blocks-common.js',
		array( 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n', 'wp-server-side-render' ),
		filemtime( get_template_directory() . '/assets/js/blocks-common.js' ),
		true
	);

	wp_localize_script(
		'elfzwo-blocks-common',
		'elfzwoBlockData',
		array(
			'iconKeys' => elfzwo_icon_keys(),
		)
	);
}
add_action( 'init', 'elfzwo_register_block_common_assets' );

/**
 * Tailwind (CDN + Config) und das Theme-CSS auch im Block-Editor laden,
 * damit jede edit.js mit denselben Utility-Klassen wie das Frontend
 * arbeiten kann — ohne das Design in JS neu zu bauen.
 */
function elfzwo_enqueue_block_editor_styling() {
	wp_enqueue_script( 'tailwind-cdn', 'https://cdn.tailwindcss.com', array(), null, false );
	wp_enqueue_script( 'elfzwo-tailwind-config', get_template_directory_uri() . '/assets/js/tailwind-config.js', array( 'tailwind-cdn' ), filemtime( get_template_directory() . '/assets/js/tailwind-config.js' ), false );
	wp_enqueue_style(
		'elfzwo-google-fonts',
		'https://fonts.googleapis.com/css2?family=Nunito:wght@600;700;800;900&family=Source+Sans+3:wght@400;500;600;700&family=Caveat:wght@500;700&display=swap',
		array(),
		null
	);
	wp_enqueue_style( 'elfzwo-theme', get_template_directory_uri() . '/assets/css/theme.css', array(), filemtime( get_template_directory() . '/assets/css/theme.css' ) );
}
add_action( 'enqueue_block_assets', 'elfzwo_enqueue_block_editor_styling' );

/**
 * Klicks auf interne Links im Editor-Canvas (iframe) sollen das Backend zur
 * Bearbeiten-Ansicht der Zielseite schicken, statt nur das iframe zu
 * navigieren. Das Skript läuft im iframe (enqueue_block_assets wird dafür
 * von WP-Core eigens erneut ausgeführt) und braucht eine Route, die eine
 * Frontend-URL server-seitig auf einen Beitrag/eine Seite auflöst.
 */
function elfzwo_enqueue_editor_link_intercept() {
	if ( ! is_admin() ) {
		return;
	}
	wp_enqueue_script(
		'elfzwo-editor-link-intercept',
		get_template_directory_uri() . '/assets/js/editor-link-intercept.js',
		array(),
		filemtime( get_template_directory() . '/assets/js/editor-link-intercept.js' ),
		true
	);
	wp_localize_script(
		'elfzwo-editor-link-intercept',
		'elfzwoEditorLinkNav',
		array(
			'restUrl' => rest_url( 'elfzwo/v1/resolve-url' ),
			'nonce'   => wp_create_nonce( 'wp_rest' ),
		)
	);
}
add_action( 'enqueue_block_assets', 'elfzwo_enqueue_editor_link_intercept' );

function elfzwo_register_resolve_url_route() {
	register_rest_route(
		'elfzwo/v1',
		'/resolve-url',
		array(
			'methods'             => 'GET',
			'callback'            => 'elfzwo_resolve_url_to_edit_link',
			'permission_callback' => function () {
				return current_user_can( 'edit_posts' );
			},
			'args'                => array(
				'url' => array(
					'required' => true,
					'type'     => 'string',
				),
			),
		)
	);
}
add_action( 'rest_api_init', 'elfzwo_register_resolve_url_route' );

function elfzwo_resolve_url_to_edit_link( WP_REST_Request $request ) {
	$post_id = url_to_postid( $request->get_param( 'url' ) );

	if ( ! $post_id || ! current_user_can( 'edit_post', $post_id ) ) {
		return new WP_REST_Response( array( 'editUrl' => null ), 200 );
	}

	return new WP_REST_Response( array( 'editUrl' => get_edit_post_link( $post_id, 'raw' ) ), 200 );
}
