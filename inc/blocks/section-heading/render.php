<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$tag                = $attributes['tag'] ?? '';
$title              = $attributes['title'] ?? '';
$description        = $attributes['description'] ?? '';
$description_source = $attributes['descriptionSource'] ?? 'custom';
$layout             = $attributes['layout'] ?? 'split';
$accent             = $attributes['accent'] ?? 'text-primary';
$is_nina            = 'nina' === $description_source;
?>
<section class="mx-auto max-w-7xl px-5 pt-10 pb-2 md:px-8">
	<?php if ( 'split' === $layout ) : ?>
		<div class="flex flex-wrap items-start justify-between gap-6">
			<div class="max-w-2xl">
				<?php if ( $tag ) : ?><p class="font-hand text-2xl <?php echo esc_attr( $accent ); ?>"><?php echo esc_html( $tag ); ?></p><?php endif; ?>
				<h2 class="mt-1 font-display text-4xl md:text-5xl"><?php echo esc_html( $title ); ?></h2>
			</div>
			<?php if ( $is_nina ) : ?>
				<?php echo elfzwo_nina_render_compact(); // phpcs:ignore -- bereits escaped in elfzwo_nina_render_compact() ?>
			<?php elseif ( $description ) : ?>
				<p class="max-w-md text-muted-foreground"><?php echo esc_html( $description ); ?></p>
			<?php endif; ?>
		</div>
	<?php else : ?>
		<?php if ( $tag ) : ?><p class="font-hand text-2xl <?php echo esc_attr( $accent ); ?>"><?php echo esc_html( $tag ); ?></p><?php endif; ?>
		<h2 class="mt-1 font-display text-4xl md:text-5xl"><?php echo esc_html( $title ); ?></h2>
		<?php if ( $is_nina ) : ?>
			<div class="mt-4"><?php echo elfzwo_nina_render_compact(); // phpcs:ignore -- bereits escaped in elfzwo_nina_render_compact() ?></div>
		<?php elseif ( $description ) : ?>
			<p class="mt-4 max-w-2xl text-muted-foreground"><?php echo esc_html( $description ); ?></p>
		<?php endif; ?>
	<?php endif; ?>
</section>
