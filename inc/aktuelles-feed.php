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

/** Neuester Beitrag — wird an anderer Stelle als Aufmacher gezeigt, daher aus dem Feed ausgeschlossen. */
function elfzwo_aktuelles_feed_hero_post() {
	$posts = get_posts( array( 'post_type' => 'post', 'posts_per_page' => 1, 'orderby' => 'date', 'order' => 'DESC' ) );
	return $posts ? $posts[0] : null;
}

function elfzwo_aktuelles_feed_query( $page ) {
	$hero_post = elfzwo_aktuelles_feed_hero_post();
	return new WP_Query(
		array(
			'post_type'      => 'post',
			'posts_per_page' => 4,
			'paged'          => $page,
			'post__not_in'   => $hero_post ? array( $hero_post->ID ) : array(),
		)
	);
}

function elfzwo_render_aktuelles_feed_cards( $query ) {
	ob_start();
	while ( $query->have_posts() ) :
		$query->the_post();
		$kategorie_line = elfzwo_post_category_line( get_the_ID() );
		$image          = elfzwo_post_cover_image_url( get_the_ID(), 'large' );
		?>
		<article class="group flex flex-col">
			<a href="<?php the_permalink(); ?>">
				<?php if ( $image ) : ?><img src="<?php echo esc_url( $image ); ?>" alt="<?php the_title_attribute(); ?>" width="1200" height="800" loading="lazy" class="mb-5 aspect-[4/3] w-full rounded-md object-cover"><?php endif; ?>
				<?php if ( $kategorie_line ) : ?><p class="text-xs font-semibold text-signal"><?php echo esc_html( $kategorie_line ); ?></p><?php endif; ?>
				<h3 class="mt-2 font-display text-3xl leading-tight"><?php the_title(); ?></h3>
				<p class="mt-3 text-smoke"><?php echo esc_html( elfzwo_excerpt( get_the_ID(), 55 ) ); ?></p>
				<p class="mt-3 text-xs text-smoke"><?php echo esc_html( get_the_date() ); ?></p>
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
