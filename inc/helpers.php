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
 * Der "Werde Teil des Teams"-Button unter den Ansprechpartnern (HTML-Block
 * auf Mannschaft und Jugendfeuerwehr) war ein voller roter Button. Primary
 * ist nur noch für Hero und Navigation da, deshalb wird er einmalig auf den
 * Secondary-Button umgestellt.
 */
function elfzwo_migrate_team_button() {
	if ( get_option( 'elfzwo_team_button_migrated' ) ) {
		return;
	}
	$old   = 'class="flex items-center justify-center gap-3 rounded-full bg-signal px-7 py-4 text-base font-semibold text-signal-foreground transition-colors hover:bg-signal/90"';
	$new   = 'class="elfzwo-btn elfzwo-btn-secondary w-full"';
	$pages = get_posts(
		array(
			'post_type'   => 'page',
			'post_status' => 'any',
			'numberposts' => -1,
			's'           => 'Werde Teil des Teams',
		)
	);
	foreach ( $pages as $page ) {
		if ( false === strpos( $page->post_content, $old ) ) {
			continue;
		}
		wp_update_post(
			array(
				'ID'           => $page->ID,
				'post_content' => wp_slash( str_replace( $old, $new, $page->post_content ) ),
			)
		);
	}
	update_option( 'elfzwo_team_button_migrated', 1 );
}
add_action( 'init', 'elfzwo_migrate_team_button', 30 );

/**
 * Das frühere Feld "Weitere Bilder" (Carousel) gibt es nicht mehr. Läuft
 * einmalig nach dem Update (auch auf dem Produktivserver, beim ersten
 * Seitenaufruf): Hat ein Beitrag kein Beitragsbild, wird das erste Galerie-Bild
 * zum Beitragsbild; die übrigen Bilder werden wie über "Dateien hinzufügen"
 * ans Ende des Beitragstexts gehängt. Danach wird das alte Feld gelöscht.
 */
function elfzwo_migrate_post_gallery() {
	if ( get_option( 'elfzwo_post_gallery_migrated' ) ) {
		return;
	}
	if ( get_transient( 'elfzwo_post_gallery_migration_lock' ) ) {
		return;
	}
	set_transient( 'elfzwo_post_gallery_migration_lock', 1, 5 * MINUTE_IN_SECONDS );

	$posts = get_posts(
		array(
			'post_type'        => 'post',
			'post_status'      => 'any',
			'numberposts'      => -1,
			'meta_key'         => '_elfzwo_bilder',
			'suppress_filters' => true,
		)
	);
	foreach ( $posts as $post ) {
		$ids = array_values( array_filter( array_map( 'intval', explode( ',', (string) get_post_meta( $post->ID, '_elfzwo_bilder', true ) ) ) ) );
		$ids = array_values( array_filter( $ids, 'wp_attachment_is_image' ) );

		$thumbnail_id = (int) get_post_thumbnail_id( $post->ID );
		if ( ! $thumbnail_id && $ids ) {
			$thumbnail_id = array_shift( $ids );
			set_post_thumbnail( $post->ID, $thumbnail_id );
		}

		$images = '';
		foreach ( $ids as $id ) {
			$src = wp_get_attachment_image_src( $id, 'large' );
			if ( $id === $thumbnail_id || ! $src || false !== strpos( $post->post_content, 'wp-image-' . $id . '"' ) ) {
				continue;
			}
			$alt     = get_post_meta( $id, '_wp_attachment_image_alt', true );
			$images .= sprintf(
				"\n\n" . '<img class="alignnone size-large wp-image-%1$d" src="%2$s" alt="%3$s" width="%4$d" height="%5$d" />',
				$id,
				esc_url( $src[0] ),
				esc_attr( $alt ),
				(int) $src[1],
				(int) $src[2]
			);
		}
		if ( $images ) {
			wp_update_post(
				array(
					'ID'           => $post->ID,
					'post_content' => wp_slash( rtrim( $post->post_content ) . $images ),
				)
			);
		}
		delete_post_meta( $post->ID, '_elfzwo_bilder' );
	}

	update_option( 'elfzwo_post_gallery_migrated', 1 );
	delete_transient( 'elfzwo_post_gallery_migration_lock' );
}
add_action( 'init', 'elfzwo_migrate_post_gallery', 30 );

/**
 * Beiträge lassen sich keinem Einsatz mehr zuordnen. Läuft einmalig nach dem
 * Update (auch auf dem Produktivserver) und löscht die alten Zuordnungen.
 */
function elfzwo_migrate_drop_einsatz_bezug() {
	if ( get_option( 'elfzwo_einsatz_bezug_dropped' ) ) {
		return;
	}
	delete_post_meta_by_key( '_elfzwo_einsatz_bezug' );
	update_option( 'elfzwo_einsatz_bezug_dropped', 1 );
}
add_action( 'init', 'elfzwo_migrate_drop_einsatz_bezug', 30 );
