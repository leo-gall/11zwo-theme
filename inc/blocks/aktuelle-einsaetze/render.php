<?php
/**
 * Einsätze (Seite /einsaetze/): Verteilung nach Einsatzart und Tabelle eines
 * Jahres (inc/einsatzliste.php). Jahreswechsel und Filter laufen ohne
 * Neuladen über assets/js/einsaetze.js; ohne JavaScript funktionieren die
 * Jahres-Links als normale Links.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

$jahr = isset( $_GET['einsatz_jahr'] ) ? (int) $_GET['einsatz_jahr'] : 0;
?>
<section id="einsatzliste" class="mx-auto max-w-7xl scroll-mt-28 px-5 py-12 md:px-8 md:py-16" data-einsaetze>
	<div class="transition-opacity" data-einsaetze-inhalt aria-live="polite">
		<?php echo elfzwo_einsaetze_ansicht( $jahr, get_permalink() ); // phpcs:ignore -- bereits escaped ?>
	</div>
</section>
