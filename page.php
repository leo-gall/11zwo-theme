<?php
/**
 * Generisches Seiten-Template. Der komplette Seitenaufbau kommt aus dem
 * Block-Editor (siehe inc/blocks/) — dieses Template liefert nur noch den
 * Rahmen (Header/Footer).
 */

get_header();
?>

<main>
	<?php while ( have_posts() ) : the_post(); ?>
		<?php
		if ( ! elfzwo_seite_hat_kopf( get_post() ) ) {
			echo elfzwo_seiten_jumbotron( get_post() ); // phpcs:ignore -- bereits escaped
		}
		?>
		<?php the_content(); ?>
	<?php endwhile; ?>
</main>

<?php get_footer(); ?>
