<?php
/**
 * Custom Post Types & Taxonomien für die Feuerwehr-CMS-Inhalte.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function elfzwo_register_post_types() {

	register_post_type(
		'fahrzeug',
		array(
			'labels'       => array(
				'name'          => 'Fahrzeuge',
				'singular_name' => 'Fahrzeug',
				'add_new_item'  => 'Neues Fahrzeug',
				'edit_item'     => 'Fahrzeug bearbeiten',
				'all_items'     => 'Alle Fahrzeuge',
			),
			'public'              => false,
			'publicly_queryable'  => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => false,
			'has_archive'         => false,
			'show_in_rest'        => true,
			'rewrite'             => false,
			'menu_icon'           => 'dashicons-car',
			'supports'            => array( 'title', 'editor', 'thumbnail' ),
		)
	);

	register_post_type(
		'einsatz',
		array(
			'labels'       => array(
				'name'          => 'Einsätze',
				'singular_name' => 'Einsatz',
				'add_new_item'  => 'Neuer Einsatz',
				'edit_item'     => 'Einsatz bearbeiten',
				'all_items'     => 'Alle Einsätze',
			),
			'public'       => true,
			'has_archive'  => false,
			'show_in_rest' => true,
			'rewrite'      => array( 'slug' => 'einsatz' ),
			'menu_icon'    => 'dashicons-sos',
			'supports'     => array( 'editor' ),
		)
	);

	register_post_type(
		'termin',
		array(
			'labels'       => array(
				'name'          => 'Termine',
				'singular_name' => 'Termin',
				'add_new_item'  => 'Neuer Termin',
				'edit_item'     => 'Termin bearbeiten',
				'all_items'     => 'Alle Termine',
			),
			'public'       => true,
			'show_ui'      => false,
			'has_archive'  => false,
			'show_in_rest' => true,
			'rewrite'      => array( 'slug' => 'termin' ),
			'menu_icon'    => 'dashicons-calendar-alt',
			'supports'     => array( 'title', 'editor', 'thumbnail' ),
		)
	);

	register_post_type(
		'download',
		array(
			'labels'       => array(
				'name'          => 'Downloads',
				'singular_name' => 'Download',
				'add_new_item'  => 'Neuer Download',
				'edit_item'     => 'Download bearbeiten',
				'all_items'     => 'Alle Downloads',
			),
			'public'       => true,
			'has_archive'  => false,
			'show_in_rest' => true,
			'rewrite'      => array( 'slug' => 'download' ),
			'menu_icon'    => 'dashicons-media-default',
			'supports'     => array( 'title' ),
		)
	);

	register_post_type(
		'externe_kraft',
		array(
			'labels'              => array(
				'name'          => 'Externe Kräfte',
				'singular_name' => 'Externe Kraft',
				'add_new_item'  => 'Neue externe Kraft',
				'edit_item'     => 'Externe Kraft bearbeiten',
				'all_items'     => 'Alle externen Kräfte',
			),
			'public'              => false,
			'publicly_queryable'  => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => false,
			'has_archive'         => false,
			'show_in_rest'        => true,
			'rewrite'             => false,
			'menu_icon'           => 'dashicons-networking',
			'supports'            => array( 'title' ),
		)
	);

}
add_action( 'init', 'elfzwo_register_post_types' );

/**
 * Einsätze, Fahrzeuge, Downloads und Beiträge werden im klassischen Editor
 * statt im Block-Editor bearbeitet: so erscheinen Titel, Text und die
 * Zusatzfelder (Datum, Ort, Fahrzeuge, Datei, Kicker, …) alle auf einem
 * durchgehenden Formular, statt dass sie als separates, erst unten zu
 * findendes Kompatibilitäts-Panel unter dem Block-Editor auftauchen.
 */
function elfzwo_disable_block_editor_for_form_types( $use_block_editor, $post_type ) {
	if ( in_array( $post_type, array( 'einsatz', 'fahrzeug', 'download', 'post', 'externe_kraft' ), true ) ) {
		return false;
	}
	return $use_block_editor;
}
add_filter( 'use_block_editor_for_post_type', 'elfzwo_disable_block_editor_for_form_types', 10, 2 );

function elfzwo_register_taxonomies() {

	register_taxonomy(
		'einsatzstichwort',
		'einsatz',
		array(
			'labels'            => array(
				'name'          => 'Einsatzstichwörter',
				'singular_name' => 'Einsatzstichwort',
				'all_items'     => 'Alle Einsatzstichwörter',
				'edit_item'     => 'Einsatzstichwort bearbeiten',
				'add_new_item'  => 'Neues Einsatzstichwort',
				'parent_item'   => 'Übergeordnete Gruppe',
			),
			'public'            => true,
			'show_in_rest'      => true,
			'hierarchical'      => true,
			'show_admin_column' => true,
			'rewrite'           => array( 'slug' => 'einsatzstichwort' ),
		)
	);

	register_taxonomy(
		'einsatzort',
		'einsatz',
		array(
			'labels'            => array(
				'name'          => 'Einsatzorte',
				'singular_name' => 'Einsatzort',
				'all_items'     => 'Alle Einsatzorte',
				'edit_item'     => 'Einsatzort bearbeiten',
				'add_new_item'  => 'Neuer Einsatzort',
			),
			'public'            => true,
			'show_in_rest'      => true,
			'hierarchical'      => false,
			'show_admin_column' => true,
			'rewrite'           => array( 'slug' => 'einsatzort' ),
		)
	);

	register_taxonomy(
		'download_kategorie',
		'download',
		array(
			'labels'            => array(
				'name'          => 'Download-Kategorien',
				'singular_name' => 'Download-Kategorie',
			),
			'public'            => true,
			'show_in_rest'      => true,
			'hierarchical'      => false,
			'rewrite'           => array( 'slug' => 'download-kategorie' ),
		)
	);

	register_taxonomy(
		'termin_kategorie',
		'termin',
		array(
			'labels'            => array(
				'name'          => 'Termin-Kategorien',
				'singular_name' => 'Termin-Kategorie',
			),
			'public'            => true,
			'show_in_rest'      => true,
			'hierarchical'      => true,
			'rewrite'           => array( 'slug' => 'termin-kategorie' ),
		)
	);
}
add_action( 'init', 'elfzwo_register_taxonomies' );

/**
 * Die native "Einsatzorte"-Tag-Box im Einsatz-Formular erlaubt freies
 * Anlegen neuer Orte per Text und dupliziert damit die kuratierte Auswahl
 * im eigenen "Einsatzort"-Feld (taxonomy_select) — daher hier entfernt.
 * Die Taxonomie selbst bleibt unverändert nutzbar.
 */
function elfzwo_remove_native_einsatzort_metabox() {
	remove_meta_box( 'tagsdiv-einsatzort', 'einsatz', 'side' );
}
add_action( 'add_meta_boxes', 'elfzwo_remove_native_einsatzort_metabox' );

/**
 * Diese Taxonomien sollen im Backend nur aus einem Namen (und ggf. der
 * Gruppierung) bestehen — Titelform (Slug) und/oder Beschreibungsfeld
 * werden auf der jeweiligen Verwaltungsseite ausgeblendet, da sie im
 * Frontend nie genutzt werden.
 */
function elfzwo_simplify_taxonomy_admin_fields() {
	$hidden_fields = array(
		'download_kategorie' => array( 'slug', 'description' ),
		'einsatzstichwort'    => array( 'description' ),
		'einsatzort'          => array( 'slug', 'description' ),
	);

	$taxonomy = $_GET['taxonomy'] ?? '';
	if ( ! isset( $hidden_fields[ $taxonomy ] ) ) {
		return;
	}

	$selectors = array();
	foreach ( $hidden_fields[ $taxonomy ] as $field ) {
		if ( 'slug' === $field ) {
			$selectors[] = '.term-slug-wrap';
			$selectors[] = '#tag-slug';
		} elseif ( 'description' === $field ) {
			$selectors[] = '.term-description-wrap';
			$selectors[] = '#tag-description';
		}
	}

	printf( '<style>%s { display: none !important; }</style>', implode( ',', $selectors ) );
}
add_action( 'admin_head-edit-tags.php', 'elfzwo_simplify_taxonomy_admin_fields' );
add_action( 'admin_head-term.php', 'elfzwo_simplify_taxonomy_admin_fields' );

/**
 * Backend-Liste der Einsätze nach Einsatz-ID (Jahr + Nummer, neuestes
 * zuerst) statt nach dem nativen post_date sortieren -- post_date zeigt nur,
 * wann der Beitrag im CMS angelegt wurde, nicht den Einsatzzeitpunkt. Der
 * Sortierschlüssel wird in elfzwo_einsatz_renumber_year()
 * (inc/einsatz-nummern.php) gepflegt. Gilt als Standard und beim Klick auf
 * die Spalte "Einsatz-ID".
 */
function elfzwo_einsatz_admin_default_order( $query ) {
	if ( ! is_admin() || ! $query->is_main_query() ) {
		return;
	}
	if ( 'einsatz' !== $query->get( 'post_type' ) ) {
		return;
	}
	$orderby = $query->get( 'orderby' );
	if ( $orderby && 'einsatz_id' !== $orderby ) {
		return;
	}
	$query->set( 'orderby', 'meta_value' );
	$query->set( 'meta_key', '_elfzwo_sort_key' );
	$query->set( 'order', $orderby ? ( 'asc' === strtolower( (string) $query->get( 'order' ) ) ? 'ASC' : 'DESC' ) : 'DESC' );
}
add_action( 'pre_get_posts', 'elfzwo_einsatz_admin_default_order' );
