<?php
/**
 * Fallback-Template (Archive, Suche, einzelne Beiträge etc.).
 */

get_header();
?>

<main>
	<section class="mx-auto max-w-4xl px-5 py-16 md:px-8">
		<?php if ( have_posts() ) : ?>
			<?php while ( have_posts() ) : the_post(); ?>
				<article <?php post_class( 'mb-16 border-b border-border pb-16 last:border-0' ); ?>>
					<h1 class="font-display text-3xl md:text-4xl"><?php the_title(); ?></h1>
					<div class="prose prose-neutral mt-6 max-w-none text-foreground/90">
						<?php the_content(); ?>
					</div>
				</article>
			<?php endwhile; ?>
		<?php else : ?>
			<p class="text-muted-foreground"><?php esc_html_e( 'Keine Inhalte gefunden.', '11zwo' ); ?></p>
		<?php endif; ?>
	</section>
</main>

<?php get_footer(); ?>
