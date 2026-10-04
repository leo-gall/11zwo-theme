<?php
/**
 * Ansprechpartner: Überschrift, kurzer Text und Karten (inc/komponenten.php)
 * — bewusst ohne Aufruf-Button.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
$columns = max( 1, min( 4, (int) ( $attributes['columns'] ?? 2 ) ) );
$karten  = array_filter( array_map( 'elfzwo_render_ansprechpartner_karte', $attributes['people'] ?? array() ) );
?>
<section class="mx-auto max-w-7xl px-5 py-12 md:px-8">
	<?php if ( ! empty( $attributes['title'] ) ) : ?><h2 class="font-display text-3xl font-bold text-signal md:text-4xl"><?php echo esc_html( $attributes['title'] ); ?></h2><?php endif; ?>
	<?php if ( ! empty( $attributes['text'] ) ) : ?><p class="mt-3 max-w-2xl font-light text-foreground/80"><?php echo esc_html( $attributes['text'] ); ?></p><?php endif; ?>
	<div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-<?php echo esc_attr( $columns ); ?>">
		<?php echo implode( '', $karten ); // phpcs:ignore -- bereits escaped ?>
	</div>
</section>
