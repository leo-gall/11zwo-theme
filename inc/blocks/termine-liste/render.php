<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! function_exists( 'elfzwo_termine_sort' ) ) {
	function elfzwo_termine_sort( $a, $b ) {
		return strcmp( elfzwo_meta( $a->ID, 'datum', '' ), elfzwo_meta( $b->ID, 'datum', '' ) );
	}
}

$kategorien = get_terms( array( 'taxonomy' => 'termin_kategorie', 'hide_empty' => true ) );
?>
<section class="mx-auto max-w-7xl px-5 py-8 md:px-8">
	<?php if ( is_wp_error( $kategorien ) || ! $kategorien ) : ?>
		<div class="rounded-2xl border border-border bg-card p-8 text-center"><p class="text-smoke">Keine anstehenden Termine vorhanden.</p></div>
	<?php else : ?>
		<div class="space-y-12">
			<?php foreach ( $kategorien as $kategorie ) :
				$termine = get_posts(
					array(
						'post_type'      => 'termin',
						'posts_per_page' => -1,
						'tax_query'      => array( array( 'taxonomy' => 'termin_kategorie', 'field' => 'term_id', 'terms' => $kategorie->term_id ) ),
					)
				);
				if ( ! $termine ) {
					continue;
				}
				usort( $termine, 'elfzwo_termine_sort' );
				?>
				<div>
					<div class="mb-6 flex items-center gap-3 border-b border-border pb-4">
						<div class="h-1 w-12 rounded-full bg-ember"></div>
						<h2 class="font-display text-2xl"><?php echo esc_html( $kategorie->name ); ?></h2>
					</div>
					<div class="grid gap-6">
						<?php foreach ( $termine as $termin ) :
							$tid   = $termin->ID;
							$datum = elfzwo_meta( $tid, 'datum', '' );
							$zeit  = elfzwo_meta( $tid, 'zeit', '' );
							$ort   = elfzwo_meta( $tid, 'ort', '' );
							$kurz  = elfzwo_meta( $tid, 'kurzbeschreibung', '' );
							$ts    = $datum ? strtotime( $datum ) : false;
							$image = has_post_thumbnail( $tid ) ? get_the_post_thumbnail_url( $tid, 'large' ) : '';
							?>
							<article class="overflow-hidden rounded-2xl border border-border bg-card transition-all hover:shadow-lg">
								<div class="grid gap-4 p-6 md:grid-cols-[1fr_300px] md:gap-6">
									<div>
										<h3 class="font-display text-2xl leading-tight"><?php echo esc_html( $termin->post_title ); ?></h3>
										<?php if ( $kurz ) : ?><p class="mt-2 text-smoke"><?php echo esc_html( $kurz ); ?></p><?php endif; ?>
										<div class="mt-6 flex flex-wrap gap-4 text-sm">
											<?php if ( $ts ) : ?><div class="flex items-center gap-2 text-foreground"><?php echo elfzwo_icon( 'calendar-days', 'h-4 w-4 text-signal' ); ?><span class="font-medium"><?php echo esc_html( date_i18n( 'l, d.m.Y', $ts ) ); ?></span></div><?php endif; ?>
											<?php if ( $zeit ) : ?><div class="flex items-center gap-2 text-foreground"><?php echo elfzwo_icon( 'clock', 'h-4 w-4 text-signal' ); ?><span class="font-medium"><?php echo esc_html( $zeit ); ?></span></div><?php endif; ?>
											<?php if ( $ort ) : ?><div class="flex items-center gap-2 text-foreground"><?php echo elfzwo_icon( 'map-pin', 'h-4 w-4 text-signal' ); ?><span class="font-medium"><?php echo esc_html( $ort ); ?></span></div><?php endif; ?>
										</div>
										<?php
										$termin_content = apply_filters( 'the_content', $termin->post_content );
										if ( $termin_content ) :
											?>
											<div class="mt-6 space-y-3 leading-relaxed elfzwo-termin-content"><?php echo $termin_content; // phpcs:ignore ?></div>
										<?php endif; ?>
									</div>
									<?php if ( $image ) : ?>
										<div class="hidden overflow-hidden rounded-xl md:block"><img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $termin->post_title ); ?>" class="h-full w-full object-cover"></div>
									<?php endif; ?>
								</div>
							</article>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</section>
