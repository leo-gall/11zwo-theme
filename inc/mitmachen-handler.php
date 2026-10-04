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
	// Nur noch E-Mail-Adressen (keine Telefonnummern) als Kontaktweg.
	$kontakt   = isset( $_POST['kontakt'] ) ? sanitize_email( wp_unslash( $_POST['kontakt'] ) ) : '';
	$page_id   = isset( $_POST['redirect_id'] ) ? absint( $_POST['redirect_id'] ) : 0;

	if ( '' === $name || ! is_email( $kontakt ) ) {
		wp_safe_redirect( add_query_arg( 'mitmachen', 'error', get_permalink( $page_id ) ) );
		exit;
	}

	$config = elfzwo_mitmachen_config( $page_id, $interesse );
	$mail   = elfzwo_mitmachen_mail_inhalt( $config, $name, $kontakt, $interesse );
	wp_mail( elfzwo_mitmachen_empfaenger( $config['empfaenger'] ), $mail['betreff'], $mail['text'], array( 'Reply-To: ' . $kontakt ) );

	wp_safe_redirect( add_query_arg( 'mitmachen', 'success', get_permalink( $page_id ) ) );
	exit;
}
add_action( 'admin_post_elfzwo_mitmachen', 'elfzwo_handle_mitmachen_submit' );
add_action( 'admin_post_nopriv_elfzwo_mitmachen', 'elfzwo_handle_mitmachen_submit' );

/**
 * Der Mach-mit-Block bringt seinen Kopfbereich (Kicker, Titel,
 * Beschreibung) inzwischen selbst mit. Ein direkt davor stehender
 * "Einfacher Hero" wird deshalb einmalig entfernt; seine Texte werden in
 * den Mach-mit-Block übernommen, damit nichts doppelt erscheint.
 */
function elfzwo_migrate_mitmachen_hero() {
	if ( get_option( 'elfzwo_mitmachen_hero_migrated' ) ) {
		return;
	}
	$pages = get_posts(
		array(
			'post_type'   => 'page',
			'post_status' => 'any',
			'numberposts' => -1,
			's'           => 'wp:elfzwo/mitmachen-form',
		)
	);
	foreach ( $pages as $page ) {
		$blocks  = parse_blocks( $page->post_content );
		$changed = false;
		foreach ( $blocks as $i => $block ) {
			if ( 'elfzwo/mitmachen-form' !== $block['blockName'] ) {
				continue;
			}
			// Leere Freiraum-Blöcke zwischen Hero und Formular überspringen.
			$j = $i - 1;
			while ( $j >= 0 && null === $blocks[ $j ]['blockName'] && '' === trim( $blocks[ $j ]['innerHTML'] ) ) {
				$j--;
			}
			if ( $j < 0 || 'elfzwo/simple-hero' !== $blocks[ $j ]['blockName'] ) {
				continue;
			}
			foreach ( array( 'kicker', 'title', 'description' ) as $key ) {
				if ( isset( $blocks[ $j ]['attrs'][ $key ] ) && ! isset( $block['attrs'][ $key ] ) ) {
					$blocks[ $i ]['attrs'][ $key ] = $blocks[ $j ]['attrs'][ $key ];
				}
			}
			array_splice( $blocks, $j, $i - $j );
			$changed = true;
			break;
		}
		if ( $changed ) {
			wp_update_post(
				array(
					'ID'           => $page->ID,
					'post_content' => wp_slash( serialize_blocks( $blocks ) ),
				)
			);
		}
	}
	update_option( 'elfzwo_mitmachen_hero_migrated', 1 );
}
add_action( 'init', 'elfzwo_migrate_mitmachen_hero', 30 );

/**
 * Kurzzeitig gab es statt der Mail-Einstellungen je Gruppe nur eine
 * Benachrichtigungs-E-Mail (notifyEmail). Wo das schon gespeichert wurde,
 * wird die Adresse einmalig wieder als Empfänger aller Gruppen eingetragen.
 */
function elfzwo_migrate_mitmachen_notify_email() {
	if ( get_option( 'elfzwo_mitmachen_notify_email_reverted' ) ) {
		return;
	}
	$posts = get_posts(
		array(
			'post_type'   => array( 'page', 'post', 'wp_block' ),
			'post_status' => 'any',
			'numberposts' => -1,
			's'           => 'notifyEmail',
		)
	);
	foreach ( $posts as $post ) {
		$changed = false;
		$blocks  = elfzwo_mitmachen_notify_email_blocks( parse_blocks( $post->post_content ), $changed );
		if ( $changed ) {
			wp_update_post(
				array(
					'ID'           => $post->ID,
					'post_content' => wp_slash( serialize_blocks( $blocks ) ),
				)
			);
		}
	}
	update_option( 'elfzwo_mitmachen_notify_email_reverted', 1 );
}
add_action( 'init', 'elfzwo_migrate_mitmachen_notify_email', 31 );

function elfzwo_mitmachen_notify_email_blocks( $blocks, &$changed ) {
	foreach ( $blocks as $i => $block ) {
		if ( ! empty( $block['innerBlocks'] ) ) {
			$blocks[ $i ]['innerBlocks'] = elfzwo_mitmachen_notify_email_blocks( $block['innerBlocks'], $changed );
		}
		if ( 'elfzwo/mitmachen-form' !== $block['blockName'] || ! array_key_exists( 'notifyEmail', $block['attrs'] ) ) {
			continue;
		}
		$empfaenger = str_replace( ',', "\n", (string) $block['attrs']['notifyEmail'] );
		if ( empty( $block['attrs']['mail'] ) && '' !== trim( $empfaenger ) ) {
			foreach ( array_keys( elfzwo_mitmachen_gruppen() ) as $gruppe ) {
				$blocks[ $i ]['attrs']['mail'][ $gruppe ] = array(
					'empfaenger' => $empfaenger,
					'betreff'    => '',
					'text'       => '',
				);
			}
		}
		unset( $blocks[ $i ]['attrs']['notifyEmail'] );
		$changed = true;
	}
	return $blocks;
}
