<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$name      = $attributes['name'] ?? '';
$badge     = $attributes['badge'] ?? '';
$text      = $attributes['text'] ?? '';
$zitat     = $attributes['zitat'] ?? '';
$cta_text  = $attributes['ctaText'] ?? '';
$cta_url   = $attributes['ctaUrl'] ?? '#';
$image_id  = (int) ( $attributes['imageId'] ?? 0 );
$image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'large' ) : ( $attributes['imageUrl'] ?? '' );
?>
<figure class="relative isolate flex h-full min-h-[26rem] flex-col justify-end overflow-hidden rounded-3xl bg-foreground text-white">
	<?php if ( $image_url ) : ?>
		<img src="<?php echo esc_url( $image_url ); ?>" loading="lazy" alt="<?php echo esc_attr( $name ); ?>" class="absolute inset-0 -z-10 h-full w-full object-cover object-[center_15%]">
	<?php endif; ?>
	<div class="absolute inset-x-0 bottom-0 -z-10 h-1/3 bg-gradient-to-t from-black/80 to-transparent"></div>
	<figcaption class="flex items-end justify-between gap-4 p-6 md:p-7">
		<div class="min-w-0">
			<?php if ( $zitat ) : ?><p class="mb-2 font-hand text-xl leading-snug">„<?php echo esc_html( $zitat ); ?>“</p><?php endif; ?>
			<?php if ( $name ) : ?><p class="font-display text-xl leading-tight"><?php echo esc_html( $name ); ?></p><?php endif; ?>
			<?php if ( $badge ) : ?><p class="mt-1 text-xs font-semibold uppercase tracking-wider text-white/75"><?php echo esc_html( $badge ); ?></p><?php endif; ?>
			<?php if ( $text ) : ?><p class="mt-2 max-w-sm text-sm text-white/80"><?php echo esc_html( $text ); ?></p><?php endif; ?>
		</div>
		<?php if ( $cta_text ) : ?>
			<a href="<?php echo esc_url( $cta_url ); ?>" aria-label="<?php echo esc_attr( $cta_text ); ?>" title="<?php echo esc_attr( $cta_text ); ?>" class="group grid h-11 w-11 shrink-0 place-items-center rounded-full bg-signal text-signal-foreground transition-colors hover:bg-signal/90">
				<?php echo elfzwo_icon( 'arrow-right', 'h-4 w-4 transition group-hover:translate-x-0.5' ); ?>
			</a>
		<?php endif; ?>
	</figcaption>
</figure>
