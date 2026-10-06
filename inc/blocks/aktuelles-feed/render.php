<?php
/**
 * "Aktuelles" (Verein-Seite): die Beiträge als Karten; "Ältere Beiträge laden"
 * hängt weitere an (assets/js/aktuelles-feed.js).
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

$feed_query = elfzwo_aktuelles_feed_query( 1 );
?>
<section id="aktuelles" class="mx-auto max-w-7xl scroll-mt-28 px-5 py-12 md:px-8">
	<h2 class="font-display text-3xl font-black text-signal md:text-4xl">Aktuelles</h2>
	<?php if ( ! $feed_query->have_posts() ) : ?>
		<p class="mt-6 text-smoke">Noch keine Beiträge.</p>
	<?php else : ?>
		<div class="elfzwo-aktuelles-feed-grid mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
			<?php echo elfzwo_render_aktuelles_feed_cards( $feed_query ); // phpcs:ignore -- bereits escaped ?>
		</div>
		<?php if ( $feed_query->max_num_pages > 1 ) : ?>
			<div class="elfzwo-aktuelles-feed-more mt-10 text-center">
				<button type="button" class="elfzwo-btn elfzwo-btn-secondary" data-next-page="2">Ältere Beiträge laden</button>
			</div>
		<?php endif; ?>
	<?php endif; ?>
</section>
