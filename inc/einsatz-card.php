<?php
/**
 * Wiederverwendbare "Einsatzdaten"-Card (Fakten-Grid, Fahrzeuge, weitere
 * Einsatzkräfte) für die Einsatz-Detailseite.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Rendert die vollständige "Einsatzdaten"-Card (Fakten-Grid, Fahrzeuge,
 * weitere Einsatzkräfte, optional Berichtstext) für einen Einsatz.
 */
function elfzwo_render_einsatzdaten_card( $post_id, $show_content = true ) {
	$datum         = elfzwo_meta( $post_id, 'datum', '' );
	$ort           = elfzwo_einsatzort_name( $post_id );
	$einsatznummer = (int) elfzwo_meta( $post_id, 'einsatznummer', 0 );

	$fahrzeug_ids = array_filter( array_map( 'intval', explode( ',', elfzwo_meta( $post_id, 'fahrzeuge', '' ) ) ) );
	$fahrzeuge    = array();
	foreach ( $fahrzeug_ids as $fid ) {
		$fahrzeug_post = get_post( $fid );
		if ( ! $fahrzeug_post || 'fahrzeug' !== $fahrzeug_post->post_type ) {
			continue;
		}
		$fahrzeuge[] = elfzwo_meta( $fid, 'tag', '' ) ?: $fahrzeug_post->post_title;
	}

	$kraft_ids      = array_filter( array_map( 'intval', explode( ',', elfzwo_meta( $post_id, 'einsatzkraefte', '' ) ) ) );
	$einsatzkraefte = array();
	foreach ( $kraft_ids as $kid ) {
		$kraft_post = get_post( $kid );
		if ( ! $kraft_post || 'externe_kraft' !== $kraft_post->post_type ) {
			continue;
		}
		$einsatzkraefte[] = array(
			'name' => $kraft_post->post_title,
			'url'  => elfzwo_meta( $kid, 'url', '' ),
		);
	}

	$ts = $datum ? strtotime( $datum ) : false;

	$facts = array();
	if ( $ts ) {
		$facts[] = array( 'icon' => 'calendar-days', 'label' => 'Datum', 'value' => date_i18n( 'j. F Y', $ts ) );
		$facts[] = array( 'icon' => 'clock', 'label' => 'Uhrzeit', 'value' => date_i18n( 'H:i', $ts ) . ' Uhr' );
	}
	if ( $ort ) {
		$facts[] = array( 'icon' => 'map-pin', 'label' => 'Einsatzort', 'value' => $ort );
	}
	if ( $einsatznummer && $ts ) {
		$facts[] = array( 'icon' => 'hash', 'label' => 'Einsatz-Nr.', 'value' => sprintf( '%02d/%s', $einsatznummer, gmdate( 'Y', $ts ) ) );
	}

	ob_start();
	?>
	<div class="mt-10 rounded-lg border border-border bg-card p-6 md:p-8">
		<h2 class="font-display text-xl">Einsatzdaten</h2>
		<?php if ( $show_content ) : ?>
			<?php
			$content = get_post_field( 'post_content', $post_id );
			if ( $content ) :
				?>
				<div class="mt-4 text-muted-foreground"><?php echo apply_filters( 'the_content', $content ); // phpcs:ignore -- Kern-Filter, bereits sicher ?></div>
			<?php endif; ?>
		<?php endif; ?>

		<dl class="mt-5 grid gap-3 sm:grid-cols-2">
			<?php foreach ( $facts as $f ) : ?>
				<div class="flex items-center gap-3 rounded-2xl border border-border p-4">
					<span class="grid h-8 w-8 shrink-0 place-items-center text-signal"><?php echo elfzwo_icon( $f['icon'], 'h-5 w-5', 2.2 ); ?></span>
					<div class="min-w-0">
						<dt class="text-[11px] uppercase tracking-widest text-muted-foreground"><?php echo esc_html( $f['label'] ); ?></dt>
						<dd class="font-display text-lg leading-tight"><?php echo esc_html( $f['value'] ); ?></dd>
					</div>
				</div>
			<?php endforeach; ?>
		</dl>

		<?php if ( $fahrzeuge || $einsatzkraefte ) : ?>
			<div class="mt-5 space-y-3 border-t border-border pt-5">
				<?php if ( $fahrzeuge ) : ?>
					<div class="flex flex-wrap items-center gap-2">
						<span class="inline-flex shrink-0 items-center gap-1.5 text-[11px] font-semibold uppercase tracking-widest text-muted-foreground"><?php echo elfzwo_icon( 'truck', 'h-3.5 w-3.5' ); ?> Fahrzeuge</span>
						<?php foreach ( $fahrzeuge as $f ) : ?>
							<span class="rounded-full border border-border px-2.5 py-0.5 text-xs font-medium text-foreground/80"><?php echo esc_html( $f ); ?></span>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<?php if ( $einsatzkraefte ) : ?>
					<div class="flex flex-wrap items-center gap-2">
						<span class="inline-flex shrink-0 items-center gap-1.5 text-[11px] font-semibold uppercase tracking-widest text-muted-foreground"><?php echo elfzwo_icon( 'heart-handshake', 'h-3.5 w-3.5' ); ?> Weitere Kräfte</span>
						<?php foreach ( $einsatzkraefte as $kraft ) : ?>
							<?php if ( ! empty( $kraft['name'] ) ) : ?>
								<?php if ( ! empty( $kraft['url'] ) ) : ?>
									<a href="<?php echo esc_url( $kraft['url'] ); ?>" target="_blank" rel="noopener noreferrer" class="rounded-full border border-border px-2.5 py-1 text-xs font-semibold text-foreground underline decoration-dotted transition-colors hover:bg-secondary"><?php echo esc_html( $kraft['name'] ); ?></a>
								<?php else : ?>
									<span class="rounded border border-border px-2 py-0.5 text-xs font-semibold text-foreground"><?php echo esc_html( $kraft['name'] ); ?></span>
								<?php endif; ?>
							<?php endif; ?>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
	<?php
	return ob_get_clean();
}
