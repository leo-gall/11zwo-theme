<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$tag         = $attributes['tag'] ?? '';
$title       = $attributes['title'] ?? '';
$description = $attributes['description'] ?? '';
$badge       = $attributes['badge'] ?? '';
$badge_icon  = $attributes['badgeIcon'] ?? 'users';
$zitat       = $attributes['zitat'] ?? '';
$cta_text    = $attributes['ctaText'] ?? '';
$cta_url     = $attributes['ctaUrl'] ?? '#';
$image_id    = (int) ( $attributes['imageId'] ?? 0 );
$image_url   = $image_id ? wp_get_attachment_image_url( $image_id, 'large' ) : ( $attributes['imageUrl'] ?? '' );
$bullets     = $attributes['bulletPoints'] ?? array();
?>
<section class="relative mx-auto max-w-7xl px-5 py-16 md:px-8">
	<div class="relative overflow-hidden rounded-[2.5rem] border border-border bg-card">
		<div class="grid gap-10 p-8 md:grid-cols-[1fr_1.05fr] md:items-center md:p-14 lg:gap-16">
			<div>
				<?php if ( $tag ) : ?><p class="font-hand text-2xl text-primary"><?php echo esc_html( $tag ); ?></p><?php endif; ?>
				<h2 class="mt-1 font-display text-4xl leading-tight md:text-5xl"><?php echo esc_html( $title ); ?></h2>
				<?php if ( $description ) : ?><p class="mt-5 max-w-lg text-muted-foreground"><?php echo esc_html( $description ); ?></p><?php endif; ?>
				<?php if ( $bullets ) : ?>
					<div class="mt-6 elfzwo-feature-list">
						<ul>
							<?php foreach ( $bullets as $bullet ) : ?>
								<li><?php echo esc_html( $bullet ); ?></li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>
				<?php if ( $cta_text ) : ?>
					<div class="mt-9">
						<a href="<?php echo esc_url( $cta_url ); ?>" class="elfzwo-btn elfzwo-btn-secondary">
							<?php echo esc_html( $cta_text ); ?> <?php echo elfzwo_icon( 'arrow-right', 'h-4 w-4 elfzwo-btn-arrow' ); ?>
						</a>
					</div>
				<?php endif; ?>
			</div>
			<div class="relative">
				<?php if ( $image_url ) : ?>
					<div class="overflow-hidden rounded-[2rem] shadow-xl">
						<img src="<?php echo esc_url( $image_url ); ?>" loading="lazy" alt="" class="aspect-[4/3] h-full w-full object-cover">
					</div>
				<?php endif; ?>
				<?php if ( $badge ) : ?>
					<div class="absolute bottom-5 left-5 flex items-center gap-3 rounded-2xl bg-background/95 px-4 py-3 shadow-lg backdrop-blur">
						<span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-signal text-signal-foreground"><?php echo elfzwo_icon( $badge_icon, 'h-4 w-4', 2.2 ); ?></span>
						<span class="text-sm font-semibold text-white"><?php echo esc_html( $badge ); ?></span>
					</div>
				<?php endif; ?>
				<?php if ( $zitat ) : ?>
					<div class="absolute -bottom-3 -left-3 rotate-6 rounded-2xl bg-cream px-4 py-2 font-hand text-xl text-signal shadow-lg"><?php echo esc_html( $zitat ); ?></div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
