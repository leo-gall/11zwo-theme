<?php
/**
 * Fest im Code hinterlegte Kategorien für Beiträge (category) und Downloads
 * (download_kategorie). Beide Taxonomien lassen sich im Backend weder
 * erweitern, umbenennen noch löschen; zugewiesen wird nur über das eigene
 * Kategorie-Feld im Formular (siehe elfzwo_meta_box_schemas()), nicht über
 * die Seitenleiste. elfzwo_sync_kategorien() gleicht die Begriffe in der
 * Datenbank an, sobald sich die Liste ändert.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Feste Kategorien je Taxonomie in Anzeigereihenfolge; die erste ist die Standard-Kategorie. */
function elfzwo_kategorien( $taxonomy ) {
	if ( 'download_kategorie' === $taxonomy ) {
		return array( 'Allgemein', 'Aktive Mannschaft', 'Bürgerinformationen', 'Jugend', 'Verein' );
	}
	return array( 'Allgemein', 'Bürgerinformationen', 'Einsatzbericht', 'Jugendfeuerwehr', 'Kinderfeuerwehr', 'Verein' );
}

/** Alte Begriffe, die beim Abgleich in eine bestimmte neue Kategorie wandern (sonst "Allgemein"). */
function elfzwo_kategorien_umzug( $taxonomy ) {
	if ( 'download_kategorie' === $taxonomy ) {
		return array(
			'Jugendfeuerwehr' => 'Jugend',
			'Kinderfeuerwehr' => 'Jugend',
			'Einsatzbericht'  => 'Aktive Mannschaft',
		);
	}
	return array();
}

/** Taxonomien, für die die feste Liste gilt. */
function elfzwo_kategorien_taxonomies() {
	return array( 'category', 'download_kategorie' );
}

function elfzwo_kategorien_taxonomy_args( $args, $taxonomy ) {
	if ( ! in_array( $taxonomy, elfzwo_kategorien_taxonomies(), true ) ) {
		return $args;
	}
	$args['show_in_quick_edit'] = false;
	$args['meta_box_cb']        = false;
	$args['capabilities']       = array(
		'manage_terms' => 'do_not_allow',
		'edit_terms'   => 'do_not_allow',
		'delete_terms' => 'do_not_allow',
		'assign_terms' => 'edit_posts',
	);
	return $args;
}
add_filter( 'register_taxonomy_args', 'elfzwo_kategorien_taxonomy_args', 10, 2 );

/** Begriffe der festen Liste einer Taxonomie in Listenreihenfolge (Name => WP_Term). */
function elfzwo_kategorien_terms( $taxonomy ) {
	$terms = array();
	foreach ( elfzwo_kategorien( $taxonomy ) as $name ) {
		$term = get_term_by( 'name', $name, $taxonomy );
		if ( $term ) {
			$terms[ $name ] = $term;
		}
	}
	return $terms;
}

function elfzwo_kategorie_is_valid_term( $term_id, $taxonomy ) {
	$term = get_term( (int) $term_id, $taxonomy );
	return $term && ! is_wp_error( $term ) && in_array( wp_specialchars_decode( $term->name, ENT_QUOTES ), elfzwo_kategorien( $taxonomy ), true );
}

/**
 * Legt fehlende Kategorien an; Beiträge/Downloads in Kategorien, die nicht
 * (mehr) in der Liste stehen, wandern laut elfzwo_kategorien_umzug() bzw.
 * nach "Allgemein", die alten Begriffe werden gelöscht.
 */
function elfzwo_sync_kategorien() {
	$all = array();
	foreach ( elfzwo_kategorien_taxonomies() as $taxonomy ) {
		$all[ $taxonomy ] = array( elfzwo_kategorien( $taxonomy ), elfzwo_kategorien_umzug( $taxonomy ) );
	}
	$version = md5( wp_json_encode( $all ) );
	if ( get_option( 'elfzwo_kategorien_version' ) === $version ) {
		return;
	}
	if ( get_transient( 'elfzwo_kategorien_sync_lock' ) ) {
		return;
	}
	set_transient( 'elfzwo_kategorien_sync_lock', 1, 5 * MINUTE_IN_SECONDS );

	foreach ( elfzwo_kategorien_taxonomies() as $taxonomy ) {
		$wanted = elfzwo_kategorien( $taxonomy );
		$umzug  = elfzwo_kategorien_umzug( $taxonomy );
		$ids    = array();
		foreach ( $wanted as $name ) {
			$term = get_term_by( 'name', $name, $taxonomy );
			if ( ! $term ) {
				$result = wp_insert_term( $name, $taxonomy );
				$term   = is_wp_error( $result ) ? null : get_term( $result['term_id'], $taxonomy );
			}
			if ( $term && ! is_wp_error( $term ) ) {
				$ids[ $name ] = (int) $term->term_id;
			}
		}
		if ( empty( $ids[ $wanted[0] ] ) ) {
			continue;
		}
		$fallback_id = $ids[ $wanted[0] ];
		if ( 'category' === $taxonomy ) {
			// Die Standard-Kategorie kann WordPress nicht löschen — vorher umstellen.
			update_option( 'default_category', $fallback_id );
		}

		$existing = get_terms( array( 'taxonomy' => $taxonomy, 'hide_empty' => false ) );
		foreach ( is_wp_error( $existing ) ? array() : $existing as $term ) {
			if ( in_array( (int) $term->term_id, $ids, true ) ) {
				continue;
			}
			$name      = wp_specialchars_decode( $term->name, ENT_QUOTES );
			$target_id = isset( $umzug[ $name ], $ids[ $umzug[ $name ] ] ) ? $ids[ $umzug[ $name ] ] : $fallback_id;
			$objects   = get_objects_in_term( $term->term_id, $taxonomy );
			foreach ( is_wp_error( $objects ) ? array() : $objects as $object_id ) {
				wp_add_object_terms( (int) $object_id, $target_id, $taxonomy );
			}
			wp_delete_term( $term->term_id, $taxonomy );
		}
		clean_taxonomy_cache( $taxonomy );
	}

	update_option( 'elfzwo_kategorien_version', $version );
	delete_transient( 'elfzwo_kategorien_sync_lock' );
}
add_action( 'init', 'elfzwo_sync_kategorien', 20 );
