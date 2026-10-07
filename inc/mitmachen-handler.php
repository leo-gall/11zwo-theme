<?php
/**
 * "Mach mit"-Formular: Einsendungen werden nicht im Backend gespeichert,
 * sondern direkt per E-Mail zugestellt. Im Block "Mach-mit-Formular" hat
 * jede Auswahlmöglichkeit ihre eigenen Empfänger.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
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
 * Empfänger der gewählten Auswahl, direkt aus dem gespeicherten Block der
 * Seite gelesen (nie aus dem abgeschickten Formular), damit sie nicht von
 * außen manipuliert werden können. Unbekannte Auswahl: erste Auswahl.
 */
function elfzwo_mitmachen_empfaenger_von( $page_id, $interesse ) {
	$post   = get_post( $page_id );
	$blocks = $post ? elfzwo_mitmachen_find_blocks( parse_blocks( $post->post_content ) ) : array();
	$type   = WP_Block_Type_Registry::get_instance()->get_registered( 'elfzwo/mitmachen-form' );

	$fallback = null;
	foreach ( $blocks as $block ) {
		$attrs = $type ? $type->prepare_attributes_for_render( $block['attrs'] ) : $block['attrs'];
		foreach ( $attrs['interests'] ?? array() as $interest ) {
			if ( ( $interest['label'] ?? '' ) === $interesse ) {
				return $interest['empfaenger'] ?? '';
			}
			$fallback = $fallback ?? ( $interest['empfaenger'] ?? '' );
		}
	}
	return $fallback ?? '';
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

/**
 * Spamschutz ohne Captcha: ein Feld, das Menschen nicht sehen (Bots füllen es
 * trotzdem aus), und ein signierter Zeitstempel, damit Einsendungen, die
 * schneller als ELFZWO_MITMACHEN_MINDESTZEIT nach dem Seitenaufruf kommen,
 * verworfen werden.
 */
define( 'ELFZWO_MITMACHEN_MINDESTZEIT', 3 );

function elfzwo_mitmachen_spamschutz_felder() {
	$zeit = time();
	?>
	<div class="elfzwo-hp" aria-hidden="true">
		<label>Website <input type="text" name="website" value="" tabindex="-1" autocomplete="off"></label>
	</div>
	<input type="hidden" name="elfzwo_zeit" value="<?php echo esc_attr( $zeit . '.' . wp_hash( 'elfzwo_mitmachen_' . $zeit ) ); ?>">
	<?php
}

function elfzwo_mitmachen_ist_spam() {
	if ( ! empty( $_POST['website'] ) ) {
		return true;
	}
	$teile = explode( '.', sanitize_text_field( wp_unslash( $_POST['elfzwo_zeit'] ?? '' ) ), 2 );
	if ( 2 !== count( $teile ) || ! hash_equals( wp_hash( 'elfzwo_mitmachen_' . $teile[0] ), $teile[1] ) ) {
		return true;
	}
	return time() - (int) $teile[0] < ELFZWO_MITMACHEN_MINDESTZEIT;
}

function elfzwo_handle_mitmachen_submit() {
	if ( ! isset( $_POST['elfzwo_mitmachen_nonce'] ) || ! wp_verify_nonce( $_POST['elfzwo_mitmachen_nonce'], 'elfzwo_mitmachen' ) ) {
		wp_die( 'Ungültige Anfrage.' );
	}

	$page_id = isset( $_POST['redirect_id'] ) ? absint( $_POST['redirect_id'] ) : 0;

	// Bots bekommen dieselbe Erfolgsmeldung, damit sie nicht nachjustieren.
	if ( elfzwo_mitmachen_ist_spam() ) {
		wp_safe_redirect( add_query_arg( 'mitmachen', 'success', get_permalink( $page_id ) ) . '#formular' );
		exit;
	}

	$interesse = isset( $_POST['interesse'] ) ? sanitize_text_field( wp_unslash( $_POST['interesse'] ) ) : '';
	$name      = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$kontakt   = isset( $_POST['kontakt'] ) ? sanitize_email( wp_unslash( $_POST['kontakt'] ) ) : '';

	if ( '' === $name || ! is_email( $kontakt ) ) {
		wp_safe_redirect( add_query_arg( 'mitmachen', 'error', get_permalink( $page_id ) ) . '#formular' );
		exit;
	}

	wp_mail(
		elfzwo_mitmachen_empfaenger( elfzwo_mitmachen_empfaenger_von( $page_id, $interesse ) ),
		'Neue Mach-mit-Anfrage von ' . $name,
		"Name: {$name}\nE-Mail: {$kontakt}\nInteresse: {$interesse}",
		array( 'Reply-To: ' . $kontakt )
	);

	wp_safe_redirect( add_query_arg( 'mitmachen', 'success', get_permalink( $page_id ) ) . '#formular' );
	exit;
}
add_action( 'admin_post_elfzwo_mitmachen', 'elfzwo_handle_mitmachen_submit' );
add_action( 'admin_post_nopriv_elfzwo_mitmachen', 'elfzwo_handle_mitmachen_submit' );
