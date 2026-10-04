<?php
/**
 * "Mach mit"-Formular: Jede Einsendung wird als "Mitmachen-Anfrage" im
 * Backend gespeichert (eigene Tabellenansicht unter "Mitmachen-Anfragen")
 * und zusätzlich per Benachrichtigungs-Mail gemeldet. Die Empfänger der
 * Benachrichtigung werden im Block "Mach-mit-Formular" gepflegt.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* --------------------------------------------------------- Speicherung */

function elfzwo_register_mitmachen_anfrage() {
	register_post_type(
		'mitmachen_anfrage',
		array(
			'labels'              => array(
				'name'               => 'Mitmachen-Anfragen',
				'singular_name'      => 'Mitmachen-Anfrage',
				'menu_name'          => 'Mitmachen-Anfragen',
				'all_items'          => 'Alle Anfragen',
				'edit_item'          => 'Anfrage ansehen',
				'search_items'       => 'Anfragen durchsuchen',
				'not_found'          => 'Noch keine Anfragen.',
				'not_found_in_trash' => 'Keine Anfragen im Papierkorb.',
			),
			'public'              => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => false,
			'show_in_rest'        => false,
			'menu_position'       => 26,
			'menu_icon'           => 'dashicons-groups',
			'supports'            => array( 'title' ),
			'map_meta_cap'        => true,
			// Anfragen entstehen nur über das Formular, nicht per Hand im Backend.
			'capabilities'        => array( 'create_posts' => 'do_not_allow' ),
			'rewrite'             => false,
		)
	);
}
add_action( 'init', 'elfzwo_register_mitmachen_anfrage' );

function elfzwo_mitmachen_anfrage_columns( $columns ) {
	return array(
		'cb'        => $columns['cb'],
		'title'     => 'Name',
		'email'     => 'E-Mail',
		'interesse' => 'Interesse',
		'seite'     => 'Formular',
		'date'      => 'Eingang',
	);
}
add_filter( 'manage_mitmachen_anfrage_posts_columns', 'elfzwo_mitmachen_anfrage_columns' );

function elfzwo_mitmachen_anfrage_column( $column, $post_id ) {
	if ( 'email' === $column ) {
		$email = get_post_meta( $post_id, '_elfzwo_anfrage_email', true );
		if ( $email ) {
			printf( '<a href="mailto:%1$s">%2$s</a>', esc_attr( $email ), esc_html( $email ) );
		}
	} elseif ( 'interesse' === $column ) {
		echo esc_html( get_post_meta( $post_id, '_elfzwo_anfrage_interesse', true ) );
	} elseif ( 'seite' === $column ) {
		$page_id = (int) get_post_meta( $post_id, '_elfzwo_anfrage_seite', true );
		if ( $page_id && get_post( $page_id ) ) {
			printf( '<a href="%1$s" target="_blank">%2$s</a>', esc_url( get_permalink( $page_id ) ), esc_html( get_the_title( $page_id ) ) );
		}
	}
}
add_action( 'manage_mitmachen_anfrage_posts_custom_column', 'elfzwo_mitmachen_anfrage_column', 10, 2 );

function elfzwo_mitmachen_anfrage_sortable( $columns ) {
	$columns['interesse'] = 'interesse';
	return $columns;
}
add_filter( 'manage_edit-mitmachen_anfrage_sortable_columns', 'elfzwo_mitmachen_anfrage_sortable' );

function elfzwo_mitmachen_anfrage_orderby( $query ) {
	if ( is_admin() && $query->is_main_query() && 'interesse' === $query->get( 'orderby' ) && 'mitmachen_anfrage' === $query->get( 'post_type' ) ) {
		$query->set( 'meta_key', '_elfzwo_anfrage_interesse' );
		$query->set( 'orderby', 'meta_value' );
	}
}
add_action( 'pre_get_posts', 'elfzwo_mitmachen_anfrage_orderby' );

/** In der Liste nur "Ansehen" und "Papierkorb" — keine Schnellbearbeitung. */
function elfzwo_mitmachen_anfrage_row_actions( $actions, $post ) {
	if ( 'mitmachen_anfrage' === $post->post_type ) {
		unset( $actions['inline hide-if-no-js'] );
		if ( isset( $actions['edit'] ) ) {
			$actions['edit'] = sprintf( '<a href="%s">Ansehen</a>', esc_url( get_edit_post_link( $post->ID ) ) );
		}
	}
	return $actions;
}
add_filter( 'post_row_actions', 'elfzwo_mitmachen_anfrage_row_actions', 10, 2 );

/** Detailansicht einer Anfrage (nur lesend). */
function elfzwo_mitmachen_anfrage_meta_box() {
	add_meta_box( 'elfzwo_anfrage_details', 'Anfrage', 'elfzwo_render_mitmachen_anfrage_meta_box', 'mitmachen_anfrage', 'normal', 'high' );
}
add_action( 'add_meta_boxes_mitmachen_anfrage', 'elfzwo_mitmachen_anfrage_meta_box' );

function elfzwo_render_mitmachen_anfrage_meta_box( $post ) {
	$email   = get_post_meta( $post->ID, '_elfzwo_anfrage_email', true );
	$page_id = (int) get_post_meta( $post->ID, '_elfzwo_anfrage_seite', true );
	$rows    = array(
		'Name'      => esc_html( $post->post_title ),
		'E-Mail'    => $email ? sprintf( '<a href="mailto:%1$s">%2$s</a>', esc_attr( $email ), esc_html( $email ) ) : '',
		'Interesse' => esc_html( get_post_meta( $post->ID, '_elfzwo_anfrage_interesse', true ) ),
		'Formular'  => $page_id && get_post( $page_id ) ? sprintf( '<a href="%1$s" target="_blank">%2$s</a>', esc_url( get_permalink( $page_id ) ), esc_html( get_the_title( $page_id ) ) ) : '',
		'Eingang'   => esc_html( get_the_date( 'd.m.Y, H:i', $post ) . ' Uhr' ),
	);
	echo '<table class="form-table"><tbody>';
	foreach ( $rows as $label => $value ) {
		printf( '<tr><th style="width:160px;text-align:left;">%1$s</th><td>%2$s</td></tr>', esc_html( $label ), $value ); // phpcs:ignore -- $value ist bereits escaped
	}
	echo '</tbody></table>';
}

/* ------------------------------------------------------ Formular-Versand */

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
 * Benachrichtigungs-Empfänger, direkt aus dem gespeicherten Block der Seite
 * gelesen (nie aus dem abgeschickten Formular), damit sie nicht von außen
 * manipuliert werden können. Leer = Admin-E-Mail der Website.
 */
function elfzwo_mitmachen_empfaenger( $page_id ) {
	$post   = get_post( $page_id );
	$blocks = $post ? elfzwo_mitmachen_find_blocks( parse_blocks( $post->post_content ) ) : array();
	$raw    = $blocks ? ( $blocks[0]['attrs']['notifyEmail'] ?? '' ) : '';

	$emails = array();
	foreach ( preg_split( '/[,;\s]+/', (string) $raw ) as $part ) {
		$email = trim( $part );
		if ( $email && is_email( $email ) ) {
			$emails[] = $email;
		}
	}
	return $emails ? $emails : array( get_option( 'admin_email' ) );
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

	$anfrage_id = wp_insert_post(
		array(
			'post_type'   => 'mitmachen_anfrage',
			'post_status' => 'publish',
			'post_title'  => $name,
			'meta_input'  => array(
				'_elfzwo_anfrage_email'     => $kontakt,
				'_elfzwo_anfrage_interesse' => $interesse,
				'_elfzwo_anfrage_seite'     => $page_id,
			),
		)
	);

	$text = sprintf(
		"Neue Mach-mit-Anfrage über die Website:\n\nName: %1\$s\nE-Mail: %2\$s\nInteresse: %3\$s\n\nAlle Anfragen im Backend: %4\$s",
		$name,
		$kontakt,
		$interesse ?: '–',
		admin_url( 'edit.php?post_type=mitmachen_anfrage' )
	);
	wp_mail( elfzwo_mitmachen_empfaenger( $page_id ), 'Neue Mach-mit-Anfrage von ' . $name, $text, array( 'Reply-To: ' . $kontakt ) );

	wp_safe_redirect( add_query_arg( 'mitmachen', $anfrage_id && ! is_wp_error( $anfrage_id ) ? 'success' : 'error', get_permalink( $page_id ) ) );
	exit;
}
add_action( 'admin_post_elfzwo_mitmachen', 'elfzwo_handle_mitmachen_submit' );
add_action( 'admin_post_nopriv_elfzwo_mitmachen', 'elfzwo_handle_mitmachen_submit' );

/**
 * Früher hatte der Block je Gruppe (Aktive, Jugend/Kinder, Verein) eigene
 * Empfänger, Betreff und Text. Läuft einmalig nach dem Update (auch auf dem
 * Produktivserver): alle bisherigen Empfänger werden zur einen
 * Benachrichtigungs-E-Mail zusammengefasst, die alten Einstellungen entfernt.
 */
function elfzwo_migrate_mitmachen_mail() {
	if ( get_option( 'elfzwo_mitmachen_mail_migrated' ) ) {
		return;
	}
	$posts = get_posts(
		array(
			'post_type'   => array( 'page', 'post', 'wp_block' ),
			'post_status' => 'any',
			'numberposts' => -1,
			's'           => 'wp:elfzwo/mitmachen-form',
		)
	);
	foreach ( $posts as $post ) {
		$changed = false;
		$blocks  = elfzwo_migrate_mitmachen_mail_blocks( parse_blocks( $post->post_content ), $changed );
		if ( $changed ) {
			wp_update_post(
				array(
					'ID'           => $post->ID,
					'post_content' => wp_slash( serialize_blocks( $blocks ) ),
				)
			);
		}
	}
	update_option( 'elfzwo_mitmachen_mail_migrated', 1 );
}
add_action( 'init', 'elfzwo_migrate_mitmachen_mail', 31 );

function elfzwo_migrate_mitmachen_mail_blocks( $blocks, &$changed ) {
	foreach ( $blocks as $i => $block ) {
		if ( ! empty( $block['innerBlocks'] ) ) {
			$blocks[ $i ]['innerBlocks'] = elfzwo_migrate_mitmachen_mail_blocks( $block['innerBlocks'], $changed );
		}
		if ( 'elfzwo/mitmachen-form' !== $block['blockName'] ) {
			continue;
		}
		$attrs = $blocks[ $i ]['attrs'];
		if ( isset( $attrs['mail'] ) && is_array( $attrs['mail'] ) ) {
			$emails = array();
			foreach ( $attrs['mail'] as $gruppe ) {
				foreach ( preg_split( '/[,;\s]+/', (string) ( $gruppe['empfaenger'] ?? '' ) ) as $part ) {
					if ( is_email( trim( $part ) ) ) {
						$emails[] = trim( $part );
					}
				}
			}
			if ( $emails && empty( $attrs['notifyEmail'] ) ) {
				$attrs['notifyEmail'] = implode( ', ', array_unique( $emails ) );
			}
			unset( $attrs['mail'] );
			$changed = true;
		}
		if ( ! empty( $attrs['interests'] ) && is_array( $attrs['interests'] ) ) {
			foreach ( $attrs['interests'] as $k => $interest ) {
				if ( isset( $interest['gruppe'] ) ) {
					unset( $attrs['interests'][ $k ]['gruppe'] );
					$changed = true;
				}
			}
		}
		$blocks[ $i ]['attrs'] = $attrs;
	}
	return $blocks;
}

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
