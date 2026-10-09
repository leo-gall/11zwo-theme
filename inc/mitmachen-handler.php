<?php
/**
 * "Mach mit"-Formular: jede Einsendung wird als "Anfrage" im Backend
 * gespeichert (Menüpunkt "Anfragen" und Widget auf dem Dashboard), es geht
 * keine Mail raus.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function elfzwo_anfragen_registrieren() {
	register_post_type(
		'anfrage',
		array(
			'labels'          => array(
				'name'          => 'Anfragen',
				'singular_name' => 'Anfrage',
				'menu_name'     => 'Anfragen',
				'all_items'     => 'Alle Anfragen',
				'search_items'  => 'Anfragen durchsuchen',
				'not_found'     => 'Noch keine Anfragen.',
			),
			'public'          => false,
			'show_ui'         => true,
			'show_in_rest'    => false,
			'menu_icon'       => 'dashicons-groups',
			'menu_position'   => 3,
			'supports'        => array( 'title' ),
			'capability_type' => 'post',
			'capabilities'    => array( 'create_posts' => 'do_not_allow' ),
			'map_meta_cap'    => true,
		)
	);
}
add_action( 'init', 'elfzwo_anfragen_registrieren' );

function elfzwo_handle_mitmachen_submit() {
	$page_id = absint( $_POST['redirect_id'] ?? 0 );
	$name    = sanitize_text_field( wp_unslash( $_POST['name'] ?? '' ) );

	$id = wp_insert_post(
		array(
			'post_type'   => 'anfrage',
			'post_status' => 'publish',
			'post_title'  => $name,
			'meta_input'  => array(
				'email'     => sanitize_email( wp_unslash( $_POST['kontakt'] ?? '' ) ),
				'interesse' => sanitize_text_field( wp_unslash( $_POST['interesse'] ?? '' ) ),
			),
		)
	);
	$status = $id && ! is_wp_error( $id ) ? 'success' : 'error';

	// Das Formular schickt per JavaScript ab (ajax=1) und bekommt JSON statt einer Weiterleitung.
	if ( ! empty( $_POST['ajax'] ) ) {
		wp_send_json( array( 'status' => $status ) );
	}
	wp_safe_redirect( add_query_arg( 'mitmachen', $status, get_permalink( $page_id ) ) . '#formular' );
	exit;
}
add_action( 'admin_post_elfzwo_mitmachen', 'elfzwo_handle_mitmachen_submit' );
add_action( 'admin_post_nopriv_elfzwo_mitmachen', 'elfzwo_handle_mitmachen_submit' );

/* ------------------------------------------------------------- Backend */

function elfzwo_anfragen_spalten() {
	return array(
		'cb'        => '<input type="checkbox">',
		'name'      => 'Name',
		'email'     => 'E-Mail',
		'interesse' => 'Interesse',
		'eingang'   => 'Eingang',
	);
}
add_filter( 'manage_anfrage_posts_columns', 'elfzwo_anfragen_spalten' );

function elfzwo_anfragen_spalte( $spalte, $post_id ) {
	$email = get_post_meta( $post_id, 'email', true );
	switch ( $spalte ) {
		case 'name':
			echo '<strong>' . esc_html( get_the_title( $post_id ) ) . '</strong>';
			break;
		case 'email':
			echo $email ? '<a href="' . esc_url( 'mailto:' . $email ) . '">' . esc_html( $email ) . '</a>' : '';
			break;
		case 'interesse':
			echo esc_html( get_post_meta( $post_id, 'interesse', true ) );
			break;
		case 'eingang':
			echo esc_html( get_the_date( 'd.m.Y, H:i', $post_id ) ) . ' Uhr';
			break;
	}
}
add_action( 'manage_anfrage_posts_custom_column', 'elfzwo_anfragen_spalte', 10, 2 );

function elfzwo_anfragen_hauptspalte( $spalte, $screen ) {
	return 'edit-anfrage' === $screen ? 'name' : $spalte;
}
add_filter( 'list_table_primary_column', 'elfzwo_anfragen_hauptspalte', 10, 2 );

/** In der Liste nur Löschen anbieten; Anfragen werden nicht bearbeitet. */
function elfzwo_anfragen_aktionen( $aktionen, $post ) {
	return 'anfrage' === $post->post_type ? array_intersect_key( $aktionen, array_flip( array( 'trash', 'untrash', 'delete' ) ) ) : $aktionen;
}
add_filter( 'post_row_actions', 'elfzwo_anfragen_aktionen', 10, 2 );

function elfzwo_anfragen_bulk( $aktionen ) {
	unset( $aktionen['edit'] );
	return $aktionen;
}
add_filter( 'bulk_actions-edit-anfrage', 'elfzwo_anfragen_bulk' );

function elfzwo_anfragen_widget() {
	wp_add_dashboard_widget( 'elfzwo_anfragen', 'Neueste Mach-mit-Anfragen', 'elfzwo_anfragen_widget_inhalt' );
}
add_action( 'wp_dashboard_setup', 'elfzwo_anfragen_widget' );

function elfzwo_anfragen_widget_inhalt() {
	$anfragen = get_posts( array( 'post_type' => 'anfrage', 'numberposts' => 8 ) );
	if ( ! $anfragen ) {
		echo '<p>Noch keine Anfragen.</p>';
		return;
	}
	echo '<table class="widefat striped"><tbody>';
	foreach ( $anfragen as $a ) {
		$email = get_post_meta( $a->ID, 'email', true );
		printf(
			'<tr><td><strong>%s</strong><br><a href="%s">%s</a></td><td>%s</td><td style="white-space:nowrap">%s</td></tr>',
			esc_html( $a->post_title ),
			esc_url( 'mailto:' . $email ),
			esc_html( $email ),
			esc_html( get_post_meta( $a->ID, 'interesse', true ) ),
			esc_html( get_the_date( 'd.m.Y H:i', $a ) )
		);
	}
	echo '</tbody></table>';
	printf( '<p><a href="%s">Alle Anfragen ansehen</a></p>', esc_url( admin_url( 'edit.php?post_type=anfrage' ) ) );
}

/**
 * Einmalig: die Datenschutzerklärung beschreibt statt des früheren
 * Kontaktformulars das Mach-mit-Formular (Name, E-Mail, Interesse, gespeichert
 * im Backend der Website).
 */
function elfzwo_migrate_datenschutz_mitmachen() {
	if ( get_option( 'elfzwo_datenschutz_mitmachen' ) ) {
		return;
	}
	update_option( 'elfzwo_datenschutz_mitmachen', 1 );
	$seite = get_post( (int) get_option( 'wp_page_for_privacy_policy' ) );
	foreach ( array( 'datenschutzerklaerung', 'datenschutzerklarung', 'datenschutz' ) as $slug ) {
		if ( $seite && 'publish' === $seite->post_status ) {
			break;
		}
		$seite = get_page_by_path( $slug );
	}
	if ( ! $seite || 'publish' !== $seite->post_status ) {
		return;
	}
	$neu = str_replace(
		array(
			"<strong>Kontaktdaten (Kontaktformular):</strong><br>\nWenn Sie unser Kontaktformular nutzen, verarbeiten und speichern wir Ihren Namen, Ihre E-Mail-Adresse und/oder Telefonnummer sowie Ihre Nachricht, um Ihre Anfrage zu beantworten.",
			'sowie ein verschlüsseltes Kontaktformular.',
		),
		array(
			"<strong>Kontaktdaten (Mach-mit-Formular):</strong><br>\nWenn Sie unser Mach-mit-Formular nutzen, speichern wir Ihren Namen, Ihre E-Mail-Adresse und Ihr gewähltes Interesse in der Verwaltung unserer Website, um uns bei Ihnen zu melden. Weitere Daten werden nicht abgefragt. Die Angaben werden verschlüsselt übertragen und sind nur für die zuständigen Mitglieder des Vereins einsehbar.",
			'sowie ein Mach-mit-Formular, über das Sie uns Ihren Namen, Ihre E-Mail-Adresse und Ihr Interesse an einer Mitgliedschaft mitteilen können.',
		),
		$seite->post_content
	);
	if ( $neu !== $seite->post_content ) {
		_wp_put_post_revision( $seite );
		wp_update_post( array( 'ID' => $seite->ID, 'post_content' => wp_slash( $neu ) ) );
	}
}
add_action( 'init', 'elfzwo_migrate_datenschutz_mitmachen', 30 );
