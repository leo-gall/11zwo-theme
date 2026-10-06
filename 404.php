<?php
/**
 * Seite nicht gefunden: Kopfbereich wie auf den übrigen Seiten, Suchfeld mit
 * Vorschlägen und, falls die aufgerufene Adresse etwas hergibt, passende
 * Seiten zu ihren Wörtern.
 */

get_header();

$elfzwo_woerter = trim( preg_replace( '/[^\p{L}\p{N}]+/u', ' ', urldecode( (string) wp_parse_url( $_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH ) ) ) );
$elfzwo_treffer = array();
if ( mb_strlen( $elfzwo_woerter ) >= 3 ) {
	$elfzwo_treffer = get_posts(
		array(
			's'              => $elfzwo_woerter,
			'post_type'      => array( 'page', 'post', 'einsatz', 'download', 'fahrzeug' ),
			'posts_per_page' => 4,
		)
	);
}
?>

<main>
	<?php
	echo elfzwo_render_jumbotron( // phpcs:ignore -- bereits escaped
		array(
			'titel'      => 'Seite nicht gefunden',
			'untertitel' => 'Fehler 404',
		)
	);
	?>
	<section class="mx-auto flex min-h-[calc(100vh-26rem)] max-w-3xl flex-col items-center justify-center px-5 py-16 text-center md:px-8">
		<p class="text-lg text-foreground/80">Diese Seite gibt es nicht oder nicht mehr. Vielleicht findest du sie über die Suche.</p>
		<div class="mt-8 w-full"><?php echo elfzwo_suchfeld(); // phpcs:ignore -- bereits escaped ?></div>

		<?php if ( $elfzwo_treffer ) : ?>
			<div class="mt-10 w-full max-w-xl text-left">
				<p class="font-semibold">Meintest du vielleicht:</p>
				<ul class="mt-2 border-t border-border">
					<?php foreach ( $elfzwo_treffer as $elfzwo_eintrag ) : ?>
						<li class="border-b border-border">
							<a href="<?php echo esc_url( get_permalink( $elfzwo_eintrag ) ); ?>" class="flex items-baseline justify-between gap-4 py-3 hover:text-signal">
								<span class="font-semibold"><?php echo esc_html( get_the_title( $elfzwo_eintrag ) ); ?></span>
								<span class="shrink-0 text-sm text-smoke"><?php echo esc_html( elfzwo_typ_name( $elfzwo_eintrag->post_type ) ); ?></span>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>

		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="mt-10 font-semibold text-signal underline underline-offset-4 hover:text-wood">Zur Startseite</a>
	</section>
</main>

<?php get_footer(); ?>
