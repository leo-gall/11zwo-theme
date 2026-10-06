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

/**
 * Einmalig: das Mach-mit-Formular (mit Gruppen und Mail-Einstellungen der
 * Mitmachen-Seite) ans Ende der Startseite setzen.
 */
function elfzwo_migrate_mitmachen_auf_startseite() {
	if ( get_option( 'elfzwo_mitmachen_auf_startseite' ) ) {
		return;
	}
	update_option( 'elfzwo_mitmachen_auf_startseite', 1 );
	$start = get_post( (int) get_option( 'page_on_front' ) );
	if ( ! $start || has_block( 'elfzwo/mitmachen-form', $start ) ) {
		return;
	}
	$vorlage = null;
	$quelle  = get_page_by_path( 'mitmachen' );
	if ( $quelle ) {
		foreach ( elfzwo_mitmachen_find_blocks( parse_blocks( $quelle->post_content ) ) as $block ) {
			$vorlage = $block;
			break;
		}
	}
	$vorlage = $vorlage ? $vorlage : array( 'blockName' => 'elfzwo/mitmachen-form', 'attrs' => array(), 'innerBlocks' => array(), 'innerHTML' => '', 'innerContent' => array() );
	$blocks  = parse_blocks( $start->post_content );
	$blocks[] = $vorlage;
	_wp_put_post_revision( $start );
	wp_update_post( array( 'ID' => $start->ID, 'post_content' => wp_slash( serialize_blocks( $blocks ) ) ) );
}
add_action( 'init', 'elfzwo_migrate_mitmachen_auf_startseite', 30 );

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

/** Einmalig: die eigene Mitmachen-Seite in den Papierkorb, sobald das Formular auf der Startseite steht. */
function elfzwo_migrate_mitmachen_seite_entfernen() {
	if ( get_option( 'elfzwo_mitmachen_seite_entfernt' ) ) {
		return;
	}
	$start = get_post( (int) get_option( 'page_on_front' ) );
	if ( ! $start || ! has_block( 'elfzwo/mitmachen-form', $start ) ) {
		return;
	}
	update_option( 'elfzwo_mitmachen_seite_entfernt', 1 );
	$seite = get_page_by_path( 'mitmachen' );
	if ( $seite && (int) $seite->ID !== (int) $start->ID ) {
		wp_trash_post( $seite->ID );
	}
}
add_action( 'init', 'elfzwo_migrate_mitmachen_seite_entfernen', 31 );

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

/** Alle Fremdbestandteile der Website für die Seite /lizenzen/ (page-lizenzen.php). */
function elfzwo_lizenzen() {
	return array(
		'Software'       => array(
			array(
				'name'       => 'WordPress',
				'url'        => 'https://wordpress.org/',
				'lizenz'     => 'GPL 2.0 oder später',
				'lizenz_url' => 'https://www.gnu.org/licenses/old-licenses/gpl-2.0.html',
				'hinweis'    => '© WordPress-Mitwirkende. Grundlage der Website und des Redaktionssystems.',
			),
			array(
				'name'       => 'Tailwind CSS',
				'url'        => 'https://tailwindcss.com/',
				'lizenz'     => 'MIT',
				'lizenz_url' => 'https://github.com/tailwindlabs/tailwindcss/blob/main/LICENSE',
				'hinweis'    => '© Tailwind Labs, Inc. Erzeugt die Gestaltungsklassen der Website.',
			),
			array(
				'name'       => 'Umami',
				'url'        => 'https://umami.is/',
				'lizenz'     => 'MIT',
				'lizenz_url' => 'https://github.com/umami-software/umami/blob/master/LICENSE',
				'hinweis'    => '© Umami Software, Inc. Cookielose Besucherstatistik (Umami Cloud).',
			),
			array(
				'name'       => 'esbuild',
				'url'        => 'https://esbuild.github.io/',
				'lizenz'     => 'MIT',
				'lizenz_url' => 'https://github.com/evanw/esbuild/blob/main/LICENSE.md',
				'hinweis'    => '© Evan Wallace. Verkleinert JavaScript und CSS beim Erstellen des Themes.',
			),
		),
		'Schrift'        => array(
			array(
				'name'       => 'Titillium Web',
				'url'        => 'https://fonts.google.com/specimen/Titillium+Web',
				'lizenz'     => 'SIL Open Font License 1.1',
				'lizenz_url' => 'https://openfontlicense.org/open-font-license-official-text/',
				'hinweis'    => '© Accademia di Belle Arti di Urbino. Wird von dieser Website selbst ausgeliefert, nicht von Google.',
			),
		),
		'Icons'          => array(
			array(
				'name'       => 'SVG Repo – handgezeichnete Icons',
				'url'        => 'https://www.svgrepo.com/vectors/hand-drawn/',
				'lizenz'     => 'CC0 1.0 (gemeinfrei)',
				'lizenz_url' => 'https://creativecommons.org/publicdomain/zero/1.0/deed.de',
				'hinweis'    => 'Die übrigen Icons der Website, z. B. Telefon, Kalender, Pfeile und Fahrzeug.',
			),
			array(
				'name'       => 'Lucide',
				'url'        => 'https://lucide.dev/',
				'lizenz'     => 'ISC',
				'lizenz_url' => 'https://lucide.dev/license',
				'hinweis'    => '© Lucide Contributors. Pfeil im Menü und RSS-Symbol.',
			),
		),
		'Daten & Dienste' => array(
			array(
				'name'       => 'NINA – Warn-App des Bundes',
				'url'        => 'https://warnung.bund.de/',
				'lizenz'     => 'Bundesamt für Bevölkerungsschutz und Katastrophenhilfe',
				'lizenz_url' => 'https://warnung.bund.de/impressum',
				'hinweis'    => 'Aktuelle Warnmeldungen für Greifenberg. Quelle: BBK, Warnsystem MoWaS.',
			),
			array(
				'name'       => 'OpenPLZ API',
				'url'        => 'https://www.openplzapi.org/',
				'lizenz'     => 'Daten: CC BY 4.0 / ODbL',
				'lizenz_url' => 'https://www.openplzapi.org/de/',
				'hinweis'    => 'Ordnet die Postleitzahl dem Gemeindeschlüssel für die Warnmeldungen zu.',
			),
		),
	);
}

/** Die Lizenzen stehen im Impressum; eine früher angelegte eigene Seite kommt weg. */
function elfzwo_migrate_lizenzen_ins_impressum() {
	if ( get_option( 'elfzwo_lizenzen_im_impressum' ) ) {
		return;
	}
	update_option( 'elfzwo_lizenzen_im_impressum', 1 );
	$seite = get_page_by_path( 'lizenzen' );
	if ( $seite && '' === trim( $seite->post_content ) ) {
		wp_trash_post( $seite->ID );
	}
}
add_action( 'init', 'elfzwo_migrate_lizenzen_ins_impressum', 30 );

function elfzwo_lizenzen_weiterleitung() {
	if ( 'lizenzen' === trim( (string) wp_parse_url( $_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH ), '/' ) ) {
		wp_safe_redirect( home_url( '/impressum/#lizenzen' ), 301 );
		exit;
	}
}
add_action( 'template_redirect', 'elfzwo_lizenzen_weiterleitung', 1 );
