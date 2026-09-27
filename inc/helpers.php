<?php
/**
 * Kleine Template-Helfer, die von mehreren Seiten-Templates genutzt werden.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Escaped eine E-Mail-Adresse für die Anzeige und setzt nach "@" und jedem
 * "." ein <wbr>, damit lange Adressen in schmalen Spalten an sinnvollen
 * Stellen umbrechen statt (mit break-all) mitten im Wort zu reißen.
 */
function elfzwo_wbr_email( $email ) {
	return str_replace( array( '@', '.' ), array( '@<wbr>', '.<wbr>' ), esc_html( $email ) );
}

/**
 * Fest gekürzter Auszug eines Beitrags, unabhängig von WordPress' eigenem
 * get_the_excerpt()/wp_trim_excerpt(): Core-Blöcke wie core/post-excerpt
 * setzen den 'excerpt_length'-Filter beim Rendern kurzzeitig auf 101 und
 * entfernen ihn nicht immer sauber wieder — das "leakt" dann in andere
 * Requests desselben PHP-Prozesses (z. B. admin-ajax.php) und führt zu
 * unterschiedlich langen Vorschautexten je nach Aufrufweg. Diese Funktion
 * kürzt daher selbst, mit fester Wortanzahl, immer gleich.
 */
function elfzwo_excerpt( $post_id, $words = 55 ) {
	$post = get_post( $post_id );
	if ( ! $post ) {
		return '';
	}
	$source = $post->post_excerpt ? $post->post_excerpt : $post->post_content;
	$text   = wp_strip_all_tags( strip_shortcodes( $source ) );
	return wp_trim_words( $text, $words, '…' );
}

/** Zugewiesene Kategorien eines Beitrags als "·"-getrennter String für die Anzeige (ersetzt die frühere freie Kicker-Zeile). */
function elfzwo_post_category_line( $post_id ) {
	$categories = get_the_category( $post_id );
	if ( ! $categories ) {
		return '';
	}
	return implode( ' · ', wp_list_pluck( $categories, 'name' ) );
}

/** Zusätzliche Bilder eines Beitrags ("bilder"-Galerie-Feld) als Attachment-ID-Array. */
function elfzwo_post_gallery_ids( $post_id ) {
	$ids = array_filter( array_map( 'intval', explode( ',', elfzwo_meta( $post_id, 'bilder', '' ) ) ) );
	return array_values( $ids );
}

/**
 * Alle Bilder eines Beitrags für das Carousel: Beitragsbild (falls gesetzt)
 * zuerst — dient als Vorschaubild in Listen —, danach die weiteren
 * Galerie-Bilder, doppelte IDs entfernt.
 */
function elfzwo_post_all_image_ids( $post_id ) {
	$ids = array();
	if ( has_post_thumbnail( $post_id ) ) {
		$ids[] = (int) get_post_thumbnail_id( $post_id );
	}
	foreach ( elfzwo_post_gallery_ids( $post_id ) as $gallery_id ) {
		if ( ! in_array( $gallery_id, $ids, true ) ) {
			$ids[] = $gallery_id;
		}
	}
	return $ids;
}

/** Erstes verfügbares Bild eines Beitrags (Beitragsbild oder erstes Galerie-Bild) als URL, mit Platzhalter-Fallback. */
function elfzwo_post_cover_image_url( $post_id, $size = 'large', $placeholder = '' ) {
	$ids = elfzwo_post_all_image_ids( $post_id );
	if ( $ids ) {
		return wp_get_attachment_image_url( $ids[0], $size );
	}
	return $placeholder;
}

function elfzwo_initials( $name ) {
	$parts    = preg_split( '/\s+/', trim( $name ) );
	$initials = '';
	foreach ( array_slice( $parts, 0, 2 ) as $part ) {
		$initials .= mb_strtoupper( mb_substr( $part, 0, 1 ) );
	}
	return $initials;
}

/**
 * Baut einen einfachen zweistufigen Menübaum für eine Menüposition.
 * Wird statt eines Walker_Nav_Menu genutzt, damit die verschachtelte
 * Dropdown-Ausgabe (Wrapper-Div + Panel) ohne Walker-Aufruf-Reihenfolge-
 * Fallstricke direkt und nachvollziehbar im Template geschrieben werden kann.
 *
 * @return array<int, array{item: WP_Post, children: WP_Post[]}>
 */
function elfzwo_get_menu_tree( $location ) {
	$locations = get_nav_menu_locations();
	if ( empty( $locations[ $location ] ) ) {
		return array();
	}
	$items = wp_get_nav_menu_items( $locations[ $location ] );
	if ( ! $items ) {
		return array();
	}
	usort(
		$items,
		function ( $a, $b ) {
			return $a->menu_order <=> $b->menu_order;
		}
	);

	$children_by_parent = array();
	$top_level          = array();
	foreach ( $items as $item ) {
		$parent_id = (int) $item->menu_item_parent;
		if ( $parent_id ) {
			$children_by_parent[ $parent_id ][] = $item;
		} else {
			$top_level[] = $item;
		}
	}

	$tree = array();
	foreach ( $top_level as $item ) {
		$tree[] = array(
			'item'     => $item,
			'children' => $children_by_parent[ $item->ID ] ?? array(),
		);
	}
	return $tree;
}
