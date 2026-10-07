<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$title       = $attributes['title'] ?? '';
$description = $attributes['description'] ?? '';
$split       = 'split' === ( $attributes['layout'] ?? 'split' );
if ( elfzwo_kopf_uebernommen( 'elfzwo/section-heading' ) ) {
	// Überschrift steht schon im Seiten-Jumbotron, hier nur noch die Beschreibung.
	if ( $description ) {
		echo '<section class="mx-auto max-w-7xl px-5 pt-6 md:px-8"><p class="max-w-2xl text-smoke">' . esc_html( $description ) . '</p></section>';
	}
	return;
}
?>
<section class="mx-auto max-w-7xl px-5 pt-10 pb-2 md:px-8">
	<div class="<?php echo $split ? 'flex flex-wrap items-start justify-between gap-6' : ''; ?>">
		<h2 class="max-w-2xl font-display text-3xl font-black text-signal md:text-4xl"><?php echo esc_html( $title ); ?></h2>
		<?php if ( $description ) : ?>
			<p class="<?php echo $split ? 'max-w-md' : 'mt-4 max-w-2xl'; ?> text-smoke"><?php echo esc_html( $description ); ?></p>
		<?php endif; ?>
	</div>
</section>
