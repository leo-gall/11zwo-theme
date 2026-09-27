<?php
/**
 * Einzelansicht eines Aktuelles-Beitrags (natives WP-Post).
 */

get_header();

while ( have_posts() ) :
	the_post();
	$post_id       = get_the_ID();
	$kategorie_line = elfzwo_post_category_line( $post_id );
	$einsatz_id    = elfzwo_meta( $post_id, 'einsatz_bezug', '' );
	$gallery_ids  = elfzwo_post_all_image_ids( $post_id );
	?>

	<main>
		<section class="mx-auto max-w-3xl px-5 pt-10 pb-4 md:px-8 md:pt-16">
			<a href="<?php echo esc_url( home_url( '/einsaetze/' ) ); ?>" class="inline-flex items-center gap-2 text-sm font-semibold text-muted-foreground hover:text-foreground">
				<?php echo elfzwo_icon( 'arrow-left', 'h-4 w-4' ); ?> Zurück zu Einsätze &amp; Aktuelles
			</a>
		</section>

		<article class="mx-auto max-w-3xl px-5 pb-20 md:px-8">
			<?php if ( $kategorie_line ) : ?><p class="mt-4 text-xs font-semibold uppercase tracking-[0.18em] text-signal"><?php echo esc_html( $kategorie_line ); ?></p><?php endif; ?>
			<h1 class="mt-3 font-display text-4xl leading-tight md:text-5xl"><?php the_title(); ?></h1>
			<p class="mt-4 text-xs uppercase tracking-widest text-muted-foreground"><?php echo esc_html( get_the_date() ); ?></p>

			<?php if ( count( $gallery_ids ) > 2 ) : ?>
				<div class="elfzwo-carousel relative mt-8 overflow-hidden rounded-[2rem] border border-border" tabindex="0">
					<div class="elfzwo-carousel-track flex touch-pan-y">
						<?php foreach ( $gallery_ids as $img_id ) : ?>
							<div class="elfzwo-carousel-slide w-full shrink-0">
								<img src="<?php echo esc_url( wp_get_attachment_image_url( $img_id, 'large' ) ); ?>" alt="<?php echo esc_attr( get_post_meta( $img_id, '_wp_attachment_image_alt', true ) ?: get_the_title() ); ?>" width="1200" height="800" class="h-[360px] w-full select-none object-cover md:h-[460px]" draggable="false">
							</div>
						<?php endforeach; ?>
					</div>
					<button type="button" class="elfzwo-carousel-prev absolute left-3 top-1/2 grid h-12 w-12 -translate-y-1/2 place-items-center rounded-full border border-black/5 bg-white text-ink shadow-xl transition-transform hover:scale-110" aria-label="Vorheriges Bild"><?php echo elfzwo_icon( 'arrow-left', 'h-5 w-5', 2.5 ); ?></button>
					<button type="button" class="elfzwo-carousel-next absolute right-3 top-1/2 grid h-12 w-12 -translate-y-1/2 place-items-center rounded-full border border-black/5 bg-white text-ink shadow-xl transition-transform hover:scale-110" aria-label="Nächstes Bild"><?php echo elfzwo_icon( 'arrow-right', 'h-5 w-5', 2.5 ); ?></button>
					<div class="absolute bottom-4 left-1/2 flex -translate-x-1/2 items-center gap-2 rounded-full bg-black/50 px-3 py-2 backdrop-blur-sm">
						<?php foreach ( $gallery_ids as $i => $img_id ) : ?>
							<button type="button" class="elfzwo-carousel-dot h-2 w-2 rounded-full bg-white/40 transition-colors" aria-label="Bild <?php echo esc_attr( $i + 1 ); ?>"></button>
						<?php endforeach; ?>
					</div>
				</div>
			<?php else : ?>
				<div class="relative mt-8 overflow-hidden rounded-[2rem] border border-border">
					<?php if ( $gallery_ids ) : ?>
						<img src="<?php echo esc_url( wp_get_attachment_image_url( $gallery_ids[0], 'large' ) ); ?>" alt="<?php echo esc_attr( get_post_meta( $gallery_ids[0], '_wp_attachment_image_alt', true ) ?: get_the_title() ); ?>" width="1200" height="800" class="h-[360px] w-full object-cover md:h-[460px]">
					<?php else : ?>
						<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/news-1.jpg' ); ?>" alt="<?php the_title_attribute(); ?>" width="1200" height="800" class="h-[360px] w-full object-cover md:h-[460px]">
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<div class="prose prose-neutral mt-10 max-w-none text-lg leading-relaxed">
				<?php the_content(); ?>
			</div>

			<?php if ( $einsatz_id ) : ?>
				<p class="mt-10 text-sm text-muted-foreground">Zugehöriger Einsatzbericht: <strong><?php echo esc_html( get_the_title( $einsatz_id ) ); ?></strong></p>
				<?php echo elfzwo_render_einsatzdaten_card( $einsatz_id, false ); // phpcs:ignore -- bereits escaped in elfzwo_render_einsatzdaten_card() ?>
				<a href="<?php echo esc_url( get_permalink( $einsatz_id ) ); ?>" class="mt-5 inline-flex items-center gap-2 text-sm font-semibold text-signal hover:underline">
					Ganzen Einsatzbericht ansehen <?php echo elfzwo_icon( 'arrow-right', 'h-4 w-4' ); ?>
				</a>
			<?php endif; ?>
		</article>
	</main>

<?php endwhile; ?>

<?php get_footer(); ?>
