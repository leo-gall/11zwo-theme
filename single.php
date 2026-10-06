<?php
/**
 * Einzelansicht eines Vereinsbeitrags: Datum, Titel, Foto, Text. Sonst nichts.
 */

get_header();

while ( have_posts() ) :
	the_post();
	?>

	<main class="mx-auto max-w-3xl px-5 py-12 md:px-8 md:py-16">
		<a href="<?php echo esc_url( home_url( '/verein/#aktuelles' ) ); ?>" class="text-sm text-smoke underline underline-offset-4 hover:text-foreground">Alle Beiträge</a>

		<article class="mt-10">
			<p class="text-smoke"><?php echo esc_html( get_the_date( 'd.m.Y' ) ); ?></p>
			<h1 class="mt-2 border-b-2 border-foreground pb-4 text-4xl font-bold leading-tight md:text-5xl"><?php the_title(); ?></h1>

			<?php if ( has_post_thumbnail() ) : ?>
				<?php the_post_thumbnail( 'large', array( 'class' => 'mt-8 w-full' ) ); ?>
			<?php endif; ?>

			<div class="prose mt-8 max-w-none text-lg">
				<?php the_content(); ?>
			</div>
		</article>
	</main>

<?php endwhile; ?>

<?php get_footer(); ?>
