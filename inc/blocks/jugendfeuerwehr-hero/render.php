<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$badge       = $attributes['badge'] ?? '';
$line1       = $attributes['titleLine1'] ?? '';
$highlight   = $attributes['titleHighlight'] ?? '';
$line2       = $attributes['titleLine2'] ?? '';
$description = $attributes['description'] ?? '';
$cta         = $attributes['ctaPrimary'] ?? '';
$cta_url     = $attributes['ctaUrl'] ?? '/mitmachen/';
$zitat       = $attributes['zitat'] ?? '';
$image_id    = (int) ( $attributes['imageId'] ?? 0 );
$image_url   = $image_id ? wp_get_attachment_image_url( $image_id, 'large' ) : ( $attributes['imageUrl'] ?? '' );
?>
<section class="relative mx-auto grid max-w-7xl gap-10 px-5 pt-14 pb-10 md:grid-cols-[1.1fr_1fr] md:px-8 md:pt-20">
	<div class="flex flex-col justify-center">
		<?php if ( $badge ) : ?><p class="font-hand text-2xl text-primary"><?php echo esc_html( $badge ); ?></p><?php endif; ?>
		<?php // Zeile 2 ohne Leerzeichen anhängen, wenn sie mit einem Satzzeichen beginnt ("Abenteuer, echte Skills."). ?>
		<h1 class="mt-1 font-display text-5xl leading-[1.02] md:text-6xl">
			<?php echo esc_html( $line1 ); ?>
			<?php if ( $highlight ) : ?> <span class="text-signal"><?php echo esc_html( $highlight ); ?></span><?php endif; ?><?php if ( $line2 ) { echo ( preg_match( '/^[.,!?:;]/u', $line2 ) ? '' : ' ' ) . esc_html( $line2 ); } ?>
		</h1>
		<?php if ( $description ) : ?><p class="mt-5 max-w-xl text-lg text-muted-foreground"><?php echo esc_html( $description ); ?></p><?php endif; ?>
		<?php if ( $cta ) : ?>
			<div class="mt-8">
				<a href="<?php echo esc_url( $cta_url ); ?>" class="elfzwo-btn elfzwo-btn-primary">
					<?php echo esc_html( $cta ); ?> <?php echo elfzwo_icon( 'arrow-right', 'h-4 w-4 elfzwo-btn-arrow' ); ?>
				</a>
			</div>
		<?php endif; ?>
	</div>
	<?php if ( $image_url ) : ?>
		<div class="relative">
			<div class="overflow-hidden rounded-[2rem] shadow-xl"><img src="<?php echo esc_url( $image_url ); ?>" alt="" class="aspect-[4/3] h-full w-full object-cover"></div>
			<?php if ( $zitat ) : ?>
				<div class="absolute -right-3 top-6 rotate-6 rounded-2xl bg-cream px-4 py-2 font-hand text-xl text-signal shadow-lg"><?php echo esc_html( $zitat ); ?></div>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</section>
