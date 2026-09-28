<?php
/**
 * Echte Warnmeldungen der NINA-Warn-App (Bundesamt für Bevölkerungsschutz
 * und Katastrophenhilfe) für die im Abschnitts-Überschrift-Block
 * hinterlegte Postleitzahl. NINA liefert Warnungen nur auf Kreisebene über einen
 * "Amtlichen Regionalschlüssel" (ARS) — die PLZ wird daher einmalig über
 * die freie openplzapi.org auf den zuständigen Kreis (ARS) abgebildet.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Löst eine 5-stellige PLZ auf den 12-stelligen NINA-ARS (Kreisebene,
 * letzte 7 Stellen "0") auf. Ergebnis wird dauerhaft gecacht, da sich die
 * Kreiszugehörigkeit einer PLZ praktisch nie ändert.
 */
function elfzwo_nina_resolve_ars( $plz ) {
	$plz = preg_replace( '/\D/', '', (string) $plz );
	if ( 5 !== strlen( $plz ) ) {
		return '';
	}

	$cache_key = 'elfzwo_nina_ars_' . $plz;
	$cached    = get_transient( $cache_key );
	if ( false !== $cached ) {
		return $cached;
	}

	$response = wp_remote_get(
		'https://openplzapi.org/de/Localities?postalCode=' . rawurlencode( $plz ),
		array( 'timeout' => 8 )
	);

	if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
		return '';
	}

	$data = json_decode( wp_remote_retrieve_body( $response ), true );
	if ( empty( $data[0]['district']['key'] ) ) {
		return '';
	}

	$kreisschluessel = preg_replace( '/\D/', '', $data[0]['district']['key'] );
	$ars             = str_pad( $kreisschluessel, 5, '0' ) . '0000000';

	set_transient( $cache_key, $ars, MONTH_IN_SECONDS );

	return $ars;
}

/**
 * Aktive NINA-Warnungen für die übergebene PLZ.
 * Liefert ein Array mit level/title/message/sent, neueste zuerst.
 */
function elfzwo_nina_get_warnings( $plz ) {
	if ( ! $plz ) {
		return array();
	}

	$ars = elfzwo_nina_resolve_ars( $plz );
	if ( ! $ars ) {
		return array();
	}

	$cache_key = 'elfzwo_nina_warnings_' . $ars;
	$cached    = get_transient( $cache_key );
	if ( false !== $cached ) {
		return $cached;
	}

	$response = wp_remote_get(
		'https://warnung.bund.de/api31/dashboard/' . rawurlencode( $ars ) . '.json',
		array( 'timeout' => 8 )
	);

	if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
		return get_transient( $cache_key . '_stale' ) ?: array();
	}

	$raw = json_decode( wp_remote_retrieve_body( $response ), true );
	if ( ! is_array( $raw ) ) {
		return array();
	}

	$severity_labels = array(
		'Minor'    => 'Hinweis',
		'Moderate' => 'Warnung',
		'Severe'   => 'Gefahr',
		'Extreme'  => 'Extreme Gefahr',
	);

	$warnings = array();
	foreach ( $raw as $item ) {
		$data       = $item['payload']['data'] ?? array();
		$severity   = $data['severity'] ?? 'Minor';
		$id         = $item['id'] ?? '';
		$warnings[] = array(
			'level'   => $severity_labels[ $severity ] ?? $severity,
			'title'   => $data['headline'] ?? '',
			'sent'    => $item['sent'] ?? '',
			'source'  => $data['provider'] ?? 'NINA',
			'url'     => $id ? 'https://warnung.bund.de/meldungen/' . rawurlencode( $id ) : '',
		);
	}

	usort(
		$warnings,
		function ( $a, $b ) {
			return strcmp( $b['sent'], $a['sent'] );
		}
	);

	set_transient( $cache_key, $warnings, 10 * MINUTE_IN_SECONDS );
	set_transient( $cache_key . '_stale', $warnings, DAY_IN_SECONDS );

	return $warnings;
}

/**
 * Ortsname aus der Einstellung "PLZ & Ort" (z. B. "86926 Greifenberg" -> "Greifenberg").
 */
function elfzwo_ort_name() {
	$plz_ort = elfzwo_option( 'elfzwo_geraetehaus_plz_ort', '' );
	$ort     = trim( preg_replace( '/^\s*\d+\s*/', '', $plz_ort ) );
	return $ort ?: $plz_ort;
}

/**
 * Logo des Bundesamts für Bevölkerungsschutz und Katastrophenhilfe (BBK):
 * oranger Kreis mit dunkelblauem Dreieck, das amtliche Zeichen für
 * Warnmeldungen. Feste zwei Farben, daher eigenes SVG statt elfzwo_icon().
 */
function elfzwo_bbk_logo_svg( $class = 'h-6 w-6' ) {
	return sprintf(
		'<svg class="%s shrink-0" viewBox="0 0 40 40" aria-hidden="true"><circle cx="20" cy="20" r="20" fill="#EF7D00"/><path d="M20 9 32 30H8Z" fill="#132C5C"/></svg>',
		esc_attr( $class )
	);
}

/**
 * Kompakte Darstellung der aktuellen Warnungen, passend für den
 * Beschreibungs-Slot des Abschnitts-Überschrift-Blocks (max-w-md).
 * Liegen Warnungen vor, wird nur die Anzahl mit BBK-Logo gezeigt; ein
 * Klick öffnet ein Modal mit allen Meldungen (siehe nina-warnungen.js).
 */
function elfzwo_nina_render_compact( $plz ) {
	$plz      = preg_replace( '/\D/', '', (string) $plz );
	$warnings = elfzwo_nina_get_warnings( $plz );

	ob_start();

	if ( ! $plz ) {
		?>
		<p class="max-w-md self-end text-sm text-muted-foreground">Für Warnmeldungen bitte im Block eine Postleitzahl hinterlegen.</p>
		<?php
		return ob_get_clean();
	}

	if ( ! $warnings ) {
		?>
		<div class="flex max-w-md items-start gap-2 self-end text-sm text-muted-foreground">
			<?php echo elfzwo_icon( 'shield-check', 'mt-0.5 h-4 w-4 shrink-0 text-primary' ); ?>
			<span>Keine aktuellen Warnungen für <?php echo esc_html( elfzwo_ort_name() ); ?></span>
		</div>
		<?php
		return ob_get_clean();
	}

	$count = count( $warnings );
	?>
	<button type="button" class="elfzwo-nina-toggle flex w-full shrink-0 items-center justify-center gap-2 self-end rounded-full border border-destructive/40 bg-destructive/10 py-1.5 pl-2 pr-3.5 text-sm text-destructive sm:inline-flex sm:w-auto sm:justify-start" aria-haspopup="dialog" aria-controls="elfzwo-nina-modal">
		<?php echo elfzwo_bbk_logo_svg( 'h-6 w-6' ); ?>
		<span class="font-semibold"><?php echo esc_html( $count ); ?> <?php echo esc_html( 1 === $count ? 'Warnung liegt vor' : 'Warnungen liegen vor' ); ?></span>
	</button>

	<div id="elfzwo-nina-modal" class="elfzwo-nina-modal fixed inset-0 z-50 hidden items-center justify-center p-4" role="dialog" aria-modal="true" aria-label="Aktuelle Warnmeldungen">
		<div class="elfzwo-nina-modal-backdrop absolute inset-0 bg-ink/60" data-nina-close></div>
		<div class="relative flex max-h-[80vh] w-full max-w-lg flex-col rounded-2xl border border-border bg-background shadow-xl">
			<div class="flex items-center justify-between gap-3 border-b border-border px-5 py-4">
				<div class="flex items-center gap-2">
					<?php echo elfzwo_bbk_logo_svg( 'h-6 w-6' ); ?>
					<h3 class="font-display text-lg">Aktuelle Warnmeldungen</h3>
				</div>
				<button type="button" class="grid h-8 w-8 shrink-0 place-items-center rounded-full text-muted-foreground hover:bg-secondary" data-nina-close aria-label="Schließen"><?php echo elfzwo_icon( 'x', 'h-4 w-4' ); ?></button>
			</div>
			<div class="min-h-0 flex-1 overflow-y-auto p-5">
				<ul class="space-y-3">
					<?php foreach ( $warnings as $w ) : ?>
						<li>
							<?php $tag = $w['url'] ? 'a' : 'div'; ?>
							<<?php echo $tag; ?>
								<?php if ( $w['url'] ) : ?>href="<?php echo esc_url( $w['url'] ); ?>" target="_blank" rel="noopener"<?php endif; ?>
								class="block rounded-xl border border-destructive/40 bg-destructive/10 p-3<?php echo $w['url'] ? ' transition-colors hover:bg-destructive/15' : ''; ?>"
							>
								<div class="flex items-center justify-between gap-2">
									<span class="text-[11px] font-bold uppercase tracking-widest text-destructive"><?php echo esc_html( $w['level'] ); ?></span>
									<?php if ( $w['sent'] ) : ?>
										<span class="shrink-0 text-xs text-muted-foreground"><?php echo esc_html( wp_date( 'd.m.Y, H:i \U\h\r', strtotime( $w['sent'] ) ) ); ?></span>
									<?php endif; ?>
								</div>
								<p class="mt-1 text-sm font-semibold text-foreground"><?php echo esc_html( $w['title'] ); ?></p>
								<div class="mt-1 flex items-center justify-between gap-2">
									<?php if ( $w['source'] ) : ?><p class="text-xs text-muted-foreground">Quelle: <?php echo esc_html( $w['source'] ); ?></p><?php endif; ?>
									<?php if ( $w['url'] ) : ?><span class="shrink-0 text-xs font-semibold text-destructive">Mehr erfahren &rarr;</span><?php endif; ?>
								</div>
							</<?php echo $tag; ?>>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
	</div>
	<?php
	return ob_get_clean();
}
