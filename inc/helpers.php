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

/** Zugewiesene Kategorien eines Beitrags als "·"-getrennter String für die Anzeige. */
function elfzwo_post_category_line( $post_id ) {
	$categories = get_the_category( $post_id );
	if ( ! $categories ) {
		return '';
	}
	return implode( ' · ', wp_list_pluck( $categories, 'name' ) );
}

/** Beitragsbild eines Beitrags als URL, oder leerer String, wenn keins gesetzt ist. */
function elfzwo_post_cover_image_url( $post_id, $size = 'large' ) {
	return has_post_thumbnail( $post_id ) ? (string) get_the_post_thumbnail_url( $post_id, $size ) : '';
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

/**
 * Einmalig: "Einsätze & Aktuelles" aufteilen. Die Seite /einsaetze/ heißt nur
 * noch "Einsätze" und zeigt die Einsatztabelle; die Beiträge wandern als
 * Aktuelles-Block auf die Verein-Seite (vor den Aufruf am Ende, sonst ans Ende).
 */
function elfzwo_migrate_aktuelles_auf_verein() {
	if ( get_option( 'elfzwo_aktuelles_auf_verein' ) ) {
		return;
	}
	update_option( 'elfzwo_aktuelles_auf_verein', 1 );

	$einsaetze = get_page_by_path( 'einsaetze' );
	if ( $einsaetze ) {
		$blocks = array_values(
			array_filter(
				parse_blocks( $einsaetze->post_content ),
				function ( $b ) {
					return 'elfzwo/aktuelles-feed' !== $b['blockName'];
				}
			)
		);
		foreach ( $blocks as $i => $b ) {
			if ( 'elfzwo/section-heading' === $b['blockName'] && 'Einsätze & Aktuelles' === ( $b['attrs']['title'] ?? '' ) ) {
				$blocks[ $i ]['attrs']['title'] = 'Einsätze';
			}
		}
		_wp_put_post_revision( $einsaetze );
		wp_update_post(
			array(
				'ID'           => $einsaetze->ID,
				'post_title'   => 'Einsätze & Aktuelles' === $einsaetze->post_title ? 'Einsätze' : $einsaetze->post_title,
				'post_content' => wp_slash( serialize_blocks( $blocks ) ),
			)
		);
	}

	$verein = get_page_by_path( 'verein' );
	if ( $verein && ! has_block( 'elfzwo/aktuelles-feed', $verein ) ) {
		$blocks = parse_blocks( $verein->post_content );
		$neu    = array(
			'blockName'    => 'elfzwo/aktuelles-feed',
			'attrs'        => array(),
			'innerBlocks'  => array(),
			'innerHTML'    => '',
			'innerContent' => array(),
		);
		$stelle = count( $blocks );
		foreach ( $blocks as $i => $b ) {
			if ( 'elfzwo/cta-banner' === $b['blockName'] ) {
				$stelle = $i;
			}
		}
		array_splice( $blocks, $stelle, 0, array( $neu ) );
		_wp_put_post_revision( $verein );
		wp_update_post( array( 'ID' => $verein->ID, 'post_content' => wp_slash( serialize_blocks( $blocks ) ) ) );
	}

	// Menüpunkte mit eigenem Titel umbenennen (sonst übernimmt WordPress den Seitentitel).
	foreach ( get_posts( array( 'post_type' => 'nav_menu_item', 'numberposts' => -1, 'post_status' => 'any' ) ) as $eintrag ) {
		if ( in_array( $eintrag->post_title, array( 'Einsätze & Aktuelles', 'Einsätze &amp; Aktuelles', 'Einsätze &#038; Aktuelles' ), true ) ) {
			wp_update_post( array( 'ID' => $eintrag->ID, 'post_title' => 'Einsätze' ) );
		}
	}
}
add_action( 'init', 'elfzwo_migrate_aktuelles_auf_verein', 30 );
