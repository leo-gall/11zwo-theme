<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$title   = $attributes['title'] ?? '';
$columns = max( 1, min( 4, (int) ( $attributes['columns'] ?? 2 ) ) );
$karten  = array_filter( array_map( 'elfzwo_render_ansprechpartner_karte', $attributes['people'] ?? array() ) );
?>
<div class="mx-auto max-w-7xl px-5 pb-8 md:px-8">
	<?php if ( $title ) : ?><h3 class="mb-5 font-display text-2xl"><?php echo esc_html( $title ); ?></h3><?php endif; ?>
	<div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-<?php echo esc_attr( $columns ); ?>">
		<?php echo implode( '', $karten ); // phpcs:ignore -- bereits escaped ?>
	</div>
</div>
