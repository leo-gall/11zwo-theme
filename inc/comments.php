<?php
/**
 * Kommentar-Verhalten: Einsätze können nicht kommentiert werden, die
 * Kommentarverwaltung ist im Backend ausgeblendet (Inhalte laufen über
 * Beiträge), der Kommentar-RSS-Feed bleibt für Leser auffindbar.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Einsätze dürfen nie kommentierbar sein — unabhängig davon, wie der
 * Beitrag gespeichert wird (klassischer Editor, REST-API, Quick Edit).
 */
function elfzwo_force_close_einsatz_comment_status( $data, $postarr ) {
	if ( 'einsatz' === $data['post_type'] ) {
		$data['comment_status'] = 'closed';
		$data['ping_status']    = 'closed';
	}
	return $data;
}
add_filter( 'wp_insert_post_data', 'elfzwo_force_close_einsatz_comment_status', 10, 2 );

function elfzwo_force_close_einsatz_comments_open( $open, $post_id ) {
	if ( 'einsatz' === get_post_type( $post_id ) ) {
		return false;
	}
	return $open;
}
add_filter( 'comments_open', 'elfzwo_force_close_einsatz_comments_open', 10, 2 );
add_filter( 'pings_open', 'elfzwo_force_close_einsatz_comments_open', 10, 2 );

/**
 * Kommentar-Feed (/comments/feed/) bleibt auffindbar: WordPress trägt
 * den Discovery-Link automatisch in <head> ein.
 */
function elfzwo_theme_support_feed_links() {
	add_theme_support( 'automatic-feed-links' );
}
add_action( 'after_setup_theme', 'elfzwo_theme_support_feed_links' );

/**
 * Der Haupt-RSS-Feed (/feed/, /rss.xml) enthält standardmäßig nur die
 * neuesten 10 Beiträge — hier ALLE Beiträge und Einsätze, damit Abonnenten
 * auch über neue Einsätze informiert werden, nicht nur über "Aktuelles".
 * Gilt nicht für den Kommentar-Feed.
 */
function elfzwo_feed_include_einsaetze( $query ) {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_feed() || $query->is_comment_feed() ) {
		return;
	}
	$query->set( 'post_type', array( 'post', 'einsatz' ) );
	$query->set( 'elfzwo_feed_all', true );
}
add_action( 'pre_get_posts', 'elfzwo_feed_include_einsaetze' );

/**
 * Feeds ignorieren posts_per_page = -1 (WordPress erzwingt dort immer das
 * Limit aus "posts_per_rss"), daher das LIMIT für diese eine Abfrage direkt
 * entfernen.
 */
function elfzwo_feed_no_limit( $limits, $query ) {
	return $query->get( 'elfzwo_feed_all' ) ? '' : $limits;
}
add_filter( 'post_limits', 'elfzwo_feed_no_limit', 10, 2 );

/**
 * Einsätze tragen im Feed ihren tatsächlichen Einsatzzeitpunkt als
 * Veröffentlichungsdatum -- nicht den Zeitpunkt, zu dem sie im CMS erfasst
 * wurden (bei nachträglich eingetragenen Einsätzen sonst falsch).
 */
function elfzwo_feed_einsatz_pubdate( $time, $format, $gmt ) {
	$post = get_post();
	if ( ! is_feed() || ! $post || 'einsatz' !== $post->post_type ) {
		return $time;
	}
	$datum = elfzwo_meta( $post->ID, 'datum', '' );
	if ( ! $datum ) {
		return $time;
	}
	$local = date_create_immutable( $datum, wp_timezone() );
	if ( ! $local ) {
		return $time;
	}
	$date = $gmt ? $local->setTimezone( new DateTimeZone( 'UTC' ) ) : $local;
	return 'U' === $format ? $date->getTimestamp() : $date->format( $format );
}
add_filter( 'get_post_time', 'elfzwo_feed_einsatz_pubdate', 10, 3 );

/**
 * Der RSS-Feed soll zusätzlich unter der kurzen, gut merkbaren URL /rss.xml
 * erreichbar sein (viele Feed-Reader/-Verzeichnisse erwarten diese
 * Konvention), nicht nur unter dem WordPress-Standard /feed/. Leitet intern
 * auf denselben rss2-Feed weiter wie /feed/ (inkl. Beiträge + Einsätze,
 * siehe elfzwo_feed_include_einsaetze() oben) -- keine separate Feed-Logik.
 */
function elfzwo_add_rss_xml_rewrite() {
	add_rewrite_rule( '^rss\.xml$', 'index.php?feed=rss2', 'top' );
}
add_action( 'init', 'elfzwo_add_rss_xml_rewrite' );

/**
 * WordPress' eigener kanonischer Redirect kennt /rss.xml nicht als gültige
 * Feed-Struktur und würde sie fälschlich zu /rss.xml/feed/ "korrigieren" --
 * für diese eine URL unterdrücken, sonst bleibt /rss.xml unerreichbar.
 */
function elfzwo_skip_canonical_redirect_for_rss_xml( $redirect_url ) {
	if ( is_feed() && ! empty( $_SERVER['REQUEST_URI'] ) && false !== strpos( $_SERVER['REQUEST_URI'], '/rss.xml' ) ) {
		return false;
	}
	return $redirect_url;
}
add_filter( 'redirect_canonical', 'elfzwo_skip_canonical_redirect_for_rss_xml' );

/**
 * Die Kommentarverwaltung im Backend wird komplett ausgeblendet: Inhalte
 * ("Aktuelles") werden ausschließlich über Beiträge gepflegt, eine
 * separate Kommentar-Moderationsseite braucht es dafür nicht.
 */
function elfzwo_remove_comments_admin_menu() {
	remove_menu_page( 'edit-comments.php' );
}
add_action( 'admin_menu', 'elfzwo_remove_comments_admin_menu', 999 );

function elfzwo_remove_comments_admin_bar( $wp_admin_bar ) {
	$wp_admin_bar->remove_node( 'comments' );
}
add_action( 'admin_bar_menu', 'elfzwo_remove_comments_admin_bar', 999 );

/**
 * Direkter Aufruf von edit-comments.php (z. B. per Lesezeichen oder alter
 * Verlinkung) führt zurück ins Dashboard statt eine verwaiste Seite zu zeigen.
 */
function elfzwo_redirect_away_from_comments_screen() {
	global $pagenow;
	if ( 'edit-comments.php' === $pagenow ) {
		wp_safe_redirect( admin_url() );
		exit;
	}
}
add_action( 'admin_init', 'elfzwo_redirect_away_from_comments_screen' );
