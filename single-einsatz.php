<?php
/**
 * Einzelansicht eines Einsatzes.
 */

get_header();

while ( have_posts() ) :
	the_post();
	$post_id = get_the_ID();
	?>

	<main>
		<section class="mx-auto max-w-3xl px-5 pt-10 pb-4 md:px-8 md:pt-16">
			<a href="<?php echo esc_url( home_url( '/einsaetze/' ) ); ?>" class="inline-flex items-center gap-2 text-sm font-semibold text-muted-foreground hover:text-foreground">
				<?php echo elfzwo_icon( 'arrow-left', 'h-4 w-4' ); ?> Zurück zu Einsätze &amp; Aktuelles
			</a>
		</section>

		<article class="mx-auto max-w-3xl px-5 pb-20 md:px-8">
			<h1 class="mt-3 font-display text-4xl leading-tight md:text-5xl"><?php the_title(); ?></h1>

			<?php echo elfzwo_render_einsatzdaten_card( $post_id ); // phpcs:ignore -- bereits escaped in elfzwo_render_einsatzdaten_card() ?>
		</article>
	</main>

<?php endwhile; ?>

<?php get_footer(); ?>
