<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

$fahrzeuge = new WP_Query( array( 'post_type' => 'fahrzeug', 'posts_per_page' => -1, 'orderby' => 'menu_order date', 'order' => 'ASC' ) );
?>
<section class="mx-auto max-w-7xl space-y-16 px-5 py-8 md:px-8">
	<?php
	$i = 0;
	while ( $fahrzeuge->have_posts() ) :
		$fahrzeuge->the_post();
		$fid  = get_the_ID();
		$tag  = elfzwo_meta( $fid, 'tag', '' );
		$img_url = has_post_thumbnail() ? get_the_post_thumbnail_url( $fid, 'large' ) : get_template_directory_uri() . '/assets/images/hero-team.jpg';

		$spec_fields = array(
			array( 'key' => 'besatzung', 'icon' => 'users', 'label' => 'Besatzung' ),
			array( 'key' => 'hersteller_aufbau', 'icon' => 'truck', 'label' => 'Hersteller / Aufbau' ),
			array( 'key' => 'baujahr', 'icon' => 'calendar-days', 'label' => 'Baujahr' ),
			array( 'key' => 'besonderheiten', 'icon' => 'list-checks', 'label' => 'Besonderheiten' ),
		);
		$specs = array();
		foreach ( $spec_fields as $sf ) {
			$specs[] = array(
				'icon'  => $sf['icon'],
				'label' => $sf['label'],
				'value' => elfzwo_meta( $fid, $sf['key'], '' ) ?: '–',
			);
		}
		$hotspots = elfzwo_hotspots_decode( get_post_meta( $fid, '_elfzwo_geraetefaecher', true ) );
		$reverse  = ( 1 === $i % 2 );
		$i++;
		?>
		<article class="grid gap-8 md:grid-cols-2 md:items-center" data-fahrzeug="<?php echo esc_attr( get_the_title() ); ?>">
			<div class="<?php echo $reverse ? 'md:order-2' : ''; ?>">
				<div class="relative">
					<div class="relative">
						<img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" width="1200" height="800" loading="lazy" class="block w-full object-cover <?php echo $reverse ? 'elfzwo-bildform-b' : 'elfzwo-bildform-a'; ?>">
						<?php foreach ( $hotspots as $h => $spot ) : ?>
							<?php $bild_url = $spot['bild'] ? wp_get_attachment_image_url( $spot['bild'], 'large' ) : ''; ?>
							<button type="button" class="fahrzeug-hotspot group absolute grid h-11 w-11 -translate-x-1/2 -translate-y-1/2 place-items-center rounded-full focus:outline-none" style="left:<?php echo esc_attr( (float) $spot['x'] ); ?>%;top:<?php echo esc_attr( (float) $spot['y'] ); ?>%;" aria-haspopup="dialog" aria-label="<?php echo esc_attr( 'Gerätefach ansehen: ' . ( $spot['titel'] ?: ( $h + 1 ) ) ); ?>">
								<span class="fahrzeug-hotspot-puls absolute inset-1 rounded-full bg-white/70" aria-hidden="true"></span>
								<span class="relative grid h-8 w-8 place-items-center rounded-full border-2 border-white bg-signal text-signal-foreground shadow-lg transition-transform duration-200 group-hover:scale-110 group-focus-visible:scale-110 group-focus-visible:ring-4 group-focus-visible:ring-white/80"><?php echo elfzwo_icon( 'plus', 'h-4 w-4', 3 ); ?></span>
							</button>
							<template class="fahrzeug-hotspot-inhalt">
								<?php // Fachfotos sind Hochformat: auf dem Desktop links neben dem Text, mobil darüber. ?>
								<div class="<?php echo $bild_url ? 'md:grid md:grid-cols-[minmax(0,2fr)_minmax(0,3fr)]' : ''; ?>">
									<?php if ( $bild_url ) : ?>
										<img src="<?php echo esc_url( $bild_url ); ?>" alt="<?php echo esc_attr( $spot['titel'] ); ?>" class="block max-h-[55vh] w-full bg-muted object-contain md:h-full md:max-h-[calc(100dvh-7rem)] md:object-cover">
									<?php endif; ?>
									<div class="p-6 md:self-center md:p-8">
										<p class="text-xs font-semibold text-muted-foreground"><?php echo esc_html( get_the_title() ); ?></p>
										<?php if ( $spot['titel'] ) : ?><h3 class="fahrzeug-modal-titel mt-1 font-display text-3xl"><?php echo esc_html( $spot['titel'] ); ?></h3><?php endif; ?>
										<?php if ( $spot['text'] ) : ?><div class="mt-3 space-y-3 text-muted-foreground"><?php echo wpautop( esc_html( $spot['text'] ) ); ?></div><?php endif; ?>
									</div>
								</div>
							</template>
						<?php endforeach; ?>
					</div>
				</div>
				<?php if ( $hotspots ) : ?>
					<p class="mt-6 flex items-center gap-2 text-sm text-muted-foreground"><?php echo elfzwo_icon( 'plus', 'h-4 w-4 text-signal', 3 ); ?> Auf die Punkte klicken, um in die Gerätefächer zu schauen.</p>
				<?php endif; ?>
			</div>
			<div>
				<?php if ( $tag ) : ?><p class="text-xs font-semibold uppercase tracking-[0.18em] text-muted-foreground"><?php echo esc_html( $tag ); ?></p><?php endif; ?>
				<h2 class="mt-2 font-display text-4xl md:text-5xl"><?php the_title(); ?></h2>
				<?php if ( get_the_content() ) : ?><div class="mt-4 text-lg text-muted-foreground"><?php the_content(); ?></div><?php endif; ?>
				<?php if ( $specs ) : ?>
					<dl class="mt-6 grid gap-3 sm:grid-cols-2">
						<?php foreach ( $specs as $s ) : ?>
							<div class="flex min-w-0 items-center gap-3 rounded-2xl border border-border bg-card p-4">
								<span class="grid h-8 w-8 shrink-0 place-items-center text-signal"><?php echo elfzwo_icon( $s['icon'], 'h-5 w-5', 2.2 ); ?></span>
								<div class="min-w-0">
									<dt class="text-xs text-muted-foreground [overflow-wrap:anywhere]"><?php echo esc_html( $s['label'] ); ?></dt>
									<dd class="font-display text-lg leading-tight break-words"><?php echo esc_html( $s['value'] ); ?></dd>
								</div>
							</div>
						<?php endforeach; ?>
					</dl>
				<?php endif; ?>
			</div>
		</article>
	<?php endwhile; wp_reset_postdata(); ?>

	<dialog class="fahrzeug-modal m-auto w-[calc(100%-2rem)] max-w-4xl max-h-[calc(100dvh-2rem)] overflow-y-auto rounded-lg bg-card p-0 text-foreground shadow-2xl backdrop:bg-ink/75">
		<div class="fahrzeug-modal-body"></div>
		<div class="flex items-center justify-between gap-4 border-t border-border px-6 py-4 md:px-8">
			<button type="button" class="fahrzeug-modal-prev inline-flex whitespace-nowrap items-center gap-1 rounded-md px-2 py-1 text-sm font-semibold hover:text-signal disabled:invisible"><?php echo elfzwo_icon( 'chevron-left', 'h-4 w-4' ); ?> <span class="sm:hidden">Zurück</span><span class="hidden sm:inline">Vorheriges Fach</span></button>
			<span class="fahrzeug-modal-zaehler whitespace-nowrap text-xs text-muted-foreground"></span>
			<button type="button" class="fahrzeug-modal-next inline-flex whitespace-nowrap items-center gap-1 rounded-md px-2 py-1 text-sm font-semibold hover:text-signal disabled:invisible"><span class="sm:hidden">Weiter</span><span class="hidden sm:inline">Nächstes Fach</span> <?php echo elfzwo_icon( 'chevron-right', 'h-4 w-4' ); ?></button>
		</div>
		<button type="button" class="fahrzeug-modal-close absolute right-3 top-3 grid h-10 w-10 place-items-center rounded-full bg-ink/70 text-white hover:bg-ink" aria-label="Schließen"><?php echo elfzwo_icon( 'x', 'h-5 w-5' ); ?></button>
	</dialog>
</section>
