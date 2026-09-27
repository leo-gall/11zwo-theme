<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$kicker = $attributes['kicker'] ?? '';
$title  = $attributes['title'] ?? '';
$tiles  = $attributes['tiles'] ?? array();
?>
<section class="mx-auto max-w-7xl px-5 py-10 md:px-8">
	<div class="mb-8 max-w-2xl">
		<?php if ( $kicker ) : ?><p class="text-xs font-semibold uppercase tracking-[0.18em] text-muted-foreground"><?php echo esc_html( $kicker ); ?></p><?php endif; ?>
		<?php if ( $title ) : ?><h2 class="mt-2 font-display text-3xl md:text-4xl"><?php echo esc_html( $title ); ?></h2><?php endif; ?>
	</div>
	<div class="grid grid-cols-2 gap-4 md:grid-cols-3">
		<?php foreach ( $tiles as $tile ) :
			$tile_title = $tile['title'] ?? '';
			$caption    = $tile['caption'] ?? '';
			$featured   = ! empty( $tile['featured'] );
			$image_id   = (int) ( $tile['imageId'] ?? 0 );
			$image_url  = $image_id ? wp_get_attachment_image_url( $image_id, 'large' ) : ( $tile['imageUrl'] ?? '' );
			?>
			<div class="group relative overflow-hidden rounded-2xl bg-ink <?php echo $featured ? 'col-span-2 aspect-[21/9]' : 'aspect-[4/5]'; ?>">
				<?php if ( $image_url ) : ?>
					<img src="<?php echo esc_url( $image_url ); ?>" loading="lazy" alt="<?php echo esc_attr( $tile_title ); ?>" class="absolute inset-0 h-full w-full object-cover transition-transform duration-300 group-hover:scale-105">
				<?php endif; ?>
				<div class="absolute inset-0 bg-gradient-to-t from-ink/85 via-ink/10 to-transparent"></div>
				<div class="relative flex h-full flex-col justify-end p-5">
					<?php if ( $tile_title ) : ?><h3 class="font-display text-xl text-white <?php echo $featured ? 'md:text-2xl' : ''; ?>"><?php echo esc_html( $tile_title ); ?></h3><?php endif; ?>
					<?php if ( $caption ) : ?><p class="mt-1 text-sm text-white/80"><?php echo esc_html( $caption ); ?></p><?php endif; ?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</section>
