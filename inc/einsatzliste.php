<?php
/**
 * Einsatzchronik-Card (Jahr-Filter, Tabelle, Pagination): gemeinsame
 * Render-Logik für den ersten Seitenaufruf UND für die AJAX-Aktualisierung
 * ohne Hard-Reload (siehe assets/js/einsatzliste.js).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function elfzwo_einsatzliste_months_short() {
	return array( 'JAN', 'FEB', 'MÄR', 'APR', 'MAI', 'JUN', 'JUL', 'AUG', 'SEP', 'OKT', 'NOV', 'DEZ' );
}

function elfzwo_einsatzliste_year_link( $page_id, $year, $seite = 1 ) {
	return esc_url( add_query_arg( array( 'einsatz_jahr' => $year, 'einsatz_seite' => $seite ), get_permalink( $page_id ) ) );
}

/**
 * Ermittelt Jahre, ausgewähltes Jahr und die passende Seite an Einsätzen
 * für die übergebene Jahr-/Seiten-Anfrage.
 */
function elfzwo_einsatzliste_data( $requested_year = '', $requested_page = 1 ) {
	$all_einsaetze = get_posts( array( 'post_type' => 'einsatz', 'posts_per_page' => -1 ) );
	usort(
		$all_einsaetze,
		function ( $a, $b ) {
			return strcmp( elfzwo_meta( $b->ID, 'datum', '' ), elfzwo_meta( $a->ID, 'datum', '' ) );
		}
	);

	$year_counts = array();
	foreach ( $all_einsaetze as $e ) {
		$datum = elfzwo_meta( $e->ID, 'datum', '' );
		if ( $datum ) {
			$jahr                 = substr( $datum, 0, 4 );
			$year_counts[ $jahr ] = ( $year_counts[ $jahr ] ?? 0 ) + 1;
		}
	}
	$years = array_map( 'strval', array_keys( $year_counts ) );
	rsort( $years );

	$selected_year = ( $requested_year && preg_match( '/^\d{4}$/', $requested_year ) ) ? (string) $requested_year : (string) ( $years[0] ?? gmdate( 'Y' ) );

	$filtered = array_values(
		array_filter(
			$all_einsaetze,
			function ( $e ) use ( $selected_year ) {
				return substr( elfzwo_meta( $e->ID, 'datum', '' ), 0, 4 ) === $selected_year;
			}
		)
	);

	$per_page    = 8;
	$total       = count( $filtered );
	$total_pages = max( 1, (int) ceil( $total / $per_page ) );
	$page        = max( 1, min( $total_pages, (int) $requested_page ) );
	$page_items  = array_slice( $filtered, ( $page - 1 ) * $per_page, $per_page );

	return compact( 'years', 'year_counts', 'selected_year', 'page_items', 'page', 'total_pages', 'total', 'per_page' );
}

/**
 * Rendert den kompletten Inhalt der Einsatzchronik-Card (Jahr-Filter,
 * Tabelle, Pagination) für ein bereits ermitteltes Datenpaket aus
 * elfzwo_einsatzliste_data().
 */
function elfzwo_einsatzliste_render( $post_id, array $data ) {
	$years         = $data['years'];
	$year_counts   = $data['year_counts'];
	$selected_year = $data['selected_year'];
	$page_items    = $data['page_items'];
	$page          = $data['page'];
	$total_pages   = $data['total_pages'];
	$total         = $data['total'];
	$per_page      = $data['per_page'];
	$months_short  = elfzwo_einsatzliste_months_short();
	ob_start();
	?>
	<div class="elfzwo-einsatzliste-years-row relative">
		<div class="elfzwo-einsatzliste-years-edge elfzwo-einsatzliste-years-edge--left" data-dir="-1" aria-hidden="true"></div>
		<div class="elfzwo-einsatzliste-years flex gap-2 overflow-x-auto pb-1" data-selected-year="<?php echo esc_attr( $selected_year ); ?>">
			<?php foreach ( $years as $y ) :
				$y_count = $year_counts[ $y ] ?? 0;
				$y_label = esc_html( $y_count ) . ' ' . esc_html( 1 === $y_count ? 'Einsatz' : 'Einsätze' ) . ' ' . esc_html( $y );
				$is_selected = $y === $selected_year;
				?>
				<a
					href="<?php echo elfzwo_einsatzliste_year_link( $post_id, $y, 1 ); ?>"
					data-year="<?php echo esc_attr( $y ); ?>"
					<?php if ( $is_selected ) : ?>aria-current="true"<?php endif; ?>
					class="elfzwo-einsatzliste-year-chip shrink-0 whitespace-nowrap rounded-full px-4 py-1.5 text-xs font-bold uppercase tracking-widest transition-colors <?php echo $is_selected ? 'bg-signal/20 text-signal' : 'bg-secondary text-muted-foreground hover:bg-border'; ?>"
				><?php echo $y_label; ?></a>
			<?php endforeach; ?>
		</div>
		<div class="elfzwo-einsatzliste-years-edge elfzwo-einsatzliste-years-edge--right" data-dir="1" aria-hidden="true"></div>
	</div>
	<div class="elfzwo-einsatzliste-body flex flex-1 flex-col transition-opacity">
	<h3 class="mt-3 font-display text-2xl">Letzte Einsätze</h3>

	<div class="mt-6 flex-1">
		<table class="w-full table-fixed text-sm">
			<thead>
				<tr class="border-b border-border text-left text-xs text-muted-foreground">
					<th class="w-14 py-2 font-medium">Datum</th>
					<th class="py-2 font-medium">Einsatz</th>
					<th class="w-8"></th>
				</tr>
			</thead>
			<tbody>
				<?php if ( ! $page_items ) : ?>
					<tr><td colspan="3" class="py-4 text-muted-foreground">Keine Einsätze in diesem Jahr.</td></tr>
				<?php endif; ?>
				<?php
				foreach ( $page_items as $e ) :
					$datum = elfzwo_meta( $e->ID, 'datum', '' );
					$ts    = $datum ? strtotime( $datum ) : false;
					$day   = $ts ? gmdate( 'd', $ts ) : '–';
					$month = $ts ? $months_short[ (int) gmdate( 'n', $ts ) - 1 ] : '';
					$ort   = elfzwo_einsatzort_name( $e->ID );

					$target_url = get_permalink( $e );
					?>
					<tr class="cursor-pointer border-b border-border last:border-0 hover:bg-secondary" onclick="window.location='<?php echo esc_url( $target_url ); ?>'">
						<td class="py-3 align-top">
							<div class="flex items-center gap-1 text-xs text-muted-foreground">
								<span class="font-display text-sm text-foreground"><?php echo esc_html( $day ); ?></span>
								<span class=""><?php echo esc_html( $month ); ?></span>
							</div>
						</td>
						<td class="py-3 align-top">
							<p class="truncate text-xs font-semibold leading-snug text-wood"><?php echo esc_html( $e->post_title ); ?></p>
							<?php if ( $ort ) : ?><p class="mt-1 flex min-w-0 items-center gap-1 text-xs leading-snug text-muted-foreground"><?php echo elfzwo_icon( 'map-pin', 'h-3 w-3 shrink-0' ); ?><span class="truncate"><?php echo esc_html( $ort ); ?></span></p><?php endif; ?>
						</td>
						<td class="py-3 align-top text-right">
							<a href="<?php echo esc_url( $target_url ); ?>" aria-label="Einsatz ansehen" class="inline-grid h-7 w-7 place-items-center rounded-full text-muted-foreground hover:bg-secondary hover:text-foreground"><?php echo elfzwo_icon( 'chevron-right', 'h-4 w-4' ); ?></a>
						</td>
					</tr>
				<?php endforeach; ?>
				<?php for ( $i = count( $page_items ); $i < $per_page; $i++ ) : ?>
					<tr class="border-b border-border last:border-0" aria-hidden="true">
						<td class="py-3 align-top">&nbsp;</td>
						<td class="py-3 align-top">
							<p class="text-xs font-semibold leading-snug text-transparent">&nbsp;</p>
							<p class="mt-1 text-xs leading-snug text-transparent">&nbsp;</p>
						</td>
						<td class="py-3 align-top">&nbsp;</td>
					</tr>
				<?php endfor; ?>
			</tbody>
		</table>
	</div>

	<div class="mt-4 flex items-center justify-between">
		<?php if ( $page > 1 ) : ?>
			<a href="<?php echo elfzwo_einsatzliste_year_link( $post_id, $selected_year, $page - 1 ); ?>" data-elfzwo-page="<?php echo esc_attr( $page - 1 ); ?>" class="elfzwo-einsatzliste-page grid h-8 w-8 place-items-center rounded-full text-foreground hover:bg-secondary"><?php echo elfzwo_icon( 'chevron-left', 'h-4 w-4' ); ?></a>
		<?php else : ?>
			<span class="grid h-8 w-8 place-items-center rounded-full text-muted-foreground opacity-30" aria-hidden="true"><?php echo elfzwo_icon( 'chevron-left', 'h-4 w-4' ); ?></span>
		<?php endif; ?>
		<span class="text-xs text-muted-foreground">Seite <?php echo esc_html( $page ); ?> von <?php echo esc_html( $total_pages ); ?></span>
		<?php if ( $page < $total_pages ) : ?>
			<a href="<?php echo elfzwo_einsatzliste_year_link( $post_id, $selected_year, $page + 1 ); ?>" data-elfzwo-page="<?php echo esc_attr( $page + 1 ); ?>" class="elfzwo-einsatzliste-page grid h-8 w-8 place-items-center rounded-full text-foreground hover:bg-secondary"><?php echo elfzwo_icon( 'chevron-right', 'h-4 w-4' ); ?></a>
		<?php else : ?>
			<span class="grid h-8 w-8 place-items-center rounded-full text-muted-foreground opacity-30" aria-hidden="true"><?php echo elfzwo_icon( 'chevron-right', 'h-4 w-4' ); ?></span>
		<?php endif; ?>
	</div>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * AJAX-Endpunkt: liefert die Einsatzchronik-Card für ein Jahr/eine Seite
 * als HTML-Fragment, damit das Frontend ohne Hard-Reload aktualisieren kann.
 */
function elfzwo_ajax_einsatzliste() {
	$post_id = isset( $_GET['post_id'] ) ? (int) $_GET['post_id'] : 0;
	$year    = isset( $_GET['jahr'] ) ? sanitize_text_field( wp_unslash( $_GET['jahr'] ) ) : '';
	$page    = isset( $_GET['seite'] ) ? (int) $_GET['seite'] : 1;

	$data = elfzwo_einsatzliste_data( $year, $page );
	$html = elfzwo_einsatzliste_render( $post_id, $data );

	wp_send_json_success(
		array(
			'html'  => $html,
			'jahr'  => $data['selected_year'],
			'seite' => $data['page'],
		)
	);
}
add_action( 'wp_ajax_elfzwo_einsatzliste', 'elfzwo_ajax_einsatzliste' );
add_action( 'wp_ajax_nopriv_elfzwo_einsatzliste', 'elfzwo_ajax_einsatzliste' );
