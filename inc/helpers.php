<?php
/**
 * Kleine Template-Helfer, die von mehreren Seiten-Templates genutzt werden.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
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

/** Beitragsbild eines Beitrags als URL, oder leerer String, wenn keins gesetzt ist. */
function elfzwo_post_cover_image_url( $post_id, $size = 'large' ) {
	return has_post_thumbnail( $post_id ) ? (string) get_the_post_thumbnail_url( $post_id, $size ) : '';
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
 * Ziel aller "Mach mit"-Links: das Formular am Ende der Startseite, optional
 * mit Vorauswahl (z. B. elfzwo_mitmachen_url( 'jugend' )).
 */
function elfzwo_mitmachen_url( $interesse = '' ) {
	$url = home_url( '/' );
	if ( $interesse ) {
		$url = add_query_arg( 'interesse', $interesse, $url );
	}
	return $url . '#mitmachen';
}

/** Alte Links auf /mitmachen/ (auch mit ?interesse=) dauerhaft zum Formular auf der Startseite. */
function elfzwo_mitmachen_weiterleitung() {
	$pfad = trim( (string) wp_parse_url( $_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH ), '/' );
	if ( 'mitmachen' !== $pfad ) {
		return;
	}
	$interesse = isset( $_GET['interesse'] ) ? sanitize_title( wp_unslash( $_GET['interesse'] ) ) : '';
	wp_safe_redirect( elfzwo_mitmachen_url( $interesse ), 301 );
	exit;
}
add_action( 'template_redirect', 'elfzwo_mitmachen_weiterleitung', 1 );

/** Anzeigename eines Inhaltstyps in Suchergebnissen und Vorschlägen. */
function elfzwo_typ_name( $typ ) {
	$namen = array(
		'page'     => 'Seite',
		'post'     => 'Beitrag',
		'einsatz'  => 'Einsatz',
		'download' => 'Download',
		'fahrzeug' => 'Fahrzeug',
		'termin'   => 'Termin',
	);
	return $namen[ $typ ] ?? '';
}

/**
 * Suchfeld mit Vorschlägen beim Tippen (assets/js/suche.js, über die
 * WordPress-Suche der REST-API). Ohne JavaScript ein normales Suchformular.
 */
function elfzwo_suchfeld( $wert = '' ) {
	ob_start();
	?>
	<form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="relative mx-auto w-full max-w-xl text-left" data-suche>
		<label for="elfzwo-suche" class="sr-only">Website durchsuchen</label>
		<div class="flex">
			<input id="elfzwo-suche" type="search" name="s" value="<?php echo esc_attr( $wert ); ?>" placeholder="Wonach suchst du? z. B. Jugendfeuerwehr" autocomplete="off" role="combobox" aria-expanded="false" aria-controls="elfzwo-suche-vorschlaege" aria-autocomplete="list" class="min-w-0 flex-1 border border-r-0 border-border bg-white px-4 py-3 text-base focus:border-foreground focus:outline-none">
			<button type="submit" class="elfzwo-btn elfzwo-btn-primary">Suchen</button>
		</div>
		<ul id="elfzwo-suche-vorschlaege" role="listbox" class="absolute inset-x-0 top-full z-20 hidden border border-t-0 border-border bg-white shadow-lg" data-vorschlaege></ul>
	</form>
	<?php
	return ob_get_clean();
}

/**
 * Vorschläge für das Suchfeld: Titel, Adresse und Typ der besten Treffer.
 * Eigene Route, weil die Standard-Suche der REST-API Einsätze ohne Titel liefert.
 */
function elfzwo_suche_vorschlaege( WP_REST_Request $anfrage ) {
	$begriff = trim( (string) $anfrage->get_param( 'q' ) );
	if ( mb_strlen( $begriff ) < 2 ) {
		return array();
	}
	$treffer = get_posts(
		array(
			's'                => $begriff,
			'post_type'        => array( 'page', 'post', 'einsatz', 'download', 'fahrzeug' ),
			'post_status'      => 'publish',
			'posts_per_page'   => 6,
			'suppress_filters' => false,
		)
	);
	return array_map(
		function ( $post ) {
			$titel = wp_specialchars_decode( get_the_title( $post ), ENT_QUOTES );
			$typ   = elfzwo_typ_name( $post->post_type );
			if ( 'einsatz' === $post->post_type ) {
				$typ = 'Einsatz vom ' . date_i18n( 'd.m.Y', strtotime( elfzwo_einsatz_zeitpunkt( $post->ID ) ) );
			}
			return array( 'titel' => $titel, 'url' => get_permalink( $post ), 'typ' => $typ );
		},
		$treffer
	);
}

function elfzwo_suche_route() {
	register_rest_route(
		'elfzwo/v1',
		'/suche',
		array(
			'methods'             => 'GET',
			'callback'            => 'elfzwo_suche_vorschlaege',
			'permission_callback' => '__return_true',
			'args'                => array( 'q' => array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ) ),
		)
	);
}
add_action( 'rest_api_init', 'elfzwo_suche_route' );

function elfzwo_lizenzen_weiterleitung() {
	if ( 'lizenzen' === trim( (string) wp_parse_url( $_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH ), '/' ) ) {
		wp_safe_redirect( home_url( '/impressum/#lizenzen' ), 301 );
		exit;
	}
}
add_action( 'template_redirect', 'elfzwo_lizenzen_weiterleitung', 1 );
