<?php
/**
 * "Mach mit"-Formular: schickt die Anfrage per wp_mail() an die Empfänger,
 * die im Block "Mach-mit-Formular" bei der gewählten Auswahl stehen (leer =
 * Admin-E-Mail der Website).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Empfänger der gewählten Auswahl aus dem Formular-Block der Seite. */
function elfzwo_mitmachen_empfaenger( $page_id, $interesse ) {
	$post = get_post( $page_id );
	foreach ( $post ? parse_blocks( $post->post_content ) : array() as $block ) {
		if ( 'elfzwo/mitmachen-form' !== $block['blockName'] ) {
			continue;
		}
		foreach ( $block['attrs']['interests'] ?? array() as $interest ) {
			if ( ( $interest['label'] ?? '' ) === $interesse && ! empty( $interest['empfaenger'] ) ) {
				return $interest['empfaenger'];
			}
		}
	}
	return get_option( 'admin_email' );
}

function elfzwo_handle_mitmachen_submit() {
	$page_id   = absint( $_POST['redirect_id'] ?? 0 );
	$interesse = sanitize_text_field( wp_unslash( $_POST['interesse'] ?? '' ) );
	$name      = sanitize_text_field( wp_unslash( $_POST['name'] ?? '' ) );
	$kontakt   = sanitize_email( wp_unslash( $_POST['kontakt'] ?? '' ) );

	$ok = wp_mail(
		elfzwo_mitmachen_empfaenger( $page_id, $interesse ),
		'Neue Mach-mit-Anfrage von ' . $name,
		"Name: $name\nE-Mail: $kontakt\nInteresse: $interesse",
		'Reply-To: ' . $kontakt
	);
	$status = $ok ? 'success' : 'error';

	// Das Formular schickt per JavaScript ab (ajax=1) und bekommt JSON statt einer Weiterleitung.
	if ( ! empty( $_POST['ajax'] ) ) {
		wp_send_json( array( 'status' => $status ) );
	}
	wp_safe_redirect( add_query_arg( 'mitmachen', $status, get_permalink( $page_id ) ) . '#formular' );
	exit;
}
add_action( 'admin_post_elfzwo_mitmachen', 'elfzwo_handle_mitmachen_submit' );
add_action( 'admin_post_nopriv_elfzwo_mitmachen', 'elfzwo_handle_mitmachen_submit' );
