<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$title1      = $attributes['title1'] ?? '';
$title2      = $attributes['title2'] ?? '';
$description = $attributes['description'] ?? '';
$cta1        = $attributes['ctaPrimaryText'] ?? '';
$cta2        = $attributes['ctaSecondaryText'] ?? '';
$cta2_url    = $attributes['ctaSecondaryUrl'] ?? '';

$bild = function ( $n, $groesse, $fallback ) use ( $attributes ) {
	$id  = (int) ( $attributes[ "image{$n}Id" ] ?? 0 );
	$url = $id ? wp_get_attachment_image_url( $id, $groesse ) : ( $attributes[ "image{$n}Url" ] ?? '' );
	return $url ?: get_template_directory_uri() . '/assets/images/' . $fallback;
};
$mannschaft  = $bild( 1, '1536x1536', 'hero-team.jpg' );
$hintergrund = $bild( 2, 'full', 'hero-hintergrund.jpg' );
?>
<?php // Fahrzeugfoto als Hintergrund unter rotem Schleier, Mannschaftsfoto rechts. ?>
<section class="relative isolate overflow-hidden bg-signal text-white">
	<img src="<?php echo esc_url( $hintergrund ); ?>" alt="" class="absolute inset-0 -z-20 h-full w-full object-cover">
	<div class="absolute inset-0 -z-10 bg-gradient-to-r from-wood/95 via-signal/90 to-signal/75" aria-hidden="true"></div>
	<div class="mx-auto grid max-w-7xl items-center gap-10 px-5 py-14 md:grid-cols-[1fr_1.5fr] md:px-8 md:py-20">
		<div>
			<h1 class="font-display text-4xl font-black leading-tight md:text-5xl lg:text-6xl">
				<?php echo esc_html( $title1 ); ?><?php if ( $title2 ) : ?><br><?php echo esc_html( $title2 ); ?><?php endif; ?>
			</h1>
			<?php if ( $description ) : ?><p class="mt-5 max-w-xl text-lg leading-relaxed text-white/90"><?php echo esc_html( $description ); ?></p><?php endif; ?>
			<div class="mt-8 flex flex-wrap items-center gap-3">
				<?php if ( $cta1 ) : ?>
					<a href="<?php echo esc_url( elfzwo_mitmachen_url() ); ?>" class="elfzwo-btn elfzwo-btn-light w-full sm:w-auto">
						<?php echo esc_html( $cta1 ); ?>
					</a>
				<?php endif; ?>
				<?php if ( $cta2 ) : ?>
					<a href="<?php echo esc_url( $cta2_url ?: '#' ); ?>" class="elfzwo-btn elfzwo-btn-outline-light w-full sm:w-auto">
						<?php echo esc_html( $cta2 ); ?>
					</a>
				<?php endif; ?>
			</div>
		</div>
		<img src="<?php echo esc_url( $mannschaft ); ?>" alt="Mannschaft der Freiwilligen Feuerwehr Greifenberg" class="elfzwo-hero-blob w-full max-w-none object-cover object-[center_85%] xl:w-[calc(100%+5rem)]">
	</div>
</section>
