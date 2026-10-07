<?php
/**
 * Ansprechpartner: Überschrift, kurzer Text und Karten (inc/komponenten.php)
 * — bewusst ohne Aufruf-Button.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
$karten = array_filter( array_map( 'elfzwo_render_ansprechpartner_karte', $attributes['people'] ?? array() ) );
?>
<section class="mx-auto max-w-7xl px-5 py-12 md:px-8">
	<?php if ( ! empty( $attributes['title'] ) ) : ?><h2 class="font-display text-3xl font-black text-signal md:text-4xl"><?php echo esc_html( $attributes['title'] ); ?></h2><?php endif; ?>
	<div class="mt-8 grid gap-5 sm:grid-cols-2">
		<?php echo implode( '', $karten ); // phpcs:ignore -- bereits escaped ?>
	</div>
</section>
