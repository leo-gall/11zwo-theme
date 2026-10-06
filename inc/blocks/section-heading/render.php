<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$tag                = $attributes['tag'] ?? '';
$title              = $attributes['title'] ?? '';
$description        = $attributes['description'] ?? '';
$description_source = $attributes['descriptionSource'] ?? 'custom';
$layout             = $attributes['layout'] ?? 'split';
$accent             = $attributes['accent'] ?? 'text-signal';
$is_nina            = 'nina' === $description_source;
// Ohne PLZ im Block gilt die PLZ aus den Theme-Einstellungen.
$nina_plz           = ( $attributes['ninaPlz'] ?? '' ) ?: get_option( 'elfzwo_nina_plz', '' );
if ( elfzwo_kopf_uebernommen( 'elfzwo/section-heading' ) ) {
	// Überschrift steht schon im Seiten-Jumbotron — nur ggf. die NINA-Warnungen zeigen.
	if ( $is_nina ) {
		echo '<section class="mx-auto max-w-7xl px-5 pt-6 md:px-8">' . elfzwo_nina_render_compact( $nina_plz ) . '</section>'; // phpcs:ignore -- bereits escaped
	} elseif ( $description ) {
		echo '<section class="mx-auto max-w-7xl px-5 pt-6 md:px-8"><p class="max-w-2xl text-smoke">' . esc_html( $description ) . '</p></section>';
	}
	return;
}
?>
<section class="mx-auto max-w-7xl px-5 pt-10 pb-2 md:px-8">
	<?php if ( 'split' === $layout ) : ?>
		<div class="flex flex-wrap items-start justify-between gap-6">
			<div class="max-w-2xl">
				<?php if ( $tag ) : ?><p class="elfzwo-kicker"><?php echo esc_html( $tag ); ?></p><?php endif; ?>
				<h2 class="mt-1 font-display text-3xl font-black text-signal md:text-4xl"><?php echo esc_html( $title ); ?></h2>
			</div>
			<?php if ( $is_nina ) : ?>
				<?php echo elfzwo_nina_render_compact( $nina_plz ); // phpcs:ignore -- bereits escaped in elfzwo_nina_render_compact() ?>
			<?php elseif ( $description ) : ?>
				<p class="max-w-md text-smoke"><?php echo esc_html( $description ); ?></p>
			<?php endif; ?>
		</div>
	<?php else : ?>
		<?php if ( $tag ) : ?><p class="elfzwo-kicker"><?php echo esc_html( $tag ); ?></p><?php endif; ?>
		<h2 class="mt-1 font-display text-3xl font-black text-signal md:text-4xl"><?php echo esc_html( $title ); ?></h2>
		<?php if ( $is_nina ) : ?>
			<div class="mt-4"><?php echo elfzwo_nina_render_compact( $nina_plz ); // phpcs:ignore -- bereits escaped in elfzwo_nina_render_compact() ?></div>
		<?php elseif ( $description ) : ?>
			<p class="mt-4 max-w-2xl text-smoke"><?php echo esc_html( $description ); ?></p>
		<?php endif; ?>
	<?php endif; ?>
</section>
