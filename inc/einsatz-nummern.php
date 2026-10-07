<?php
/**
 * Einsatz-ID (Jahr/Nr.): Die Nummer ergibt sich immer aus der zeitlichen
 * Reihenfolge der Einsätze eines Jahres. Wird ein Einsatz nachträglich in
 * der Vergangenheit erfasst, verschoben oder gelöscht, wird das ganze Jahr
 * neu durchnummeriert. Öffentliche URL: /einsatz/{jahr}/{nr}/.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Stand der automatischen Migration (bei Änderungen am Schema hochzählen). */
define( 'ELFZWO_EINSATZ_SCHEMA', 1 );

/** Status, die bei der Nummerierung mitzählen (alles außer Papierkorb/Auto-Entwurf). */
function elfzwo_einsatz_nummer_statuses() {
	return array( 'publish', 'future', 'draft', 'pending', 'private' );
}

/**
 * Zeitpunkt eines Einsatzes als vergleichbarer String (Y-m-d\TH:i).
 * Ohne erfasstes Datum zählt das Anlagedatum des Beitrags.
 */
function elfzwo_einsatz_zeitpunkt( $post_id ) {
	$datum = elfzwo_meta( $post_id, 'datum', '' );
	if ( $datum && strtotime( $datum ) ) {
		return gmdate( 'Y-m-d\TH:i', strtotime( $datum ) );
	}
	return get_post_time( 'Y-m-d\TH:i', false, $post_id );
}

function elfzwo_einsatz_jahr( $post_id ) {
	return substr( elfzwo_einsatz_zeitpunkt( $post_id ), 0, 4 );
}

/** Einsatz-ID als "2026/33", leer wenn (noch) keine Nummer vergeben ist. */
function elfzwo_einsatz_id( $post_id ) {
	$nummer = (int) elfzwo_meta( $post_id, 'einsatznummer', 0 );
	return $nummer ? elfzwo_einsatz_jahr( $post_id ) . '/' . $nummer : '';
}

/**
 * Nummeriert alle Einsätze eines Jahres chronologisch neu (1, 2, 3, …) und
 * pflegt den Sortierschlüssel für die Backend-Liste und die URL-Auflösung.
 */
function elfzwo_einsatz_renumber_year( $jahr ) {
	$jahr = (string) $jahr;
	if ( ! preg_match( '/^\d{4}$/', $jahr ) ) {
		return;
	}

	$ids = get_posts(
		array(
			'post_type'        => 'einsatz',
			'post_status'      => elfzwo_einsatz_nummer_statuses(),
			'posts_per_page'   => -1,
			'fields'           => 'ids',
			'suppress_filters' => true,
		)
	);

	$einsaetze = array();
	foreach ( $ids as $id ) {
		$zeitpunkt = elfzwo_einsatz_zeitpunkt( $id );
		if ( substr( $zeitpunkt, 0, 4 ) === $jahr ) {
			$einsaetze[] = array( 'id' => (int) $id, 'zeitpunkt' => $zeitpunkt );
		}
	}

	usort(
		$einsaetze,
		function ( $a, $b ) {
			return strcmp( $a['zeitpunkt'], $b['zeitpunkt'] ) ?: $a['id'] - $b['id'];
		}
	);

	foreach ( $einsaetze as $i => $e ) {
		$nummer = $i + 1;
		if ( (int) elfzwo_meta( $e['id'], 'einsatznummer', 0 ) !== $nummer ) {
			update_post_meta( $e['id'], '_elfzwo_einsatznummer', $nummer );
		}
		$sort_key = sprintf( '%04d%05d', (int) $jahr, $nummer );
		if ( get_post_meta( $e['id'], '_elfzwo_sort_key', true ) !== $sort_key ) {
			update_post_meta( $e['id'], '_elfzwo_sort_key', $sort_key );
		}
	}
}

/**
 * Nach jedem Speichern: Jahr des Einsatzes neu nummerieren – und das
 * vorherige Jahr, falls das Datum in ein anderes Jahr verschoben wurde.
 * Läuft nach dem Speichern der Meta-Felder (Priorität 10) und vor der
 * Titel-/Slug-Erzeugung (Priorität 20).
 */
function elfzwo_einsatz_renumber_on_save( $post_id, $post ) {
	if ( 'einsatz' !== $post->post_type ) {
		return;
	}
	if ( wp_is_post_revision( $post_id ) || ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ) {
		return;
	}
	if ( 'auto-draft' === $post->post_status ) {
		return;
	}

	$alter_key  = get_post_meta( $post_id, '_elfzwo_sort_key', true );
	$altes_jahr = $alter_key ? substr( $alter_key, 0, 4 ) : '';
	$jahr       = elfzwo_einsatz_jahr( $post_id );

	elfzwo_einsatz_renumber_year( $jahr );
	if ( $altes_jahr && $altes_jahr !== $jahr ) {
		elfzwo_einsatz_renumber_year( $altes_jahr );
	}
}
add_action( 'save_post', 'elfzwo_einsatz_renumber_on_save', 15, 2 );

/** Papierkorb, Wiederherstellen und endgültiges Löschen verschieben die Nummern ebenfalls. */
function elfzwo_einsatz_renumber_on_status_change( $post_id ) {
	if ( 'einsatz' === get_post_type( $post_id ) ) {
		elfzwo_einsatz_renumber_year( elfzwo_einsatz_jahr( $post_id ) );
	}
}
add_action( 'trashed_post', 'elfzwo_einsatz_renumber_on_status_change' );
add_action( 'untrashed_post', 'elfzwo_einsatz_renumber_on_status_change' );

function elfzwo_einsatz_renumber_after_delete( $post_id, $post ) {
	if ( $post && 'einsatz' === $post->post_type ) {
		$key = get_post_meta( $post_id, '_elfzwo_sort_key', true );
		$GLOBALS['elfzwo_einsatz_deleted_year'] = $key ? substr( $key, 0, 4 ) : elfzwo_einsatz_jahr( $post_id );
	}
}
add_action( 'before_delete_post', 'elfzwo_einsatz_renumber_after_delete', 10, 2 );

function elfzwo_einsatz_renumber_deleted_year() {
	if ( ! empty( $GLOBALS['elfzwo_einsatz_deleted_year'] ) ) {
		elfzwo_einsatz_renumber_year( $GLOBALS['elfzwo_einsatz_deleted_year'] );
		unset( $GLOBALS['elfzwo_einsatz_deleted_year'] );
	}
}
add_action( 'deleted_post', 'elfzwo_einsatz_renumber_deleted_year' );

/* ---------------------------------------------------------------- URLs */

function elfzwo_einsatz_rewrite_rules() {
	add_rewrite_rule( '^einsatz/(\d{4})/(\d+)/?$', 'index.php?elfzwo_einsatz_jahr=$matches[1]&elfzwo_einsatz_nr=$matches[2]', 'top' );
}
add_action( 'init', 'elfzwo_einsatz_rewrite_rules' );

function elfzwo_einsatz_query_vars( $vars ) {
	$vars[] = 'elfzwo_einsatz_jahr';
	$vars[] = 'elfzwo_einsatz_nr';
	return $vars;
}
add_filter( 'query_vars', 'elfzwo_einsatz_query_vars' );

/** Löst /einsatz/{jahr}/{nr}/ über den Sortierschlüssel auf den Einsatz auf. */
function elfzwo_einsatz_resolve_request( $vars ) {
	if ( empty( $vars['elfzwo_einsatz_jahr'] ) || empty( $vars['elfzwo_einsatz_nr'] ) ) {
		return $vars;
	}
	$sort_key = sprintf( '%04d%05d', (int) $vars['elfzwo_einsatz_jahr'], (int) $vars['elfzwo_einsatz_nr'] );
	$ids      = get_posts(
		array(
			'post_type'        => 'einsatz',
			'post_status'      => elfzwo_einsatz_nummer_statuses(),
			'posts_per_page'   => 1,
			'fields'           => 'ids',
			'meta_key'         => '_elfzwo_sort_key',
			'meta_value'       => $sort_key,
			'suppress_filters' => true,
		)
	);
	unset( $vars['elfzwo_einsatz_jahr'], $vars['elfzwo_einsatz_nr'] );
	$vars['post_type'] = 'einsatz';
	if ( $ids ) {
		$vars['p'] = (int) $ids[0];
	} else {
		// Unbekannte Einsatz-ID: leere Einzelabfrage, WordPress antwortet mit 404.
		$vars['name'] = 'elfzwo-einsatz-nicht-gefunden';
	}
	return $vars;
}
add_filter( 'request', 'elfzwo_einsatz_resolve_request' );

function elfzwo_einsatz_permalink( $link, $post ) {
	if ( 'einsatz' !== $post->post_type || 'auto-draft' === $post->post_status ) {
		return $link;
	}
	$id = elfzwo_einsatz_id( $post->ID );
	return $id ? home_url( user_trailingslashit( 'einsatz/' . $id ) ) : $link;
}
add_filter( 'post_type_link', 'elfzwo_einsatz_permalink', 10, 2 );

/* ---------------------------------------------------------- Backend-Liste */

function elfzwo_einsatz_admin_columns( $columns ) {
	$neu = array();
	foreach ( $columns as $key => $label ) {
		$neu[ $key ] = $label;
		if ( 'cb' === $key ) {
			$neu['einsatz_id'] = 'Einsatz-ID';
		}
	}
	return $neu;
}
add_filter( 'manage_einsatz_posts_columns', 'elfzwo_einsatz_admin_columns' );

function elfzwo_einsatz_admin_column_content( $column, $post_id ) {
	if ( 'einsatz_id' === $column ) {
		echo '<strong>' . esc_html( elfzwo_einsatz_id( $post_id ) ?: '—' ) . '</strong>';
	}
}
add_action( 'manage_einsatz_posts_custom_column', 'elfzwo_einsatz_admin_column_content', 10, 2 );

function elfzwo_einsatz_sortable_columns( $columns ) {
	$columns['einsatz_id'] = array( 'einsatz_id', true );
	return $columns;
}
add_filter( 'manage_edit-einsatz_sortable_columns', 'elfzwo_einsatz_sortable_columns' );

/* ----------------------------------------------------- Schema-Abgleich */

/**
 * Bringt die Datenbank auf ELFZWO_EINSATZ_SCHEMA: bestehende Slugs
 * einfrieren, alle Jahre chronologisch neu nummerieren und die
 * Rewrite-Regeln für /einsatz/{jahr}/{nr}/ neu schreiben.
 */
function elfzwo_einsatz_migrate() {
	if ( (int) get_option( 'elfzwo_einsatz_schema', 0 ) >= ELFZWO_EINSATZ_SCHEMA ) {
		return;
	}
	if ( get_transient( 'elfzwo_einsatz_migration_lock' ) ) {
		return;
	}
	set_transient( 'elfzwo_einsatz_migration_lock', 1, 5 * MINUTE_IN_SECONDS );

	$ids = get_posts(
		array(
			'post_type'        => 'einsatz',
			'post_status'      => elfzwo_einsatz_nummer_statuses(),
			'posts_per_page'   => -1,
			'fields'           => 'ids',
			'suppress_filters' => true,
		)
	);
	$jahre = array();
	foreach ( $ids as $id ) {
		$jahre[ elfzwo_einsatz_jahr( $id ) ] = true;
		// Bestehende Slugs (= bisherige öffentliche URLs) einfrieren.
		if ( get_post_field( 'post_name', $id ) ) {
			update_post_meta( $id, '_elfzwo_slug_fixed', 1 );
		}
	}
	foreach ( array_keys( $jahre ) as $jahr ) {
		elfzwo_einsatz_renumber_year( $jahr );
	}

	flush_rewrite_rules( false );
	update_option( 'elfzwo_einsatz_schema', ELFZWO_EINSATZ_SCHEMA );
	delete_transient( 'elfzwo_einsatz_migration_lock' );
}
add_action( 'init', 'elfzwo_einsatz_migrate', 99 );
