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

			<?php if ( has_post_thumbnail() ) : ?>
				<div class="relative mt-8 overflow-hidden rounded-[2rem] border border-border">
					<?php the_post_thumbnail( 'large', array( 'class' => 'h-[360px] w-full object-cover md:h-[460px]' ) ); ?>
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
