<?php
/**
 * "Aktuelles"-Feed (Blog-Kacheln auf der Einsätze-&-Aktuelles-Seite): Karten-
 * Rendering und WP_Query werden hier zentral gehalten, damit sowohl der
 * normale Block-Render als auch der AJAX-"Mehr anzeigen"-Endpunkt exakt
 * dasselbe Markup erzeugen.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function elfzwo_aktuelles_feed_query( $page ) {
	return new WP_Query(
		array(
			'post_type'      => 'post',
			'posts_per_page' => 6,
			'paged'          => $page,
		)
	);
}

/**
 * Eine Beitragskarte (Startseite "Neuigkeiten" und Verein "Aktuelles"): Foto,
 * Datum, Titel, kurzer Anriss und "Weiterlesen".
 */
function elfzwo_beitrag_karte( $post ) {
	$bild = elfzwo_post_cover_image_url( $post->ID, 'medium_large' );
	ob_start();
	?>
	<article class="group flex flex-col overflow-hidden border border-border bg-card transition-colors hover:border-signal">
		<a href="<?php echo esc_url( get_permalink( $post ) ); ?>" class="flex flex-1 flex-col">
			<?php if ( $bild ) : ?><img src="<?php echo esc_url( $bild ); ?>" alt="" loading="lazy" class="aspect-[16/10] w-full object-cover"><?php endif; ?>
			<span class="flex flex-1 flex-col p-5">
				<span class="text-sm text-smoke"><?php echo esc_html( get_the_date( 'j. F Y', $post ) ); ?></span>
				<span class="mt-1 font-display text-lg font-bold leading-snug group-hover:text-signal"><?php echo esc_html( get_the_title( $post ) ); ?></span>
				<span class="mt-2 flex-1 text-smoke"><?php echo esc_html( elfzwo_excerpt( $post->ID, 18 ) ); ?></span>
				<span class="mt-4 text-sm font-semibold text-signal">Weiterlesen</span>
			</span>
		</a>
	</article>
	<?php
	return ob_get_clean();
}

function elfzwo_render_aktuelles_feed_cards( $query ) {
	$html = '';
	foreach ( $query->posts as $post ) {
		$html .= elfzwo_beitrag_karte( $post );
	}
	return $html;
}

/**
 * AJAX-Endpunkt: liefert die nächste Feed-Seite als HTML-Fragment (nur die
 * <article>-Karten, kein Grid-Wrapper), damit "Mehr anzeigen" die Liste ohne
 * Hard-Reload nach unten hin erweitern kann.
 */
function elfzwo_ajax_aktuelles_feed() {
	$page  = isset( $_GET['seite'] ) ? max( 1, (int) $_GET['seite'] ) : 1;
	$query = elfzwo_aktuelles_feed_query( $page );

	wp_send_json_success(
		array(
			'html'    => elfzwo_render_aktuelles_feed_cards( $query ),
			'hasMore' => $query->max_num_pages > $page,
		)
	);
}
add_action( 'wp_ajax_elfzwo_aktuelles_feed', 'elfzwo_ajax_aktuelles_feed' );
add_action( 'wp_ajax_nopriv_elfzwo_aktuelles_feed', 'elfzwo_ajax_aktuelles_feed' );
