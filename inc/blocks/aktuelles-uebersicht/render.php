<?php
/**
 * Startseite: die vier neuesten Beiträge als Karten, Link zu allen
 * Neuigkeiten auf der Vereinsseite.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
$beitraege = get_posts( array( 'post_type' => 'post', 'numberposts' => 4, 'post_status' => 'publish' ) );
?>
<section class="mx-auto max-w-7xl px-5 py-12 md:px-8 md:py-16">
	<div class="flex items-end justify-between gap-4">
		<h2 class="font-display text-3xl font-black text-signal md:text-4xl">Neuigkeiten aus der Feuerwehr</h2>
		<a href="<?php echo esc_url( home_url( '/verein/#aktuelles' ) ); ?>" class="shrink-0 text-sm font-semibold text-signal hover:underline">Alle Neuigkeiten</a>
	</div>
	<?php if ( ! $beitraege ) : ?>
		<p class="mt-6 text-smoke">Noch keine Neuigkeiten.</p>
	<?php else : ?>
		<div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
			<?php foreach ( $beitraege as $beitrag ) {
				echo elfzwo_beitrag_karte( $beitrag ); // phpcs:ignore -- bereits escaped
			} ?>
		</div>
	<?php endif; ?>
</section>
