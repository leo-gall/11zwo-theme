<?php
/**
 * "Mach mit"-Formular: Einsendungen werden nicht im Backend gespeichert,
 * sondern direkt per E-Mail zugestellt. Empfänger, Betreff und Text werden
 * im Block "Mach-mit-Formular" je Gruppe (Aktive, Jugend/Kinder, Verein)
 * gepflegt; jede Auswahlmöglichkeit ist einer Gruppe zugeordnet.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Gruppen, an die eine Mach-mit-Anfrage gehen kann. */
function elfzwo_mitmachen_gruppen() {
	return array(
		'aktive' => 'Aktive',
		'jugend' => 'Jugend/Kinder',
		'verein' => 'Verein',
	);
}

/**
 * Gruppe einer Auswahlmöglichkeit. Ältere Blöcke ohne Zuordnung werden
 * anhand des Labels eingeordnet.
 */
function elfzwo_mitmachen_gruppe_von( $interest ) {
	$gruppe = $interest['gruppe'] ?? '';
	if ( isset( elfzwo_mitmachen_gruppen()[ $gruppe ] ) ) {
		return $gruppe;
	}
	$label = strtolower( $interest['label'] ?? '' );
	if ( false !== strpos( $label, 'jugend' ) || false !== strpos( $label, 'kinder' ) ) {
		return 'jugend';
	}
	if ( false !== strpos( $label, 'förder' ) || false !== strpos( $label, 'verein' ) ) {
		return 'verein';
	}
	return 'aktive';
}

/** Sucht rekursiv alle Mach-mit-Blöcke in einer Liste geparster Blöcke. */
function elfzwo_mitmachen_find_blocks( $blocks ) {
	$found = array();
	foreach ( $blocks as $block ) {
		if ( 'elfzwo/mitmachen-form' === $block['blockName'] ) {
			$found[] = $block;
		}
		if ( ! empty( $block['innerBlocks'] ) ) {
			$found = array_merge( $found, elfzwo_mitmachen_find_blocks( $block['innerBlocks'] ) );
		}
	}
	return $found;
}

/**
 * Mail-Einstellungen der gewählten Gruppe, direkt aus dem gespeicherten
 * Block der Seite gelesen (nie aus dem abgeschickten Formular), damit die
 * Empfänger nicht von außen manipuliert werden können.
 */
function elfzwo_mitmachen_config( $page_id, $interesse ) {
	$post   = get_post( $page_id );
	$blocks = $post ? elfzwo_mitmachen_find_blocks( parse_blocks( $post->post_content ) ) : array();
	$type   = WP_Block_Type_Registry::get_instance()->get_registered( 'elfzwo/mitmachen-form' );

	$gruppe   = 'aktive';
	$mail     = null;
	$fallback = null;
	foreach ( $blocks as $block ) {
		$attrs = $type ? $type->prepare_attributes_for_render( $block['attrs'] ) : $block['attrs'];
		foreach ( $attrs['interests'] ?? array() as $interest ) {
			if ( ( $interest['label'] ?? '' ) === $interesse ) {
				$gruppe = elfzwo_mitmachen_gruppe_von( $interest );
				$mail   = $attrs['mail'][ $gruppe ] ?? array();
				break 2;
			}
		}
		// Unbekannte Auswahl: an die Aktiven des ersten Formulars der Seite.
		if ( null === $fallback ) {
			$fallback = $attrs['mail']['aktive'] ?? array();
		}
	}
	$mail = $mail ?? $fallback ?? array();

	return array(
		'gruppe'     => $gruppe,
		'empfaenger' => $mail['empfaenger'] ?? '',
		'betreff'    => $mail['betreff'] ?? '',
		'text'       => $mail['text'] ?? '',
	);
}

/** Empfängerliste aus dem Block; leer gelassen geht die Anfrage an die Admin-E-Mail. */
function elfzwo_mitmachen_empfaenger( $raw ) {
	$emails = array();
	foreach ( preg_split( '/[,;\s]+/', (string) $raw ) as $part ) {
		$email = trim( $part );
		if ( $email && is_email( $email ) ) {
			$emails[] = $email;
		}
	}
	return $emails ? $emails : array( get_option( 'admin_email' ) );
}

function elfzwo_mitmachen_mail_inhalt( $config, $name, $kontakt, $interesse ) {
	$betreff_vorlage = trim( $config['betreff'] ) ?: 'Neue Mach-mit-Anfrage von {name}';
	$text_vorlage    = trim( $config['text'] ) ?: "Name: {name}\nKontakt: {kontakt}\nInteresse: {interesse}";

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

	$config = elfzwo_mitmachen_config( $page_id, $interesse );
	$mail   = elfzwo_mitmachen_mail_inhalt( $config, $name, $kontakt, $interesse );
	wp_mail( elfzwo_mitmachen_empfaenger( $config['empfaenger'] ), $mail['betreff'], $mail['text'] );

	wp_safe_redirect( add_query_arg( 'mitmachen', 'success', get_permalink( $page_id ) ) );
	exit;
}
add_action( 'admin_post_elfzwo_mitmachen', 'elfzwo_handle_mitmachen_submit' );
add_action( 'admin_post_nopriv_elfzwo_mitmachen', 'elfzwo_handle_mitmachen_submit' );
