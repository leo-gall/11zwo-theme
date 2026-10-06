<?php
// Aufruf zum Mitmachen im Footer — nicht auf der Mitmachen-Seite selbst.
// Nicht auf der Mitmachen-Seite selbst und nicht auf reinen Info-Seiten (Downloads, Impressum, Datenschutz).
$elfzwo_footer_cta = ! ( is_singular() && has_block( 'elfzwo/mitmachen-form' ) ) && ! is_page( array( 'downloads', 'impressum', 'datenschutzerklarung', 'datenschutzerklaerung' ) );
// Passende Vorauswahl im Formular je nach Seite.
$elfzwo_footer_cta_url = home_url( '/mitmachen/' );
if ( is_page( 'jugendfeuerwehr' ) ) {
	$elfzwo_footer_cta_url = add_query_arg( 'interesse', 'jugend', $elfzwo_footer_cta_url );
} elseif ( is_page( 'verein' ) ) {
	$elfzwo_footer_cta_url = add_query_arg( 'interesse', 'verein', $elfzwo_footer_cta_url );
} elseif ( is_page( 'mannschaft' ) ) {
	$elfzwo_footer_cta_url = add_query_arg( 'interesse', 'aktive', $elfzwo_footer_cta_url );
}
?>
<footer class="relative mt-16 text-white/85 md:mt-20">
	<div class="bg-gradient-to-br from-signal via-signal to-wood">
		<?php if ( $elfzwo_footer_cta ) : ?>
			<div class="mx-auto max-w-7xl px-5 py-14 md:px-8 md:py-16">
				<div>
					<h2 class="font-display text-3xl text-white md:text-5xl">Werde Teil der Mannschaft!</h2>
					<p class="mt-4 max-w-lg text-lg text-white/90">Ob Quereinsteiger, Jugendliche oder Fördermitglied – wir freuen uns über alle, die mit anpacken.</p>
					<a href="<?php echo esc_url( $elfzwo_footer_cta_url ); ?>" class="elfzwo-btn elfzwo-btn-outline-light mt-6">Mach mit!</a>
				</div>
			</div>
		<?php endif; ?>
		<div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-4 border-t border-white/20 px-5 py-6 text-sm md:px-8">
			<span class="flex flex-wrap items-center gap-x-5 gap-y-1">
				<span>© <?php echo esc_html( gmdate( 'Y' ) ); ?> Freiwillige Feuerwehr Greifenberg e.V.</span>
				<a href="tel:112" class="inline-flex items-center gap-1.5 font-semibold text-white hover:underline"><?php echo elfzwo_icon( 'phone', 'h-3.5 w-3.5' ); ?> Notruf 112</a>
			</span>
			<span class="flex items-center gap-5">
				<a href="<?php echo esc_url( home_url( '/impressum/' ) ); ?>" class="hover:text-white">Impressum</a>
				<a href="<?php echo esc_url( home_url( '/datenschutzerklaerung/' ) ); ?>" class="hover:text-white">Datenschutz</a>
				<a href="<?php echo esc_url( home_url( '/rss.xml' ) ); ?>" class="inline-flex items-center gap-1.5 hover:text-white" title="Einsätze und Beiträge als RSS-Feed abonnieren"><?php echo elfzwo_icon( 'rss', 'h-3.5 w-3.5' ); ?> RSS</a>
			</span>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
