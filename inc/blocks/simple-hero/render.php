<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$kicker      = $attributes['kicker'] ?? '';
$title       = $attributes['title'] ?? '';
$description = $attributes['description'] ?? '';
$image_id    = (int) ( $attributes['imageId'] ?? 0 );
$cta1_text   = $attributes['ctaPrimaryText'] ?? '';
$cta1_url    = $attributes['ctaPrimaryUrl'] ?? '';
$cta2_text   = $attributes['ctaSecondaryText'] ?? '';
$cta2_url    = $attributes['ctaSecondaryUrl'] ?? '';
$image_url   = $image_id ? wp_get_attachment_image_url( $image_id, 'large' ) : '';

$width_classes = array(
	'3xl' => 'max-w-3xl',
	'5xl' => 'max-w-5xl',
	'7xl' => 'max-w-7xl',
);
$width_class = $width_classes[ $attributes['width'] ?? '3xl' ] ?? 'max-w-3xl';
?>
<section class="mx-auto <?php echo esc_attr( $width_class ); ?> px-5 pt-14 pb-10 md:px-8 md:pt-20">
	<?php if ( $kicker ) : ?><p class="font-hand text-2xl text-primary"><?php echo esc_html( $kicker ); ?></p><?php endif; ?>
	<h1 class="mt-1 font-display text-5xl leading-[1.02] md:text-6xl"><?php echo esc_html( $title ); ?></h1>
	<?php if ( $description ) : ?><p class="mt-5 max-w-2xl text-lg text-muted-foreground"><?php echo esc_html( $description ); ?></p><?php endif; ?>
	<?php if ( $cta1_text || $cta2_text ) : ?>
		<div class="mt-8 flex flex-wrap items-center gap-3">
			<?php if ( $cta1_text ) : ?>
				<a href="<?php echo esc_url( $cta1_url ?: '#' ); ?>" class="elfzwo-btn elfzwo-btn-primary">
					<?php echo esc_html( $cta1_text ); ?> <?php echo elfzwo_icon( 'arrow-right', 'h-4 w-4 elfzwo-btn-arrow' ); ?>
				</a>
			<?php endif; ?>
			<?php if ( $cta2_text ) : ?>
				<a href="<?php echo esc_url( $cta2_url ?: '#' ); ?>" class="elfzwo-btn elfzwo-btn-secondary">
					<?php echo esc_html( $cta2_text ); ?>
				</a>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</section>
<?php if ( $image_url ) : ?>
	<section class="mx-auto <?php echo esc_attr( $width_class ); ?> px-5 pb-10 md:px-8">
		<img src="<?php echo esc_url( $image_url ); ?>" alt="" class="h-[360px] w-full rounded-[2rem] object-cover md:h-[460px]">
	</section>
<?php endif; ?>
