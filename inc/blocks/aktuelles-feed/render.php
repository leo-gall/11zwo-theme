<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

$feed_page  = isset( $_GET['feed_seite'] ) ? max( 1, (int) $_GET['feed_seite'] ) : 1;
$feed_query = elfzwo_aktuelles_feed_query( $feed_page );
?>
<section class="mx-auto max-w-7xl px-5 py-8 md:px-8">
	<div class="elfzwo-aktuelles-feed-grid grid gap-10 md:grid-cols-2">
		<?php echo elfzwo_render_aktuelles_feed_cards( $feed_query ); // phpcs:ignore -- bereits escaped ?>
	</div>

	<?php if ( $feed_query->max_num_pages > $feed_page ) : ?>
		<div class="elfzwo-aktuelles-feed-more mt-12 flex justify-center">
			<button type="button" class="inline-flex items-center gap-2 rounded-full border border-ink px-6 py-3 text-sm font-semibold transition-colors hover:bg-secondary" data-next-page="<?php echo esc_attr( $feed_page + 1 ); ?>">Mehr anzeigen</button>
		</div>
	<?php endif; ?>
</section>
