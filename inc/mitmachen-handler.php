<?php
/**
 * "Mach mit"-Formular: Einsendungen werden nicht mehr im Backend gespeichert,
 * sondern direkt per E-Mail an die in den Einstellungen hinterlegten
 * Empfänger zugestellt (Empfänger-Liste & Vorlage: Einstellungen > Feuerwehr Greifenberg).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function elfzwo_mitmachen_empfaenger() {
	$raw   = elfzwo_option( 'elfzwo_mitmachen_empfaenger', get_option( 'admin_email' ) );
	$parts = preg_split( '/[,\n\r]+/', $raw );
	$emails = array();
	foreach ( $parts as $part ) {
		$email = trim( $part );
		if ( $email && is_email( $email ) ) {
			$emails[] = $email;
		}
	}
	return $emails ? $emails : array( get_option( 'admin_email' ) );
}

function elfzwo_mitmachen_mail_inhalt( $name, $kontakt, $interesse ) {
	$betreff_vorlage = elfzwo_option( 'elfzwo_mitmachen_betreff', 'Neue Mach-mit-Anfrage von {name}' );
	$text_vorlage    = elfzwo_option( 'elfzwo_mitmachen_template', "Name: {name}\nKontakt: {kontakt}\nInteresse: {interesse}" );

	$suche  = array( '{name}', '{kontakt}', '{interesse}' );
	$ersatz = array( $name, $kontakt, $interesse );

	return array(
		'betreff' => str_replace( $suche, $ersatz, $betreff_vorlage ),
		'text'    => str_replace( $suche, $ersatz, $text_vorlage ),
	);
}

function elfzwo_handle_mitmachen_submit() {
	if ( ! isset( $_POST['elfzwo_mitmachen_nonce'] ) || ! wp_verify_nonce( $_POST['elfzwo_mitmachen_nonce'], 'elfzwo_mitmachen' ) ) {
		wp_die( 'Ungültige Anfrage.' );
	}

	$interesse = isset( $_POST['interesse'] ) ? sanitize_text_field( wp_unslash( $_POST['interesse'] ) ) : '';
	$name      = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$kontakt   = isset( $_POST['kontakt'] ) ? sanitize_text_field( wp_unslash( $_POST['kontakt'] ) ) : '';
	$page_id   = isset( $_POST['redirect_id'] ) ? absint( $_POST['redirect_id'] ) : 0;

	if ( '' === $name || '' === $kontakt ) {
		wp_safe_redirect( add_query_arg( 'mitmachen', 'error', get_permalink( $page_id ) ) );
		exit;
	}

	$mail = elfzwo_mitmachen_mail_inhalt( $name, $kontakt, $interesse );
	wp_mail( elfzwo_mitmachen_empfaenger(), $mail['betreff'], $mail['text'] );

	wp_safe_redirect( add_query_arg( 'mitmachen', 'success', get_permalink( $page_id ) ) );
	exit;
}
add_action( 'admin_post_elfzwo_mitmachen', 'elfzwo_handle_mitmachen_submit' );
add_action( 'admin_post_nopriv_elfzwo_mitmachen', 'elfzwo_handle_mitmachen_submit' );
