<?php
/**
 * Neueste Beiträge im Stil eines Nachrichtenmagazins (Vorbild bundeswehr.de):
 * der neueste als großer Aufmacher mit Textkasten über dem Bild, darunter
 * weitere als schlichte Teaser ohne Rahmen.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
$anzahl      = max( 2, min( 8, (int) ( $attributes['anzahl'] ?? 4 ) ) );
$button_text = $attributes['buttonText'] ?? '';
$button_url  = $attributes['buttonUrl'] ?? '';
$beitraege   = get_posts( array( 'post_type' => 'post', 'numberposts' => $anzahl, 'post_status' => 'publish' ) );
$aufmacher   = array_shift( $beitraege );
?>
<section class="mx-auto max-w-7xl px-5 py-8 md:px-8">
	<?php if ( ! $aufmacher ) : ?>
		<p class="text-smoke">Noch keine Neuigkeiten.</p>
	<?php else :
		$bild      = elfzwo_post_cover_image_url( $aufmacher->ID, 'large' );
		$kategorie = elfzwo_post_category_line( $aufmacher->ID );
		?>
		<article class="relative grid items-center md:grid-cols-[1.5fr_1fr]">
			<?php if ( $bild ) : ?><img src="<?php echo esc_url( $bild ); ?>" alt="" class="aspect-[16/10] w-full object-cover"><?php endif; ?>
			<div class="relative z-10 -mt-12 mx-4 border border-border bg-card p-7 md:mx-0 md:-ml-20 md:mt-0 md:p-9 <?php echo $bild ? '' : 'md:col-span-2 md:ml-0'; ?>">
				<?php if ( $kategorie ) : ?><p class="text-sm font-semibold text-signal"><?php echo esc_html( $kategorie ); ?></p><?php endif; ?>
				<h3 class="mt-2 font-display text-2xl leading-tight md:text-3xl"><?php echo esc_html( get_the_title( $aufmacher ) ); ?></h3>
				<p class="mt-3 text-smoke"><?php echo esc_html( elfzwo_excerpt( $aufmacher->ID, 32 ) ); ?></p>
				<p class="mt-4 text-sm text-smoke"><?php echo esc_html( get_the_date( 'd.m.Y', $aufmacher ) ); ?></p>
				<a href="<?php echo esc_url( get_permalink( $aufmacher ) ); ?>" class="elfzwo-btn elfzwo-btn-secondary elfzwo-btn-sm mt-5">Zum Artikel</a>
			</div>
		</article>

		<?php if ( $beitraege ) : ?>
			<div class="mt-14 grid gap-x-8 gap-y-12 sm:grid-cols-2 lg:grid-cols-3">
				<?php foreach ( $beitraege as $beitrag ) :
					$bild      = elfzwo_post_cover_image_url( $beitrag->ID, 'medium_large' );
					$kategorie = elfzwo_post_category_line( $beitrag->ID );
					?>
					<a href="<?php echo esc_url( get_permalink( $beitrag ) ); ?>" class="group block">
						<?php if ( $bild ) : ?><img src="<?php echo esc_url( $bild ); ?>" alt="" loading="lazy" class="aspect-[16/10] w-full object-cover"><?php endif; ?>
						<?php if ( $kategorie ) : ?><p class="mt-4 text-sm font-semibold text-signal"><?php echo esc_html( $kategorie ); ?></p><?php endif; ?>
						<h3 class="mt-1 font-display text-xl leading-snug group-hover:underline"><?php echo esc_html( get_the_title( $beitrag ) ); ?></h3>
						<p class="mt-2 text-smoke"><?php echo esc_html( elfzwo_excerpt( $beitrag->ID, 22 ) ); ?></p>
						<p class="mt-3 text-sm text-smoke"><?php echo esc_html( get_the_date( 'd.m.Y', $beitrag ) ); ?></p>
					</a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php if ( $button_text && $button_url ) : ?>
			<div class="mt-12 border-t border-border pt-8">
				<a href="<?php echo esc_url( $button_url ); ?>" class="elfzwo-btn elfzwo-btn-primary"><?php echo esc_html( $button_text ); ?></a>
			</div>
		<?php endif; ?>
	<?php endif; ?>
</section>
