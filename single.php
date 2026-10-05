<?php
/**
 * Einzelansicht eines Aktuelles-Beitrags (natives WP-Post).
 */

get_header();

while ( have_posts() ) :
	the_post();
	$post_id       = get_the_ID();
	$kategorie_line = elfzwo_post_category_line( $post_id );
	?>

	<main>
		<?php
		echo elfzwo_render_jumbotron( // phpcs:ignore -- bereits escaped
			array(
				'titel'      => get_the_title(),
				'untertitel' => implode( ' · ', array_filter( array( $kategorie_line, get_the_date( 'j. F Y' ) ) ) ),
				'bild'       => has_post_thumbnail() ? get_the_post_thumbnail_url( null, 'full' ) : '',
			)
		);
		?>
		<section class="mx-auto max-w-3xl px-5 pt-6 pb-4 md:px-8">
			<a href="<?php echo esc_url( home_url( '/einsaetze/' ) ); ?>" class="inline-flex items-center gap-2 text-sm font-semibold text-smoke hover:text-foreground">
				<?php echo elfzwo_icon( 'arrow-left', 'h-4 w-4' ); ?> Zurück zu Einsätze &amp; Aktuelles
			</a>
		</section>

		<article class="mx-auto max-w-3xl px-5 pb-20 md:px-8">

			<?php if ( has_post_thumbnail() ) : ?>
				<div class="relative mt-8 overflow-hidden border border-border">
					<?php the_post_thumbnail( 'large', array( 'class' => 'h-[360px] w-full object-cover md:h-[460px]' ) ); ?>
				</div>
			<?php endif; ?>

			<div class="prose prose-neutral mt-10 max-w-none text-lg leading-relaxed">
				<?php the_content(); ?>
			</div>
		</article>
	</main>

<?php endwhile; ?>

<?php get_footer(); ?>
