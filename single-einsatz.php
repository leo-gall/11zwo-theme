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
		<?php
		$elfzwo_datum = elfzwo_meta( $post_id, 'datum', '' );
		echo elfzwo_render_jumbotron( // phpcs:ignore -- bereits escaped
			array(
				'titel'      => get_the_title(),
				'untertitel' => implode( ' · ', array_filter( array( 'Einsatzbericht', $elfzwo_datum ? date_i18n( 'j. F Y', strtotime( $elfzwo_datum ) ) : '', elfzwo_einsatzort_name( $post_id ) ) ) ),
				'bild'       => has_post_thumbnail() ? get_the_post_thumbnail_url( null, 'full' ) : '',
			)
		);
		?>
		<section class="mx-auto max-w-3xl px-5 pt-6 pb-4 md:px-8">
			<a href="<?php echo esc_url( home_url( '/einsaetze/' ) ); ?>" class="inline-flex items-center gap-2 text-sm font-semibold text-muted-foreground hover:text-foreground">
				<?php echo elfzwo_icon( 'arrow-left', 'h-4 w-4' ); ?> Zurück zu Einsätze &amp; Aktuelles
			</a>
		</section>

		<article class="mx-auto max-w-3xl px-5 pb-20 md:px-8">

			<?php echo elfzwo_render_einsatzdaten_card( $post_id ); // phpcs:ignore -- bereits escaped in elfzwo_render_einsatzdaten_card() ?>
		</article>
	</main>

<?php endwhile; ?>

<?php get_footer(); ?>
