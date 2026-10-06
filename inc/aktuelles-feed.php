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

/** Beiträge als Karten im Stil der übrigen Karten: Foto, Datum, Titel, kurzer Anriss. */
function elfzwo_render_aktuelles_feed_cards( $query ) {
	ob_start();
	while ( $query->have_posts() ) :
		$query->the_post();
		$bild = elfzwo_post_cover_image_url( get_the_ID(), 'medium_large' );
		?>
		<article class="group flex flex-col overflow-hidden border border-border bg-card transition-colors hover:border-signal">
			<a href="<?php the_permalink(); ?>" class="flex flex-1 flex-col">
				<?php if ( $bild ) : ?><img src="<?php echo esc_url( $bild ); ?>" alt="" loading="lazy" class="aspect-[16/10] w-full object-cover"><?php endif; ?>
				<span class="flex flex-1 flex-col p-6">
					<span class="text-sm text-smoke"><?php echo esc_html( get_the_date( 'j. F Y' ) ); ?></span>
					<span class="mt-1 font-display text-xl font-bold leading-snug group-hover:text-signal"><?php the_title(); ?></span>
					<span class="mt-2 text-smoke"><?php echo esc_html( elfzwo_excerpt( get_the_ID(), 18 ) ); ?></span>
				</span>
			</a>
		</article>
		<?php
	endwhile;
	wp_reset_postdata();
	return ob_get_clean();
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
