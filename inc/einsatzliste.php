<?php
/**
 * Einsätze-Seite: Verteilung nach Einsatzart (Ringdiagramm) und Tabelle eines
 * Jahres. Dieselbe Ansicht liefert der erste Seitenaufruf und der AJAX-
 * Endpunkt, über den assets/js/einsaetze.js das Jahr ohne Neuladen wechselt.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Einsatzarten in der Reihenfolge der Legende, mit Farbe. */
function elfzwo_einsatz_arten() {
	return array(
		'brand'  => array( 'label' => 'Brand', 'farbe' => '#c4161c' ),
		'vu'     => array( 'label' => 'Verkehrsunfall', 'farbe' => '#7b3fa0' ),
		'thl'    => array( 'label' => 'Technische Hilfeleistung', 'farbe' => '#1f6fd1' ),
		'fr'     => array( 'label' => 'First Responder', 'farbe' => '#2e8b45' ),
		'abc'    => array( 'label' => 'ABC', 'farbe' => '#ef7d00' ),
		'info'   => array( 'label' => 'Info', 'farbe' => '#878787' ),
	);
}

/**
 * Einsatzart aus dem Stichwort: "B …" Brand, "ABC …" ABC, THL mit "VU"
 * Verkehrsunfall, THL mit "First Responder" First Responder, übrige THL
 * Technische Hilfeleistung, alles andere (SON, INFO) Info.
 */
function elfzwo_einsatz_art( $stichwort ) {
	$stichwort = wp_specialchars_decode( (string) $stichwort, ENT_QUOTES );
	$kuerzel   = strtoupper( strtok( $stichwort, ' ' ) );
	if ( 'B' === $kuerzel ) {
		return 'brand';
	}
	if ( 'ABC' === $kuerzel ) {
		return 'abc';
	}
	// Rettungsdienst-Stichwörter ("RD 1 …", "RD 2 …") und "THL … First Responder".
	if ( 'RD' === $kuerzel || preg_match( '/first\s*responder/i', $stichwort ) ) {
		return 'fr';
	}
	if ( 'THL' === $kuerzel ) {
		return preg_match( '/\bVU\b/', $stichwort ) ? 'vu' : 'thl';
	}
	return 'info';
}

/** Alle Einsätze, neueste zuerst. */
function elfzwo_einsaetze_alle() {
	$alle = array();
	foreach ( get_posts( array( 'post_type' => 'einsatz', 'posts_per_page' => -1 ) ) as $einsatz ) {
		$zeit   = strtotime( elfzwo_einsatz_zeitpunkt( $einsatz->ID ) );
		$alle[] = array(
			'post'   => $einsatz,
			'zeit'   => $zeit,
			'jahr'   => (int) gmdate( 'Y', $zeit ),
			'nummer' => (int) elfzwo_meta( $einsatz->ID, 'einsatznummer', 0 ),
			'ort'    => elfzwo_einsatzort_name( $einsatz->ID ),
			'art'    => elfzwo_einsatz_art( $einsatz->post_title ),
		);
	}
	usort( $alle, function ( $a, $b ) { return $b['zeit'] <=> $a['zeit']; } );
	return $alle;
}

/** Ringdiagramm der Verteilung (Kreisumfang 100, damit Anteile = Strichlängen). */
function elfzwo_einsaetze_ring( $je_art, $anzahl ) {
	// Ring aus einzelnen Kreisbögen mit kleinen weißen Lücken, oben beginnend im Uhrzeigersinn.
	$arten = elfzwo_einsatz_arten();
	$r     = 16;
	$luecke = 1.2;
	$svg   = '<svg viewBox="0 0 42 42" class="h-full w-full" aria-hidden="true">';
	$start = 0;
	$teile = array_filter( $je_art );
	foreach ( $teile as $art => $n ) {
		$grad = 360 * $n / $anzahl;
		if ( 1 === count( $teile ) ) {
			$svg .= sprintf( '<circle cx="21" cy="21" r="%1$s" fill="none" stroke="%2$s" stroke-width="7" data-art="%3$s"/>', $r, esc_attr( $arten[ $art ]['farbe'] ), esc_attr( $art ) );
			break;
		}
		$von  = $start + min( $luecke, $grad / 4 ) / 2;
		$bis  = $start + $grad - min( $luecke, $grad / 4 ) / 2;
		$punkt = function ( $winkel ) use ( $r ) {
			$rad = deg2rad( $winkel - 90 );
			return round( 21 + $r * cos( $rad ), 3 ) . ' ' . round( 21 + $r * sin( $rad ), 3 );
		};
		$svg .= sprintf(
			'<path d="M%1$s A%2$s %2$s 0 %3$d 1 %4$s" fill="none" stroke="%5$s" stroke-width="7" data-art="%6$s"/>',
			$punkt( $von ),
			$r,
			$bis - $von > 180 ? 1 : 0,
			$punkt( $bis ),
			esc_attr( $arten[ $art ]['farbe'] ),
			esc_attr( $art )
		);
		$start += $grad;
	}
	return $svg . '</svg>';
}

/** Komplette Ansicht für ein Jahr: Kopf mit Jahren, Verteilung, Tabelle. */
function elfzwo_einsaetze_ansicht( $jahr, $seite_url ) {
	$alle  = elfzwo_einsaetze_alle();
	$jahre = array_values( array_unique( wp_list_pluck( $alle, 'jahr' ) ) );
	rsort( $jahre );
	$jahr = in_array( (int) $jahr, $jahre, true ) ? (int) $jahr : ( $jahre[0] ?? (int) gmdate( 'Y' ) );

	$im_jahr = array_values( array_filter( $alle, function ( $e ) use ( $jahr ) { return $e['jahr'] === $jahr; } ) );
	$anzahl  = count( $im_jahr );
	$arten   = elfzwo_einsatz_arten();
	$je_art  = array_fill_keys( array_keys( $arten ), 0 );
	foreach ( $im_jahr as $e ) {
		$je_art[ $e['art'] ]++;
	}

	ob_start();
	?>
	<div class="flex flex-wrap items-end justify-between gap-6">
		<div>
			<h2 class="font-display text-3xl font-black text-signal md:text-4xl" tabindex="-1" data-einsaetze-titel>Einsätze <?php echo esc_html( $jahr ); ?></h2>
		</div>
		<?php if ( count( $jahre ) > 1 ) : ?>
			<nav class="flex flex-wrap gap-2" aria-label="Jahr wählen">
				<?php foreach ( $jahre as $j ) : ?>
					<a href="<?php echo esc_url( add_query_arg( 'einsatz_jahr', $j, $seite_url ) ); ?>" data-jahr="<?php echo esc_attr( $j ); ?>" class="border px-4 py-2 text-sm font-bold <?php echo $j === $jahr ? 'border-signal bg-signal text-white' : 'border-border text-foreground hover:border-signal hover:text-signal'; ?>" <?php echo $j === $jahr ? 'aria-current="true"' : ''; ?>><?php echo esc_html( $j ); ?></a>
				<?php endforeach; ?>
			</nav>
		<?php endif; ?>
	</div>

	<?php if ( ! $anzahl ) : ?>
		<p class="mt-6 text-smoke">Für dieses Jahr sind noch keine Einsätze eingetragen.</p>
	<?php else : ?>
		<div class="mt-10 grid items-center gap-8 border border-border p-6 sm:grid-cols-[12rem_1fr] md:p-8">
			<div class="relative mx-auto h-48 w-48">
				<?php echo elfzwo_einsaetze_ring( $je_art, $anzahl ); // phpcs:ignore -- bereits escaped ?>
				<span class="absolute inset-0 grid place-items-center text-center leading-tight"><span><span class="block font-display text-4xl font-black"><?php echo esc_html( $anzahl ); ?></span><span class="text-sm text-smoke">Einsätze</span></span></span>
			</div>
			<div>
				<ul class="mt-3 grid gap-x-10 gap-y-1 xl:grid-cols-2">
					<?php foreach ( $arten as $art => $info ) : ?>
						<li>
							<button type="button" data-filter="<?php echo esc_attr( $art ); ?>" aria-pressed="false" class="flex w-full items-center gap-3 border border-transparent px-2 py-2 sm:px-3 text-left hover:border-border disabled:cursor-default disabled:opacity-40 aria-pressed:border-foreground" <?php disabled( 0 === $je_art[ $art ] ); ?>>
								<span class="h-4 w-4 shrink-0" style="background:<?php echo esc_attr( $info['farbe'] ); ?>" aria-hidden="true"></span>
								<span class="min-w-0 flex-1 xl:whitespace-nowrap"><?php echo esc_html( $info['label'] ); ?></span>
								<span class="w-6 shrink-0 text-right font-bold sm:w-8"><?php echo esc_html( $je_art[ $art ] ); ?></span>
								<span class="w-10 shrink-0 text-right text-sm text-smoke sm:w-12"><?php echo esc_html( round( 100 * $je_art[ $art ] / $anzahl ) ); ?> %</span>
							</button>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>

		<div class="mt-10 overflow-x-auto" data-einsaetze-tabelle>
			<table class="w-full border-collapse text-left md:min-w-[44rem]">
				<thead class="hidden md:table-header-group">
					<tr class="border-b-2 border-foreground text-sm">
						<th scope="col" class="py-3 pr-4 font-bold">Nr.</th>
						<th scope="col" class="py-3 pr-4 font-bold">Datum</th>
						<th scope="col" class="py-3 pr-4 font-bold">Uhrzeit</th>
						<th scope="col" class="py-3 pr-4 font-bold">Einsatzart</th>
						<th scope="col" class="py-3 pr-4 font-bold">Einsatzstichwort</th>
						<th scope="col" class="py-3 font-bold">Ort</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $im_jahr as $e ) : ?>
						<?php $id = $e['post']->ID; ?>
						<tr id="einsatz-<?php echo esc_attr( $id ); ?>" class="cursor-pointer scroll-mt-32 border-b border-border hover:bg-ash" data-art="<?php echo esc_attr( $e['art'] ); ?>" data-zeile data-einsatz-nummer="<?php echo esc_attr( $e['nummer'] ); ?>" data-einsatz-titel="<?php echo esc_attr( wp_specialchars_decode( $e['post']->post_title, ENT_QUOTES ) ); ?>" data-einsatz-datum="<?php echo esc_attr( date_i18n( 'd.m.Y H:i', $e['zeit'] ) ); ?>" data-einsatz-ort="<?php echo esc_attr( $e['ort'] ); ?>">
							<td class="w-10 py-3 pr-3 align-top text-smoke md:w-auto md:pr-4 md:align-middle"><?php echo esc_html( $e['nummer'] ? $e['nummer'] : '' ); ?></td>
							<td class="hidden whitespace-nowrap py-3 pr-4 md:table-cell"><?php echo esc_html( date_i18n( 'd.m.Y', $e['zeit'] ) ); ?></td>
							<td class="hidden whitespace-nowrap py-3 pr-4 text-smoke md:table-cell"><?php echo esc_html( date_i18n( 'H:i', $e['zeit'] ) ); ?> Uhr</td>
							<td class="hidden whitespace-nowrap py-3 pr-4 md:table-cell"><span class="inline-flex items-center gap-2 text-sm"><span class="h-2.5 w-2.5" style="background:<?php echo esc_attr( $arten[ $e['art'] ]['farbe'] ); ?>" aria-hidden="true"></span><?php echo esc_html( $arten[ $e['art'] ]['label'] ); ?></span></td>
							<td class="py-3 md:pr-4"><button type="button" class="group flex w-full items-center justify-between gap-2 text-left font-semibold md:w-auto md:justify-start" aria-expanded="false" aria-controls="einsatz-<?php echo esc_attr( $id ); ?>-details" data-details><?php echo esc_html( wp_specialchars_decode( $e['post']->post_title, ENT_QUOTES ) ); ?><?php echo elfzwo_icon( 'chevron-down', 'h-4 w-4 shrink-0 text-smoke transition-transform duration-300 group-aria-expanded:rotate-180' ); ?></button><span class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-0.5 text-sm text-smoke md:hidden"><span><?php echo esc_html( date_i18n( 'd.m.Y, H:i', $e['zeit'] ) ); ?> Uhr</span><?php if ( $e['ort'] ) : ?><span><?php echo esc_html( $e['ort'] ); ?></span><?php endif; ?><span class="inline-flex items-center gap-1.5"><span class="h-2 w-2" style="background:<?php echo esc_attr( $arten[ $e['art'] ]['farbe'] ); ?>" aria-hidden="true"></span><?php echo esc_html( $arten[ $e['art'] ]['label'] ); ?></span></span></td>
							<td class="hidden py-3 md:table-cell"><?php echo esc_html( $e['ort'] ); ?></td>
						</tr>
						<tr id="einsatz-<?php echo esc_attr( $id ); ?>-details" class="elfzwo-einsatz-details">
							<td colspan="6"><div class="elfzwo-einsatz-klappe"><div><?php echo elfzwo_einsatz_details( $id ); // phpcs:ignore -- bereits escaped ?></div></div></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	<?php endif; ?>
	<?php
	return ob_get_clean();
}

/** Aufklappbare Details eines Einsatzes direkt unter seiner Tabellenzeile. */
function elfzwo_einsatz_details( $post_id ) {
	$namen = function ( $meta, $typ ) use ( $post_id ) {
		$liste = array();
		foreach ( array_filter( array_map( 'intval', explode( ',', elfzwo_meta( $post_id, $meta, '' ) ) ) ) as $id ) {
			$post = get_post( $id );
			if ( $post && $typ === $post->post_type ) {
				$liste[] = array(
					'name' => 'fahrzeug' === $typ ? ( elfzwo_meta( $id, 'tag', '' ) ?: $post->post_title ) : $post->post_title,
					'url'  => 'externe_kraft' === $typ ? elfzwo_meta( $id, 'url', '' ) : '',
				);
			}
		}
		return $liste;
	};
	$bericht = trim( (string) get_post_field( 'post_content', $post_id ) );
	$listen  = array_filter(
		array(
			'Fahrzeuge'      => $namen( 'fahrzeuge', 'fahrzeug' ),
			'Weitere Kräfte' => $namen( 'einsatzkraefte', 'externe_kraft' ),
		)
	);
	ob_start();
	?>
	<div class="elfzwo-einsatz-klappe-inhalt bg-cream px-5 py-4 md:px-6">
		<?php if ( $bericht ) : ?>
			<div class="max-w-3xl space-y-2 text-[0.95rem] leading-relaxed text-foreground/85"><?php echo apply_filters( 'the_content', $bericht ); // phpcs:ignore -- Kern-Filter ?></div>
		<?php else : ?>
			<p class="text-[0.95rem] text-smoke">Zu diesem Einsatz gibt es keinen Bericht.</p>
		<?php endif; ?>
		<?php foreach ( $listen as $titel => $eintraege ) : ?>
			<p class="mt-3 flex flex-wrap items-center gap-1.5 text-sm">
				<span class="mr-1 font-semibold text-smoke"><?php echo esc_html( $titel ); ?>:</span>
				<?php foreach ( $eintraege as $e ) : ?>
					<?php if ( $e['url'] ) : ?>
						<a href="<?php echo esc_url( $e['url'] ); ?>" target="_blank" rel="noopener noreferrer" class="border border-border bg-white px-2 py-0.5 font-semibold hover:border-signal hover:text-signal"><?php echo esc_html( $e['name'] ); ?></a>
					<?php else : ?>
						<span class="border border-border bg-white px-2 py-0.5 font-semibold"><?php echo esc_html( $e['name'] ); ?></span>
					<?php endif; ?>
				<?php endforeach; ?>
			</p>
		<?php endforeach; ?>
	</div>
	<?php
	return ob_get_clean();
}

/** Eine eigene Einsatzseite gibt es nicht mehr: alte Links führen zur Zeile in der Tabelle. */
function elfzwo_einsatz_zur_tabelle() {
	if ( ! is_singular( 'einsatz' ) || is_preview() ) {
		return;
	}
	$id   = get_queried_object_id();
	$jahr = (int) gmdate( 'Y', strtotime( elfzwo_einsatz_zeitpunkt( $id ) ) );
	wp_safe_redirect( add_query_arg( 'einsatz_jahr', $jahr, home_url( '/einsaetze/' ) ) . '#einsatz-' . $id, 301 );
	exit;
}
add_action( 'template_redirect', 'elfzwo_einsatz_zur_tabelle', 5 );

/** AJAX: Ansicht eines anderen Jahres als HTML-Fragment. */
function elfzwo_ajax_einsaetze() {
	$jahr  = isset( $_GET['jahr'] ) ? (int) $_GET['jahr'] : 0;
	// Nur Adressen dieser Website als Basis für die Jahres-Links zulassen.
	$seite = wp_validate_redirect( isset( $_GET['seite'] ) ? esc_url_raw( wp_unslash( $_GET['seite'] ) ) : '', home_url( '/einsaetze/' ) );
	wp_send_json_success( array( 'html' => elfzwo_einsaetze_ansicht( $jahr, $seite ) ) );
}
add_action( 'wp_ajax_elfzwo_einsaetze', 'elfzwo_ajax_einsaetze' );
add_action( 'wp_ajax_nopriv_elfzwo_einsaetze', 'elfzwo_ajax_einsaetze' );
