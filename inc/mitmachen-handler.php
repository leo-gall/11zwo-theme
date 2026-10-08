<?php
/**
 * "Mach mit"-Formular: Einsendungen werden per E-Mail zugestellt (im Block
 * "Mach-mit-Formular" hat jede Auswahlmöglichkeit ihre eigenen Empfänger) und
 * zusätzlich mit Ergebnis protokolliert (Werkzeuge → Mach-mit-Anfragen).
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
 * Spamschutz ohne Captcha: ein per display:none verstecktes Feld (Bots füllen
 * es aus, Browser-Autofill und Passwort-Manager überspringen unsichtbare
 * Felder; der Name ist bewusst keiner, den Autofill kennt) und ein signierter
 * Zeitstempel gegen Einsendungen, die schneller als
 * ELFZWO_MITMACHEN_MINDESTZEIT nach dem Seitenaufruf kommen.
 */
define( 'ELFZWO_MITMACHEN_MINDESTZEIT', 2 );

function elfzwo_mitmachen_spamschutz_felder() {
	$zeit = time();
	?>
	<div class="hidden" aria-hidden="true">
		<label>Bitte leer lassen <input type="text" name="elfzwo_hp_x7" value="" tabindex="-1" autocomplete="off" data-1p-ignore data-lpignore="true" data-bwignore></label>
	</div>
	<input type="hidden" name="elfzwo_zeit" value="<?php echo esc_attr( $zeit . '.' . wp_hash( 'elfzwo_mitmachen_' . $zeit ) ); ?>">
	<?php
}

/** Grund, warum die Einsendung als Spam gilt, sonst ''. */
function elfzwo_mitmachen_spam_grund() {
	if ( ! empty( $_POST['elfzwo_hp_x7'] ) ) {
		return 'Verstecktes Feld ausgefüllt';
	}
	$teile = explode( '.', sanitize_text_field( wp_unslash( $_POST['elfzwo_zeit'] ?? '' ) ), 2 );
	if ( 2 !== count( $teile ) || ! hash_equals( wp_hash( 'elfzwo_mitmachen_' . $teile[0] ), $teile[1] ) ) {
		return 'Zeitstempel fehlt oder ungültig';
	}
	$sekunden = time() - (int) $teile[0];
	return $sekunden < ELFZWO_MITMACHEN_MINDESTZEIT ? 'Zu schnell abgeschickt (' . $sekunden . ' s)' : '';
}

/* ------------------------------------------------------------ Protokoll */

/**
 * Jede Einsendung landet mit Ergebnis im Protokoll (Werkzeuge → Mach-mit-
 * Anfragen), damit keine Anfrage verloren geht, auch wenn die Mail nicht
 * ankommt. Es werden die letzten ELFZWO_MITMACHEN_PROTOKOLL Einträge behalten,
 * davon höchstens 50 Spam, damit Bots keine echten Anfragen verdrängen.
 */
define( 'ELFZWO_MITMACHEN_PROTOKOLL', 200 );

function elfzwo_mitmachen_protokollieren( $eintrag ) {
	$liste = get_option( 'elfzwo_mitmachen_protokoll', array() );
	array_unshift( $liste, array_merge( array( 'zeit' => time() ), $eintrag ) );
	$spam  = 0;
	$liste = array_values(
		array_filter(
			$liste,
			function ( $e ) use ( &$spam ) {
				return 'spam' !== $e['status'] || ++$spam <= 50;
			}
		)
	);
	update_option( 'elfzwo_mitmachen_protokoll', array_slice( $liste, 0, ELFZWO_MITMACHEN_PROTOKOLL ), false );

	$namen = array( 'gesendet' => 'Mail verschickt', 'fehler' => 'Mail fehlgeschlagen', 'spam' => 'Als Spam verworfen', 'ungueltig' => 'Eingabe ungültig' );
	elfzwo_mail_protokoll(
		'MACH-MIT-ANFRAGE',
		array(
			'Name'      => $eintrag['name'] ?? '',
			'E-Mail'    => $eintrag['kontakt'] ?? '',
			'Interesse' => $eintrag['interesse'] ?? '',
			'Ergebnis'  => $namen[ $eintrag['status'] ] ?? $eintrag['status'],
			'Empfänger' => $eintrag['empfaenger'] ?? array(),
			'Details'   => $eintrag['details'] ?? '',
		)
	);
}

function elfzwo_mitmachen_protokoll_menue() {
	add_management_page( 'Mach-mit-Anfragen', 'Mach-mit-Anfragen', 'edit_pages', 'elfzwo-mitmachen', 'elfzwo_mitmachen_protokoll_seite' );
}
add_action( 'admin_menu', 'elfzwo_mitmachen_protokoll_menue' );

function elfzwo_mitmachen_protokoll_seite() {
	$liste  = get_option( 'elfzwo_mitmachen_protokoll', array() );
	$farben = array( 'gesendet' => '#00a32a', 'fehler' => '#d63638', 'spam' => '#996800', 'ungueltig' => '#787c82' );
	$namen  = array( 'gesendet' => 'Mail verschickt', 'fehler' => 'Mail fehlgeschlagen', 'spam' => 'Als Spam verworfen', 'ungueltig' => 'Eingabe ungültig' );
	?>
	<div class="wrap">
		<h1>Mach-mit-Anfragen</h1>
		<p>Alle Einsendungen des Mach-mit-Formulars mit Ergebnis, auch wenn keine Mail ankam (die letzten <?php echo (int) ELFZWO_MITMACHEN_PROTOKOLL; ?>).</p>
		<?php $dateien = array_reverse( glob( elfzwo_mail_protokoll_ordner() . '/mails-*.log' ) ?: array() ); ?>
		<h2>Mail-Protokoll (alle Mails der Website)</h2>
		<p>Liegt im Webspace unter <code><?php echo esc_html( str_replace( ABSPATH, '', elfzwo_mail_protokoll_ordner() ) ); ?>/</code>, eine Datei pro Monat; der Ordner ist für Besucher gesperrt.</p>
		<p>
			<?php if ( ! $dateien ) : ?>Noch keine Einträge.<?php endif; ?>
			<?php foreach ( $dateien as $datei ) : ?>
				<a class="button" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=elfzwo_mail_protokoll&datei=' . rawurlencode( basename( $datei ) ) ), 'elfzwo_mail_protokoll' ) ); ?>"><?php echo esc_html( basename( $datei ) ); ?> (<?php echo esc_html( size_format( filesize( $datei ) ) ); ?>)</a>
			<?php endforeach; ?>
		</p>
		<h2>Einsendungen</h2>
		<table class="widefat striped">
			<thead><tr><th>Zeit</th><th>Name</th><th>E-Mail</th><th>Interesse</th><th>Ergebnis</th><th>Empfänger / Details</th></tr></thead>
			<tbody>
				<?php if ( ! $liste ) : ?>
					<tr><td colspan="6">Noch keine Einsendungen.</td></tr>
				<?php endif; ?>
				<?php foreach ( $liste as $e ) : ?>
					<tr>
						<td><?php echo esc_html( wp_date( 'd.m.Y H:i', $e['zeit'] ) ); ?></td>
						<td><?php echo esc_html( $e['name'] ?? '' ); ?></td>
						<td><?php echo esc_html( $e['kontakt'] ?? '' ); ?></td>
						<td><?php echo esc_html( $e['interesse'] ?? '' ); ?></td>
						<td style="color:<?php echo esc_attr( $farben[ $e['status'] ] ?? '' ); ?>;font-weight:600"><?php echo esc_html( $namen[ $e['status'] ] ?? $e['status'] ); ?></td>
						<td><?php echo esc_html( trim( implode( ', ', (array) ( $e['empfaenger'] ?? array() ) ) . ( empty( $e['details'] ) ? '' : ' — ' . $e['details'] ), ' —' ) ); ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<?php
}

/** Download einer Protokolldatei für Redakteure (der Ordner selbst ist gesperrt). */
function elfzwo_mail_protokoll_download() {
	check_admin_referer( 'elfzwo_mail_protokoll' );
	$name  = basename( sanitize_file_name( wp_unslash( $_GET['datei'] ?? '' ) ) );
	$datei = elfzwo_mail_protokoll_ordner() . '/' . $name;
	if ( ! current_user_can( 'edit_pages' ) || ! preg_match( '/^mails-\d{4}-\d{2}\.log$/', $name ) || ! is_file( $datei ) ) {
		wp_die( 'Datei nicht gefunden.' );
	}
	nocache_headers();
	header( 'Content-Type: text/plain; charset=utf-8' );
	header( 'Content-Disposition: inline; filename="' . $name . '"' );
	readfile( $datei );
	exit;
}
add_action( 'admin_post_elfzwo_mail_protokoll', 'elfzwo_mail_protokoll_download' );

/* ------------------------------------------------------------- Versand */

function elfzwo_mitmachen_mail_fehler( $fehler ) {
	if ( ! empty( $GLOBALS['elfzwo_mitmachen_versand'] ) ) {
		$GLOBALS['elfzwo_mitmachen_fehler'] = $fehler->get_error_message();
	}
}
add_action( 'wp_mail_failed', 'elfzwo_mitmachen_mail_fehler' );

function elfzwo_handle_mitmachen_submit() {
	$page_id   = isset( $_POST['redirect_id'] ) ? absint( $_POST['redirect_id'] ) : 0;
	$interesse = isset( $_POST['interesse'] ) ? sanitize_text_field( wp_unslash( $_POST['interesse'] ) ) : '';
	$name      = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$kontakt   = isset( $_POST['kontakt'] ) ? sanitize_email( wp_unslash( $_POST['kontakt'] ) ) : '';
	$eintrag   = array( 'name' => $name, 'kontakt' => $kontakt, 'interesse' => $interesse );
	// Das Formular schickt per JavaScript ab (ajax=1) und bekommt JSON statt einer Weiterleitung.
	$zurueck   = function ( $status ) use ( $page_id ) {
		if ( ! empty( $_POST['ajax'] ) ) {
			wp_send_json( array( 'status' => $status ) );
		}
		wp_safe_redirect( add_query_arg( 'mitmachen', $status, get_permalink( $page_id ) ?: home_url( '/' ) ) . '#formular' );
		exit;
	};

	if ( ! isset( $_POST['elfzwo_mitmachen_nonce'] ) || ! wp_verify_nonce( $_POST['elfzwo_mitmachen_nonce'], 'elfzwo_mitmachen' ) ) {
		elfzwo_mitmachen_protokollieren( $eintrag + array( 'status' => 'ungueltig', 'details' => 'Formular abgelaufen' ) );
		$zurueck( 'error' );
	}

	// Bots bekommen dieselbe Erfolgsmeldung, damit sie nicht nachjustieren.
	$spam = elfzwo_mitmachen_spam_grund();
	if ( $spam ) {
		elfzwo_mitmachen_protokollieren( $eintrag + array( 'status' => 'spam', 'details' => $spam ) );
		$zurueck( 'success' );
	}

	if ( '' === $name || ! is_email( $kontakt ) ) {
		elfzwo_mitmachen_protokollieren( $eintrag + array( 'status' => 'ungueltig', 'details' => 'Name oder E-Mail fehlt' ) );
		$zurueck( 'error' );
	}

	$empfaenger = elfzwo_mitmachen_empfaenger( elfzwo_mitmachen_empfaenger_von( $page_id, $interesse ) );

	$GLOBALS['elfzwo_mitmachen_versand'] = true;
	$GLOBALS['elfzwo_mitmachen_fehler']  = '';
	$ok = wp_mail(
		$empfaenger,
		'Neue Mach-mit-Anfrage von ' . $name,
		"Name: {$name}\nE-Mail: {$kontakt}\nInteresse: {$interesse}",
		array( 'Reply-To: ' . $name . ' <' . $kontakt . '>' )
	);
	$GLOBALS['elfzwo_mitmachen_versand'] = false;

	elfzwo_mitmachen_protokollieren(
		$eintrag + array(
			'status'     => $ok ? 'gesendet' : 'fehler',
			'empfaenger' => $empfaenger,
			'details'    => $ok ? '' : ( $GLOBALS['elfzwo_mitmachen_fehler'] ?: 'wp_mail() fehlgeschlagen' ),
		)
	);
	$zurueck( 'success' );
}
add_action( 'admin_post_elfzwo_mitmachen', 'elfzwo_handle_mitmachen_submit' );
add_action( 'admin_post_nopriv_elfzwo_mitmachen', 'elfzwo_handle_mitmachen_submit' );
