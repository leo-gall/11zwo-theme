<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$title1    = $attributes['title1'] ?? '';
$title2    = $attributes['title2'] ?? '';
$subtitle  = $attributes['subtitle'] ?? '';
$description = $attributes['description'] ?? '';
$cta1      = $attributes['ctaPrimaryText'] ?? '';
$cta2      = $attributes['ctaSecondaryText'] ?? '';
$cta2_url  = $attributes['ctaSecondaryUrl'] ?? '';
$badge     = $attributes['emergencyBadge'] ?? '';

$fallback_images = array(
	get_template_directory_uri() . '/assets/images/hero-team.jpg',
	get_template_directory_uri() . '/assets/images/youth.jpg',
	get_template_directory_uri() . '/assets/images/truck-1.jpg',
);
$images = array();
for ( $i = 1; $i <= 3; $i++ ) {
	$id  = (int) ( $attributes[ "image{$i}Id" ] ?? 0 );
	$url = $id ? wp_get_attachment_image_url( $id, 'large' ) : $attributes[ "image{$i}Url" ] ?? '';
	$images[] = $url ?: $fallback_images[ $i - 1 ];
}
?>
<section class="relative">
	<div class="relative mx-auto grid max-w-7xl gap-10 px-5 pt-10 pb-20 md:grid-cols-[1.05fr_1fr] md:px-8 md:pt-16 lg:gap-16 lg:pt-24">
		<div class="flex flex-col justify-center">
			<h1 class="mt-6 font-display text-5xl leading-[1.02] tracking-tight md:text-6xl lg:text-7xl">
				<?php echo esc_html( $title1 ); ?><br>
				<?php echo esc_html( $title2 ); ?><br>
				<span class="text-signal font-hand text-[1.15em]"><?php echo esc_html( $subtitle ); ?></span>
			</h1>
			<?php if ( $description ) : ?><p class="mt-6 max-w-xl text-lg leading-relaxed text-muted-foreground"><?php echo esc_html( $description ); ?></p><?php endif; ?>
			<div class="mt-8 flex flex-wrap items-center gap-3">
				<?php if ( $cta1 ) : ?>
					<a href="<?php echo esc_url( home_url( '/mitmachen/' ) ); ?>" class="group flex w-full items-center justify-center gap-3 rounded-full bg-signal px-7 py-4 text-base font-semibold text-signal-foreground transition-colors hover:bg-signal/90 sm:inline-flex sm:w-auto">
						<?php echo esc_html( $cta1 ); ?> <?php echo elfzwo_icon( 'arrow-right', 'h-4 w-4 transition group-hover:translate-x-1' ); ?>
					</a>
				<?php endif; ?>
				<?php if ( $cta2 ) : ?>
					<a href="<?php echo esc_url( $cta2_url ?: '#' ); ?>" class="flex w-full items-center justify-center gap-3 rounded-full border border-ink px-7 py-3.5 text-base font-semibold text-ink transition hover:bg-ink hover:text-background sm:inline-flex sm:w-auto">
						<?php echo esc_html( $cta2 ); ?>
					</a>
				<?php endif; ?>
			</div>
		</div>

		<div class="relative flex items-center justify-center py-6">
			<div class="relative h-[340px] w-full max-w-md sm:h-[400px]">
				<img src="<?php echo esc_url( $images[2] ); ?>" alt="" class="absolute left-0 top-0 z-10 h-48 w-56 rotate-[-10deg] rounded-[2rem] border-2 border-cream object-cover shadow-lg sm:h-56 sm:w-64">
				<img src="<?php echo esc_url( $images[1] ); ?>" alt="" class="absolute right-0 top-6 z-20 h-48 w-56 rotate-[9deg] rounded-[2rem] border-2 border-cream object-cover shadow-xl sm:h-56 sm:w-64">
				<img src="<?php echo esc_url( $images[0] ); ?>" alt="" class="absolute bottom-0 left-1/2 z-30 h-48 w-56 -translate-x-1/2 rotate-[-3deg] rounded-[2rem] border-2 border-cream object-cover shadow-2xl sm:h-56 sm:w-64">
				<?php if ( $badge ) : ?>
					<div class="absolute -right-2 -top-2 z-40 rotate-6 rounded-2xl bg-cream px-4 py-2 font-hand text-xl text-signal shadow-lg"><?php echo esc_html( $badge ); ?></div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
