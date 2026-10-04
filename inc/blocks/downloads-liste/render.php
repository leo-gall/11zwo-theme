<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$kategorien = get_terms( array( 'taxonomy' => 'download_kategorie', 'hide_empty' => true ) );
?>
<section class="mx-auto max-w-7xl space-y-14 px-5 py-8 md:px-8">
	<?php if ( is_wp_error( $kategorien ) || ! $kategorien ) : ?>
		<p class="text-muted-foreground">Noch keine Downloads vorhanden.</p>
	<?php endif; ?>
	<?php foreach ( $kategorien as $kategorie ) :
		$items = new WP_Query(
			array(
				'post_type'      => 'download',
				'posts_per_page' => -1,
				'orderby'        => 'title',
				'order'          => 'ASC',
				'tax_query'      => array( array( 'taxonomy' => 'download_kategorie', 'field' => 'term_id', 'terms' => $kategorie->term_id ) ),
			)
		);
		if ( ! $items->have_posts() ) {
			continue;
		}
		?>
		<div>
			<h2 class="border-b border-border pb-4 font-display text-3xl"><?php echo esc_html( $kategorie->name ); ?></h2>
			<div class="mt-6 space-y-4">
				<?php
				while ( $items->have_posts() ) :
					$items->the_post();
					$did          = get_the_ID();
					$datei_id     = elfzwo_meta( $did, 'datei_id', '' );
					$beschreibung = elfzwo_meta( $did, 'beschreibung', '' );
					$url          = $datei_id ? wp_get_attachment_url( $datei_id ) : '';
					$mime         = $datei_id ? get_post_mime_type( $datei_id ) : '';
					$is_zip       = $mime && false !== strpos( $mime, 'zip' );
					$filesize     = $datei_id ? size_format( filesize( get_attached_file( $datei_id ) ), 0 ) : '';
					$ext          = $datei_id ? strtoupper( pathinfo( get_attached_file( $datei_id ), PATHINFO_EXTENSION ) ) : '';
					?>
					<div class="flex flex-col gap-4 rounded-2xl border border-border bg-card p-5 sm:flex-row sm:items-center sm:justify-between">
						<div class="flex min-w-0 items-start gap-4">
							<span class="grid h-8 w-8 shrink-0 place-items-center text-signal"><?php echo elfzwo_icon( $is_zip ? 'file-archive' : 'file-text', 'h-5 w-5', 2.2 ); ?></span>
							<div class="min-w-0">
								<h3 class="font-display text-lg leading-tight"><?php the_title(); ?></h3>
								<?php if ( $beschreibung ) : ?><p class="mt-1 text-sm text-muted-foreground"><?php echo esc_html( $beschreibung ); ?></p><?php endif; ?>
								<p class="mt-2 text-xs uppercase tracking-widest text-muted-foreground">
									<?php echo esc_html( $ext ); ?><?php echo $filesize ? ' · ' . esc_html( $filesize ) : ''; ?> · Aktualisiert <?php echo esc_html( get_the_modified_date() ); ?>
								</p>
							</div>
						</div>
						<?php if ( $url ) : ?>
							<a href="<?php echo esc_url( $url ); ?>" download class="elfzwo-btn elfzwo-btn-secondary elfzwo-btn-sm shrink-0">
								<?php echo elfzwo_icon( 'download', 'h-4 w-4' ); ?> Herunterladen
							</a>
						<?php endif; ?>
					</div>
				<?php endwhile; wp_reset_postdata(); ?>
			</div>
		</div>
	<?php endforeach; ?>
</section>
