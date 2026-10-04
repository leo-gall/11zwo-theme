<?php
/**
 * Gemeinsame Seiten-Komponenten nach dem Vorbild der Aicher Ambulanz:
 * - Jumbotron: Kopfbereich jeder Seite mit Foto unter rotem Schleier,
 *   zentriertem Titel und geschwungener Unterkante.
 * - Bild-Text: Text auf weißem Grund neben einem oder zwei Fotos mit
 *   ungleich gerundeten Ecken.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @param array $args {
 *     @type string $titel     Überschrift (H1).
 *     @type string $untertitel Zeile unter dem Titel.
 *     @type string $text      Optionaler längerer Text.
 *     @type string $bild      Bild-URL (sonst Fahrzeugfoto des Themes).
 *     @type array  $buttons   Liste aus array( 'text' => …, 'url' => … ); der erste ist hervorgehoben.
 * }
 */
function elfzwo_render_jumbotron( $args ) {
	$titel      = $args['titel'] ?? '';
	$untertitel = $args['untertitel'] ?? '';
	$text       = $args['text'] ?? '';
	$bild       = ( $args['bild'] ?? '' ) ?: get_template_directory_uri() . '/assets/images/hero-hintergrund.jpg';
	$buttons    = array_filter(
		$args['buttons'] ?? array(),
		function ( $b ) {
			return ! empty( $b['text'] );
		}
	);
	ob_start();
	?>
	<section class="relative isolate overflow-hidden bg-signal text-white">
		<img src="<?php echo esc_url( $bild ); ?>" alt="" class="absolute inset-0 -z-20 h-full w-full object-cover">
		<div class="absolute inset-0 -z-10 bg-signal/80" aria-hidden="true"></div>
		<div class="mx-auto max-w-3xl px-5 pb-24 pt-14 text-center md:pb-32 md:pt-20">
			<h1 class="font-display text-4xl font-bold leading-tight md:text-5xl"><?php echo esc_html( $titel ); ?></h1>
			<?php if ( $untertitel ) : ?><p class="mt-4 text-lg font-light text-white/90 md:text-xl"><?php echo esc_html( $untertitel ); ?></p><?php endif; ?>
			<?php if ( $text ) : ?><p class="mx-auto mt-4 max-w-2xl font-light leading-relaxed text-white/85"><?php echo esc_html( $text ); ?></p><?php endif; ?>
			<?php if ( $buttons ) : ?>
				<div class="mt-8 flex flex-wrap justify-center gap-3">
					<?php foreach ( array_values( $buttons ) as $i => $b ) : ?>
						<a href="<?php echo esc_url( $b['url'] ?? '#' ); ?>" class="elfzwo-btn <?php echo 0 === $i ? 'elfzwo-btn-light' : 'elfzwo-btn-outline-light'; ?>"><?php echo esc_html( $b['text'] ); ?></a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
		<svg class="absolute inset-x-0 bottom-0 h-12 w-full text-background md:h-20" viewBox="0 0 1440 100" preserveAspectRatio="none" aria-hidden="true"><path fill="currentColor" d="M0 80 C 380 115, 980 90, 1440 20 L1440 100 L0 100 Z"/></svg>
	</section>
	<?php
	return ob_get_clean();
}

/**
 * @param array $args {
 *     @type string $kicker     Zeile über der Überschrift.
 *     @type string $titel      Überschrift (H2).
 *     @type string $text       Fließtext (Absätze durch Leerzeile getrennt).
 *     @type string $inhalt     Zusätzliches, bereits escaptes HTML unter dem Text (Listen o. Ä.).
 *     @type string $bild       Hauptbild-URL.
 *     @type string $bild2      Optionales zweites, kleineres Bild (versetzt dahinter).
 *     @type string $bild_seite 'rechts' (Standard) oder 'links'.
 *     @type array  $button     array( 'text' => …, 'url' => … ).
 * }
 */
function elfzwo_render_bild_text( $args ) {
	$bild       = $args['bild'] ?? '';
	$bild2      = $args['bild2'] ?? '';
	$bild_links = 'links' === ( $args['bild_seite'] ?? 'rechts' );
	$button     = $args['button'] ?? array();
	ob_start();
	?>
	<section class="mx-auto max-w-7xl px-5 py-12 md:px-8 md:py-16">
		<div class="grid items-center gap-10 lg:grid-cols-2 lg:gap-16">
			<div class="<?php echo $bild_links ? 'lg:order-2' : ''; ?>">
				<?php if ( ! empty( $args['kicker'] ) ) : ?><p class="elfzwo-kicker"><?php echo esc_html( $args['kicker'] ); ?></p><?php endif; ?>
				<?php if ( ! empty( $args['titel'] ) ) : ?><h2 class="mt-2 font-display text-3xl font-bold leading-tight text-signal md:text-5xl"><?php echo esc_html( $args['titel'] ); ?></h2><?php endif; ?>
				<?php foreach ( array_filter( preg_split( "/\n\s*\n/", (string) ( $args['text'] ?? '' ) ) ) as $absatz ) : ?>
					<p class="mt-5 font-light leading-relaxed text-foreground/80"><?php echo esc_html( trim( $absatz ) ); ?></p>
				<?php endforeach; ?>
				<?php echo $args['inhalt'] ?? ''; // phpcs:ignore -- vom Aufrufer escaped ?>
				<?php if ( ! empty( $button['text'] ) ) : ?>
					<a href="<?php echo esc_url( $button['url'] ?? '#' ); ?>" class="elfzwo-btn elfzwo-btn-primary mt-8"><?php echo esc_html( $button['text'] ); ?></a>
				<?php endif; ?>
			</div>
			<?php if ( $bild ) : ?>
				<div class="relative <?php echo $bild2 ? 'pt-[22%]' : ''; ?> <?php echo $bild_links ? 'lg:order-1' : ''; ?>">
					<?php if ( $bild2 ) : ?>
						<img src="<?php echo esc_url( $bild2 ); ?>" alt="" loading="lazy" class="elfzwo-bildform-b absolute top-0 aspect-[3/2] w-[52%] object-cover ring-[6px] ring-background <?php echo $bild_links ? 'right-[6%]' : 'left-[6%]'; ?>">
					<?php endif; ?>
					<img src="<?php echo esc_url( $bild ); ?>" alt="" loading="lazy" class="elfzwo-bildform-a relative aspect-[4/3] object-cover <?php echo $bild2 ? 'w-[78%] ' . ( $bild_links ? '' : 'ml-auto' ) : 'w-full'; ?>">
				</div>
			<?php endif; ?>
		</div>
	</section>
	<?php
	return ob_get_clean();
}

/** Prüft, ob der erste Block einer Seite schon einen eigenen Kopfbereich mitbringt. */
function elfzwo_seite_hat_kopf( $post ) {
	foreach ( parse_blocks( $post->post_content ) as $block ) {
		if ( $block['blockName'] ) {
			return in_array( $block['blockName'], array( 'elfzwo/home-hero', 'elfzwo/simple-hero', 'elfzwo/hero-split', 'elfzwo/jugendfeuerwehr-hero' ), true );
		}
	}
	return false;
}

/**
 * Jumbotron für Seiten ohne eigenen Kopfbereich. Beginnt die Seite mit einer
 * Abschnitts-Überschrift oder dem Mach-mit-Formular, wandern deren Texte in
 * das Jumbotron und werden dort nicht noch einmal gezeigt.
 */
function elfzwo_seiten_jumbotron( $post ) {
	$args  = array(
		'titel' => get_the_title( $post ),
		'bild'  => has_post_thumbnail( $post ) ? get_the_post_thumbnail_url( $post, 'full' ) : '',
	);
	$erste = null;
	foreach ( parse_blocks( $post->post_content ) as $block ) {
		if ( $block['blockName'] ) {
			$erste = $block;
			break;
		}
	}
	if ( $erste ) {
		$typ   = WP_Block_Type_Registry::get_instance()->get_registered( $erste['blockName'] );
		$attrs = $typ ? $typ->prepare_attributes_for_render( $erste['attrs'] ) : $erste['attrs'];
		if ( 'elfzwo/section-heading' === $erste['blockName'] && ! empty( $attrs['title'] ) ) {
			$args['titel']      = $attrs['title'];
			$args['untertitel'] = $attrs['tag'] ?? '';
			$GLOBALS['elfzwo_kopf_uebernommen'] = $erste['blockName'];
		} elseif ( 'elfzwo/mitmachen-form' === $erste['blockName'] ) {
			$args['titel']      = trim( ( $attrs['title'] ?? '' ) . ' ' . ( $attrs['titleHand'] ?? '' ) );
			$args['untertitel'] = $attrs['kicker'] ?? '';
			$args['text']       = $attrs['description'] ?? '';
			$GLOBALS['elfzwo_kopf_uebernommen'] = $erste['blockName'];
		}
	}
	return elfzwo_render_jumbotron( $args );
}

/** Ob der Kopf dieses Blocks schon vom Seiten-Jumbotron gezeigt wird (gilt nur einmal, für den ersten Block). */
function elfzwo_kopf_uebernommen( $block_name ) {
	if ( ( $GLOBALS['elfzwo_kopf_uebernommen'] ?? '' ) === $block_name ) {
		$GLOBALS['elfzwo_kopf_uebernommen'] = '';
		return true;
	}
	return false;
}

/**
 * Eine Ansprechpartner-Karte: Wappen mit Name und Rolle, darunter Telefon und
 * (falls freigegeben) E-Mail als schlichte Links — ohne Buttons.
 */
function elfzwo_render_ansprechpartner_karte( $person ) {
	$name  = $person['name'] ?? '';
	$rolle = $person['rolle'] ?? '';
	$tel   = $person['telefon'] ?? '';
	$email = ! empty( $person['showEmail'] ) ? ( $person['email'] ?? '' ) : '';
	if ( ! $name ) {
		return '';
	}
	ob_start();
	?>
	<div class="flex h-full items-start gap-5 rounded-2xl border border-border bg-card p-6">
		<div class="shrink-0"><?php echo elfzwo_person_wappen_svg( $name, $rolle, 56 ); // phpcs:ignore -- bereits escaped ?></div>
		<div class="min-w-0">
			<p class="font-display text-xl font-bold leading-tight"><?php echo esc_html( $name ); ?></p>
			<?php if ( $rolle ) : ?><p class="mt-0.5 text-signal"><?php echo esc_html( $rolle ); ?></p><?php endif; ?>
			<?php if ( $tel || $email ) : ?>
				<div class="mt-4 space-y-1.5 text-sm">
					<?php if ( $tel ) : ?>
						<a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $tel ) ); ?>" class="flex items-center gap-2.5 text-foreground/80 hover:text-signal"><?php echo elfzwo_icon( 'phone', 'h-4 w-4 shrink-0 text-signal' ); ?> <?php echo esc_html( $tel ); ?></a>
					<?php endif; ?>
					<?php if ( $email ) : ?>
						<a href="mailto:<?php echo esc_attr( $email ); ?>" class="flex min-w-0 items-center gap-2.5 text-foreground/80 hover:text-signal"><?php echo elfzwo_icon( 'mail', 'h-4 w-4 shrink-0 text-signal' ); ?> <span class="truncate"><?php echo esc_html( $email ); ?></span></a>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * Einmalige Migration: Die alten "Deine Ansprechpartner"-Abschnitte (Gruppe aus
 * Bereichen mit Einzelkarten und einem "Werde Teil des Teams!"-Button) werden
 * durch den Block "Ansprechpartner" ersetzt — ohne Button.
 */
function elfzwo_migrate_ansprechpartner() {
	if ( get_option( 'elfzwo_ansprechpartner_migrated' ) ) {
		return;
	}
	$sammeln = function ( $blocks ) use ( &$sammeln ) {
		$personen = array();
		foreach ( $blocks as $b ) {
			if ( 'elfzwo/related-person-card' === $b['blockName'] ) {
				$personen[] = array(
					'name'      => $b['attrs']['name'] ?? '',
					'rolle'     => $b['attrs']['rolle'] ?? '',
					'telefon'   => $b['attrs']['telefon'] ?? '',
					'email'     => $b['attrs']['email'] ?? '',
					'showEmail' => ! empty( $b['attrs']['showEmail'] ),
				);
			}
			$personen = array_merge( $personen, $sammeln( $b['innerBlocks'] ?? array() ) );
		}
		return $personen;
	};
	foreach ( get_posts( array( 'post_type' => 'page', 'post_status' => 'any', 'numberposts' => -1, 's' => 'related-person-card' ) ) as $page ) {
		$blocks  = parse_blocks( $page->post_content );
		$changed = false;
		foreach ( $blocks as $i => $block ) {
			if ( 'elfzwo/section' !== $block['blockName'] ) {
				continue;
			}
			$html = serialize_block( $block );
			if ( false === strpos( $html, 'Ansprechpartner' ) || false === strpos( $html, 'related-person-card' ) ) {
				continue;
			}
			$text = '';
			if ( preg_match( '#<h2[^>]*>(.*?)</h2>\s*<p[^>]*>(.*?)</p>#s', $html, $m ) ) {
				$text = trim( wp_strip_all_tags( $m[2] ) );
			}
			$blocks[ $i ] = array(
				'blockName'    => 'elfzwo/ansprechpartner',
				'attrs'        => array(
					'title'  => 'Deine Ansprechpartner',
					'text'   => $text,
					'people' => $sammeln( array( $block ) ),
				),
				'innerBlocks'  => array(),
				'innerHTML'    => '',
				'innerContent' => array(),
			);
			$changed = true;
		}
		if ( $changed ) {
			_wp_put_post_revision( $page );
			wp_update_post( array( 'ID' => $page->ID, 'post_content' => wp_slash( serialize_blocks( $blocks ) ) ) );
		}
	}
	update_option( 'elfzwo_ansprechpartner_migrated', 1 );
}
add_action( 'init', 'elfzwo_migrate_ansprechpartner', 41 );

/**
 * Einmalige Migration: Der alte Zeitstrahl (elfzwo/join-timeline) wird auf
 * allen Seiten durch den Block "Werdegang" ersetzt; ein Schritt ohne Nummer,
 * aber mit Button-Text wird zum Button unter den Stufen.
 */
function elfzwo_migrate_werdegang() {
	if ( get_option( 'elfzwo_werdegang_migrated' ) ) {
		return;
	}
	$umbauen = function ( $blocks, &$changed ) use ( &$umbauen ) {
		foreach ( $blocks as $i => $block ) {
			if ( ! empty( $block['innerBlocks'] ) ) {
				$blocks[ $i ]['innerBlocks'] = $umbauen( $block['innerBlocks'], $changed );
			}
			if ( 'elfzwo/join-timeline' !== $block['blockName'] ) {
				continue;
			}
			$attrs = array( 'stufen' => array() );
			foreach ( $block['attrs']['steps'] ?? array() as $step ) {
				if ( ! empty( $step['buttonText'] ) && empty( $step['number'] ) ) {
					$attrs['buttonText'] = $step['buttonText'];
					$attrs['buttonUrl']  = $step['buttonUrl'] ?? '';
				} elseif ( ! empty( $step['title'] ) ) {
					$attrs['stufen'][] = array( 'titel' => $step['title'], 'info' => '', 'text' => $step['body'] ?? '' );
				}
			}
			$blocks[ $i ] = array(
				'blockName'    => 'elfzwo/werdegang',
				'attrs'        => $attrs,
				'innerBlocks'  => array(),
				'innerHTML'    => '',
				'innerContent' => array(),
			);
			$changed = true;
		}
		return $blocks;
	};
	foreach ( get_posts( array( 'post_type' => array( 'page', 'post', 'wp_block' ), 'post_status' => 'any', 'numberposts' => -1, 's' => 'elfzwo/join-timeline' ) ) as $post ) {
		$changed = false;
		$blocks  = $umbauen( parse_blocks( $post->post_content ), $changed );
		if ( $changed ) {
			_wp_put_post_revision( $post );
			wp_update_post( array( 'ID' => $post->ID, 'post_content' => wp_slash( serialize_blocks( $blocks ) ) ) );
		}
	}
	update_option( 'elfzwo_werdegang_migrated', 1 );
}
add_action( 'init', 'elfzwo_migrate_werdegang', 42 );

/** Bild-URL aus den Block-Attributen "{key}Id" / "{key}Url". */
function elfzwo_block_bild( $attrs, $key, $fallback = '' ) {
	$id = (int) ( $attrs[ $key . 'Id' ] ?? 0 );
	return ( $id ? wp_get_attachment_image_url( $id, 'large' ) : ( $attrs[ $key . 'Url' ] ?? '' ) ) ?: $fallback;
}

/**
 * Wandelt einen alten "Mach mit"-Block (elfzwo/mitmachen-teaser) in die
 * getrennten Blöcke "Aktive Mannschaft" und "Kinder- & Jugendfeuerwehr" um.
 */
function elfzwo_mitmachen_teaser_aufteilen( $attrs ) {
	$teil   = $attrs['teil'] ?? 'beide';
	$leer   = array( 'innerBlocks' => array(), 'innerHTML' => '', 'innerContent' => array() );
	$blocks = array();
	if ( 'jugend' !== $teil ) {
		$blocks[] = array(
			'blockName' => 'elfzwo/aktive-mannschaft',
			'attrs'     => array(
				'titel'      => $attrs['aktiveTitel'] ?? 'Aktive Mannschaft',
				'text'       => $attrs['intro'] ?? '',
				'punkte'     => $attrs['punkte'] ?? array(),
				'bildId'     => (int) ( $attrs['aktiveImageId'] ?? 0 ),
				'bildUrl'    => $attrs['aktiveImageUrl'] ?? '',
				'bild2Id'    => (int) ( $attrs['aktiveImage2Id'] ?? 0 ),
				'bild2Url'   => $attrs['aktiveImage2Url'] ?? '',
				'buttonText' => $attrs['aktiveButtonText'] ?? 'Mach mit!',
				'buttonUrl'  => $attrs['aktiveButtonUrl'] ?? '/mitmachen/',
			),
		) + $leer;
	}
	if ( 'aktive' !== $teil ) {
		$blocks[] = array(
			'blockName' => 'elfzwo/jugend-teaser',
			'attrs'     => array(
				'titel'      => $attrs['jugendTitel'] ?? 'Kinder- & Jugendfeuerwehr',
				'text'       => $attrs['jugendText'] ?? '',
				'punkte'     => $attrs['jugendPunkte'] ?? array(),
				'bildId'     => (int) ( $attrs['jugendImageId'] ?? 0 ),
				'bildUrl'    => $attrs['jugendImageUrl'] ?? '',
				'buttonText' => $attrs['jugendButtonText'] ?? 'Alles zur Jugendfeuerwehr',
				'buttonUrl'  => $attrs['jugendButtonUrl'] ?? '/jugendfeuerwehr/',
			),
		) + $leer;
	}
	return $blocks;
}

/** Einmalige Migration: vorhandene "Mach mit"-Blöcke in die zwei neuen Blöcke aufteilen. */
function elfzwo_migrate_mitmachen_teaser_trennen() {
	if ( get_option( 'elfzwo_mitmachen_teaser_getrennt' ) ) {
		return;
	}
	foreach ( get_posts( array( 'post_type' => array( 'page', 'post', 'wp_block' ), 'post_status' => 'any', 'numberposts' => -1, 's' => 'elfzwo/mitmachen-teaser' ) ) as $post ) {
		$neu     = array();
		$changed = false;
		foreach ( parse_blocks( $post->post_content ) as $block ) {
			if ( 'elfzwo/mitmachen-teaser' === $block['blockName'] ) {
				$neu     = array_merge( $neu, elfzwo_mitmachen_teaser_aufteilen( $block['attrs'] ) );
				$changed = true;
			} else {
				$neu[] = $block;
			}
		}
		if ( $changed ) {
			_wp_put_post_revision( $post );
			wp_update_post( array( 'ID' => $post->ID, 'post_content' => wp_slash( serialize_blocks( $neu ) ) ) );
		}
	}
	update_option( 'elfzwo_mitmachen_teaser_getrennt', 1 );
}
add_action( 'init', 'elfzwo_migrate_mitmachen_teaser_trennen', 43 );

/**
 * Einmalige Aufräum-Migration: WordPress-Beispielseite in den Papierkorb und
 * nichtssagende Untertitel in Seitenköpfen ("Aus Greifenberg", "Wer wir
 * sind", …) leeren.
 */
function elfzwo_migrate_aufraeumen() {
	if ( get_option( 'elfzwo_aufraeumen_v1' ) ) {
		return;
	}
	foreach ( get_posts( array( 'post_type' => 'page', 'post_status' => 'any', 'numberposts' => -1, 'post_name__in' => array( 'beispiel-seite', 'sample-page' ) ) ) as $page ) {
		if ( false !== stripos( $page->post_content, 'Fahrradkurier' ) || false !== stripos( $page->post_content, 'bike messenger' ) ) {
			wp_trash_post( $page->ID );
		}
	}

	$fuelltexte = array( 'aus greifenberg', 'zum herunterladen', 'zum mitnehmen', 'wer wir sind' );
	$felder     = array(
		'elfzwo/section-heading' => 'tag',
		'elfzwo/simple-hero'     => 'kicker',
		'elfzwo/hero-split'      => 'badge',
	);
	foreach ( get_posts( array( 'post_type' => 'page', 'post_status' => 'any', 'numberposts' => -1 ) ) as $page ) {
		$blocks  = parse_blocks( $page->post_content );
		$changed = false;
		foreach ( $blocks as $i => $block ) {
			$feld = $felder[ $block['blockName'] ] ?? '';
			if ( $feld && isset( $block['attrs'][ $feld ] ) && in_array( mb_strtolower( trim( wp_specialchars_decode( $block['attrs'][ $feld ], ENT_QUOTES ) ) ), $fuelltexte, true ) ) {
				$blocks[ $i ]['attrs'][ $feld ] = '';
				$changed = true;
			}
		}
		if ( $changed ) {
			wp_update_post( array( 'ID' => $page->ID, 'post_content' => wp_slash( serialize_blocks( $blocks ) ) ) );
		}
	}
	update_option( 'elfzwo_aufraeumen_v1', 1 );
}
add_action( 'init', 'elfzwo_migrate_aufraeumen', 44 );
