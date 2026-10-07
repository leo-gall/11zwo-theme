<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$name      = $attributes['name'] ?? '';
$badge     = $attributes['badge'] ?? '';
$image_id  = (int) ( $attributes['imageId'] ?? 0 );
$image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'large' ) : ( $attributes['imageUrl'] ?? '' );
?>
<figure class="relative isolate flex h-full min-h-[26rem] flex-col justify-end overflow-hidden bg-foreground text-white">
	<?php if ( $image_url ) : ?>
		<img src="<?php echo esc_url( $image_url ); ?>" loading="lazy" alt="<?php echo esc_attr( $name ); ?>" class="absolute inset-0 -z-10 h-full w-full object-cover object-[center_15%]">
	<?php endif; ?>
	<div class="absolute inset-x-0 bottom-0 -z-10 h-1/3 bg-gradient-to-t from-black/80 to-transparent"></div>
	<figcaption class="p-6 md:p-7">
		<?php if ( $name ) : ?><p class="font-display text-xl leading-tight"><?php echo esc_html( $name ); ?></p><?php endif; ?>
		<?php if ( $badge ) : ?><p class="mt-1 text-xs font-semibold text-white/75"><?php echo esc_html( $badge ); ?></p><?php endif; ?>
	</figcaption>
</figure>
