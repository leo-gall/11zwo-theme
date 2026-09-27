<?php
/**
 * Nav-Walker für die (flachen) Footer-Menüs. Die Hauptnavigation mit ihrem
 * Dropdown wird direkt im Template über elfzwo_get_menu_tree() gerendert
 * (siehe inc/helpers.php) statt über einen Walker mit start_lvl/end_lvl —
 * das war fehleranfällig, weil die genaue Aufruf-Reihenfolge von
 * end_lvl()/end_el() bei verschachtelten Items leicht zu doppelten oder
 * falsch platzierten schließenden Tags führt.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ELFZWO_Footer_Nav_Walker extends Walker_Nav_Menu {

	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$output .= '<li><a href="' . esc_url( $item->url ) . '" class="inline-flex items-center gap-2 text-foreground/80 transition-colors hover:text-signal">'
			. elfzwo_icon( 'arrow-right', 'h-3 w-3 text-ember' )
			. esc_html( $item->title )
			. '</a></li>';
	}
}
