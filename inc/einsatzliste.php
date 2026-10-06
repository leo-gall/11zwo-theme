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
	$arten  = elfzwo_einsatz_arten();
	$svg    = '<svg viewBox="0 0 42 42" class="h-full w-full -rotate-90" aria-hidden="true"><circle cx="21" cy="21" r="15.915" fill="none" stroke="#ebe6e6" stroke-width="7"/>';
	$versatz = 0;
	foreach ( $je_art as $art => $n ) {
		if ( ! $n ) {
			continue;
		}
		$anteil = 100 * $n / $anzahl;
		$svg   .= sprintf(
			'<circle cx="21" cy="21" r="15.915" fill="none" stroke="%1$s" stroke-width="7" stroke-dasharray="%2$s %3$s" stroke-dashoffset="%4$s" data-art="%5$s" class="transition-opacity"/>',
			esc_attr( $arten[ $art ]['farbe'] ),
			esc_attr( round( $anteil, 3 ) ),
			esc_attr( round( 100 - $anteil, 3 ) ),
			esc_attr( round( -$versatz, 3 ) ),
			esc_attr( $art )
		);
		$versatz += $anteil;
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
				<p class="text-sm font-semibold text-smoke">Verteilung nach Einsatzart – zum Filtern anklicken</p>
				<ul class="mt-3 grid gap-2 sm:grid-cols-2">
					<?php foreach ( $arten as $art => $info ) : ?>
						<li>
							<button type="button" data-filter="<?php echo esc_attr( $art ); ?>" aria-pressed="false" class="flex w-full items-center gap-3 border border-transparent px-3 py-2 text-left hover:border-border disabled:cursor-default disabled:opacity-40 aria-pressed:border-foreground" <?php disabled( 0 === $je_art[ $art ] ); ?>>
								<span class="h-4 w-4 shrink-0" style="background:<?php echo esc_attr( $info['farbe'] ); ?>" aria-hidden="true"></span>
								<span class="flex-1"><?php echo esc_html( $info['label'] ); ?></span>
								<span class="font-bold"><?php echo esc_html( $je_art[ $art ] ); ?></span>
								<span class="w-12 text-right text-sm text-smoke"><?php echo esc_html( round( 100 * $je_art[ $art ] / $anzahl ) ); ?> %</span>
							</button>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>

		<div class="mt-10 overflow-x-auto">
			<table class="w-full min-w-[44rem] border-collapse text-left">
				<thead>
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
						<tr class="border-b border-border" data-art="<?php echo esc_attr( $e['art'] ); ?>">
							<td class="py-3 pr-4 text-smoke"><?php echo esc_html( $e['nummer'] ? $e['nummer'] : '' ); ?></td>
							<td class="whitespace-nowrap py-3 pr-4"><?php echo esc_html( date_i18n( 'd.m.Y', $e['zeit'] ) ); ?></td>
							<td class="whitespace-nowrap py-3 pr-4 text-smoke"><?php echo esc_html( date_i18n( 'H:i', $e['zeit'] ) ); ?> Uhr</td>
							<td class="whitespace-nowrap py-3 pr-4"><span class="inline-flex items-center gap-2 text-sm"><span class="h-2.5 w-2.5" style="background:<?php echo esc_attr( $arten[ $e['art'] ]['farbe'] ); ?>" aria-hidden="true"></span><?php echo esc_html( $arten[ $e['art'] ]['label'] ); ?></span></td>
							<td class="py-3 pr-4"><a href="<?php echo esc_url( get_permalink( $e['post'] ) ); ?>" class="font-semibold hover:text-signal hover:underline"><?php echo esc_html( wp_specialchars_decode( $e['post']->post_title, ENT_QUOTES ) ); ?></a></td>
							<td class="py-3"><?php echo esc_html( $e['ort'] ); ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	<?php endif; ?>
	<?php
	return ob_get_clean();
}

/** AJAX: Ansicht eines anderen Jahres als HTML-Fragment. */
function elfzwo_ajax_einsaetze() {
	$jahr  = isset( $_GET['jahr'] ) ? (int) $_GET['jahr'] : 0;
	// Nur Adressen dieser Website als Basis für die Jahres-Links zulassen.
	$seite = wp_validate_redirect( isset( $_GET['seite'] ) ? esc_url_raw( wp_unslash( $_GET['seite'] ) ) : '', home_url( '/einsaetze/' ) );
	wp_send_json_success( array( 'html' => elfzwo_einsaetze_ansicht( $jahr, $seite ) ) );
}
add_action( 'wp_ajax_elfzwo_einsaetze', 'elfzwo_ajax_einsaetze' );
add_action( 'wp_ajax_nopriv_elfzwo_einsaetze', 'elfzwo_ajax_einsaetze' );
