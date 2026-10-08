<?php
/**
 * Mail-Protokoll als Textdatei im Webspace: jede Mail der Website (versendet
 * oder fehlgeschlagen) und jede Einsendung des Mach-mit-Formulars, eine Datei
 * pro Monat unter wp-content/uploads/mail-protokoll-…/. Der Ordner ist per
 * .htaccess gesperrt und hat einen nicht erratbaren Namen, weil die Dateien
 * Namen und E-Mail-Adressen enthalten.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Ordner des Protokolls; wird beim ersten Eintrag samt Zugriffssperre angelegt. */
function elfzwo_mail_protokoll_ordner() {
	$ordner = wp_upload_dir( null, false )['basedir'] . '/mail-protokoll-' . substr( wp_hash( 'elfzwo_mail_protokoll' ), 0, 12 );
	if ( ! is_dir( $ordner ) && wp_mkdir_p( $ordner ) ) {
		file_put_contents( $ordner . '/.htaccess', "Require all denied\nDeny from all\n" );
		file_put_contents( $ordner . '/index.php', "<?php\n// Kein Zugriff.\n" );
	}
	return $ordner;
}

function elfzwo_mail_protokoll_datei( $zeit = null ) {
	return elfzwo_mail_protokoll_ordner() . '/mails-' . wp_date( 'Y-m', $zeit ?? time() ) . '.log';
}

/**
 * Schreibt einen Eintrag: Kopfzeile mit Zeit und Vorgang, darunter die Felder
 * eingerückt, Einträge durch eine Leerzeile getrennt.
 *
 * @param string $vorgang z. B. "MAIL VERSENDET", "MACH-MIT SPAM".
 * @param array  $felder  Beschriftung => Wert (Arrays werden mit Komma verbunden).
 */
function elfzwo_mail_protokoll( $vorgang, $felder ) {
	$zeilen = array( wp_date( 'd.m.Y H:i:s' ) . '  ' . $vorgang );
	$breite = max( array_map( 'mb_strlen', array_keys( $felder ) ) );
	foreach ( $felder as $label => $wert ) {
		$wert = is_array( $wert ) ? implode( ', ', $wert ) : (string) $wert;
		if ( '' === $wert ) {
			continue;
		}
		$wert     = str_replace( "\n", "\n" . str_repeat( ' ', $breite + 6 ), trim( $wert ) );
		$zeilen[] = '    ' . $label . ':' . str_repeat( ' ', $breite - mb_strlen( $label ) + 1 ) . $wert;
	}
	file_put_contents( elfzwo_mail_protokoll_datei(), implode( "\n", $zeilen ) . "\n\n", FILE_APPEND | LOCK_EX );
}

/** Kopfzeilen einer Mail ohne die üblichen technischen Angaben. */
function elfzwo_mail_protokoll_kopf( $headers ) {
	$headers = is_array( $headers ) ? $headers : explode( "\n", str_replace( "\r\n", "\n", (string) $headers ) );
	return array_values(
		array_filter(
			array_map( 'trim', $headers ),
			function ( $h ) {
				return '' !== $h && ! preg_match( '/^(content-type|mime-version|x-mailer):/i', $h );
			}
		)
	);
}

/**
 * WordPress reicht an wp_mail_succeeded nur einen Teil der Kopfzeilen weiter
 * (Reply-To, Cc, Bcc fehlen), deshalb die ursprünglichen Angaben merken.
 */
function elfzwo_mail_protokoll_merken( $atts ) {
	$GLOBALS['elfzwo_mail_protokoll_atts'] = $atts;
	return $atts;
}
add_filter( 'wp_mail', 'elfzwo_mail_protokoll_merken', PHP_INT_MAX );

function elfzwo_mail_protokoll_felder( $mail ) {
	$mail = array_merge( $mail, (array) ( $GLOBALS['elfzwo_mail_protokoll_atts'] ?? array() ) );
	$GLOBALS['elfzwo_mail_protokoll_atts'] = null;
	if ( ! is_array( $mail['to'] ?? null ) ) {
		$mail['to'] = array_map( 'trim', explode( ',', (string) ( $mail['to'] ?? '' ) ) );
	}
	return array(
		'An'         => $mail['to'],
		'Betreff'    => $mail['subject'] ?? '',
		'Kopfzeilen' => implode( "\n", elfzwo_mail_protokoll_kopf( $mail['headers'] ?? array() ) ),
		'Anhänge'    => array_map( 'basename', (array) ( $mail['attachments'] ?? array() ) ),
		'Text'       => wp_strip_all_tags( (string) ( $mail['message'] ?? '' ) ),
	);
}

function elfzwo_mail_protokoll_versendet( $mail ) {
	elfzwo_mail_protokoll( 'MAIL VERSENDET', elfzwo_mail_protokoll_felder( $mail ) );
}
add_action( 'wp_mail_succeeded', 'elfzwo_mail_protokoll_versendet' );

function elfzwo_mail_protokoll_fehler( $fehler ) {
	$mail = (array) $fehler->get_error_data();
	elfzwo_mail_protokoll( 'MAIL FEHLGESCHLAGEN', array( 'Fehler' => $fehler->get_error_message() ) + elfzwo_mail_protokoll_felder( $mail ) );
}
add_action( 'wp_mail_failed', 'elfzwo_mail_protokoll_fehler' );
