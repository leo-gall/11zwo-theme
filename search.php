<?php
/**
 * Suchergebnisse: Kopfbereich, Suchfeld mit Vorschlägen und die Treffer als
 * schlichte Liste mit Typ und kurzem Anriss.
 */

get_header();
?>

<main>
	<?php
	echo elfzwo_render_jumbotron( // phpcs:ignore -- bereits escaped
		array(
			'titel'      => 'Suche',
			'untertitel' => get_search_query() ? '„' . get_search_query() . '“' : '',
		)
	);
	?>
	<section class="mx-auto max-w-3xl px-5 py-16 md:px-8">
		<?php echo elfzwo_suchfeld( get_search_query() ); // phpcs:ignore -- bereits escaped ?>

		<?php if ( have_posts() ) : ?>
			<p class="mt-10 text-smoke"><?php echo esc_html( $wp_query->found_posts . ' Treffer' ); ?></p>
			<ul class="mt-2 border-t border-border">
				<?php while ( have_posts() ) : the_post(); ?>
					<li class="border-b border-border">
						<a href="<?php the_permalink(); ?>" class="group block py-5">
							<span class="flex items-baseline justify-between gap-4">
								<span class="text-lg font-bold group-hover:text-signal"><?php the_title(); ?></span>
								<span class="shrink-0 text-sm text-smoke"><?php echo esc_html( elfzwo_typ_name( get_post_type() ) ); ?></span>
							</span>
							<?php $elfzwo_anriss = elfzwo_excerpt( get_the_ID(), 25 ); ?>
							<?php if ( $elfzwo_anriss ) : ?><span class="mt-1 block text-smoke"><?php echo esc_html( $elfzwo_anriss ); ?></span><?php endif; ?>
						</a>
					</li>
				<?php endwhile; ?>
			</ul>
			<div class="mt-8 flex justify-between text-sm font-semibold text-signal">
				<span><?php previous_posts_link( 'Vorherige Treffer' ); ?></span>
				<span><?php next_posts_link( 'Weitere Treffer' ); ?></span>
			</div>
		<?php else : ?>
			<p class="mt-10 text-lg text-foreground/80">Keine Treffer. Versuch es mit einem anderen Begriff.</p>
		<?php endif; ?>
	</section>
</main>

<?php get_footer(); ?>
