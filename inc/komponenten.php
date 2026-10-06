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
		<?php // Gleicher Schleier wie im Startseiten-Hero. ?>
		<div class="absolute inset-0 -z-10 bg-gradient-to-r from-wood/95 via-signal/90 to-signal/75" aria-hidden="true"></div>
		<div class="mx-auto max-w-3xl px-5 pb-24 pt-14 text-center md:pb-32 md:pt-20">
			<h1 class="font-display text-4xl font-black leading-tight md:text-5xl"><?php echo esc_html( $titel ); ?></h1>
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
 *     @type string $bild_format Tailwind-Seitenverhältnis des Hauptbilds, Standard 'aspect-[4/3]'.
 *     @type array  $button     array( 'text' => …, 'url' => … ).
 * }
 */
function elfzwo_render_bild_text( $args ) {
	$bild       = $args['bild'] ?? '';
	$bild2      = $args['bild2'] ?? '';
	$bild_links = 'links' === ( $args['bild_seite'] ?? 'rechts' );
	$button     = $args['button'] ?? array();
	$format     = $args['bild_format'] ?? 'aspect-[4/3]';
	ob_start();
	?>
	<section class="mx-auto max-w-7xl px-5 py-12 md:px-8 md:py-16">
		<div class="grid items-center gap-10 lg:grid-cols-2 lg:gap-16">
			<div class="<?php echo $bild_links ? 'lg:order-2' : ''; ?>">
				<?php if ( ! empty( $args['kicker'] ) ) : ?><p class="elfzwo-kicker"><?php echo esc_html( $args['kicker'] ); ?></p><?php endif; ?>
				<?php if ( ! empty( $args['titel'] ) ) : ?><h2 class="mt-2 font-display text-3xl font-black leading-tight text-signal md:text-5xl"><?php echo esc_html( $args['titel'] ); ?></h2><?php endif; ?>
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
						<img src="<?php echo esc_url( $bild2 ); ?>" alt="" loading="lazy" class="absolute top-0 aspect-[3/2] w-[52%] object-cover ring-[6px] ring-background <?php echo $bild_links ? 'right-[6%]' : 'left-[6%]'; ?>">
					<?php endif; ?>
					<img src="<?php echo esc_url( $bild ); ?>" alt="" loading="lazy" class="relative <?php echo esc_attr( $format ); ?> object-cover <?php echo $bild2 ? 'w-[78%] ' . ( $bild_links ? '' : 'ml-auto' ) : 'w-full'; ?>">
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
	<div class="flex h-full items-start gap-5 border border-border bg-card p-6">
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

/** Bild-URL aus den Block-Attributen "{key}Id" / "{key}Url". */
function elfzwo_block_bild( $attrs, $key, $fallback = '' ) {
	$id = (int) ( $attrs[ $key . 'Id' ] ?? 0 );
	return ( $id ? wp_get_attachment_image_url( $id, 'large' ) : ( $attrs[ $key . 'Url' ] ?? '' ) ) ?: $fallback;
}
