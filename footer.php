<?php
// Aufruf zum Mitmachen im Footer: nicht auf Seiten mit dem Formular selbst (Startseite)
// und nicht auf reinen Info-Seiten (Downloads, Impressum, Datenschutz).
$elfzwo_footer_cta = ! is_404() && ! ( is_singular() && has_block( 'elfzwo/mitmachen-form' ) ) && ! is_page( array( 'downloads', 'impressum', 'datenschutzerklarung', 'datenschutzerklaerung' ) );
// Passende Vorauswahl im Formular je nach Seite.
$elfzwo_footer_cta_url = elfzwo_mitmachen_url( is_page( array( 'jugend', 'jugendfeuerwehr' ) ) ? 'jugend' : ( is_page( 'verein' ) ? 'verein' : ( is_page( 'mannschaft' ) ? 'aktive' : '' ) ) );
?>
<footer class="relative <?php echo $elfzwo_footer_cta ? 'mt-16 md:mt-20' : ''; ?> text-white/80">
	<?php if ( $elfzwo_footer_cta ) : ?>
		<div class="bg-signal">
			<div class="mx-auto max-w-7xl px-5 py-14 md:px-8 md:py-16">
				<div>
					<h2 class="font-display text-3xl text-white md:text-5xl">Werde Teil der Mannschaft!</h2>
					<p class="mt-4 max-w-lg text-lg text-white/90">Werde Teil der Feuerwehr Greifenberg und unterstütze dein Dorf in der aktiven Mannschaft, der Jugend oder als Fördermitglied.</p>
					<a href="<?php echo esc_url( $elfzwo_footer_cta_url ); ?>" class="elfzwo-btn elfzwo-btn-outline-light mt-6">Mach mit!</a>
				</div>
			</div>
		</div>
	<?php endif; ?>
	<div class="bg-signal">
		<div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-4 <?php echo $elfzwo_footer_cta || is_404() ? 'border-t border-white/25' : ''; ?> px-5 py-6 text-sm md:px-8">
			<span class="flex flex-wrap items-center gap-x-5 gap-y-1">
				<span>© <?php echo esc_html( gmdate( 'Y' ) ); ?> Freiwillige Feuerwehr Greifenberg e.V.</span>
			</span>
			<span class="flex items-center gap-5">
				<a href="<?php echo esc_url( home_url( '/impressum/' ) ); ?>" class="hover:text-white" title="Impressum und Lizenzen">Impressum</a>
				<a href="<?php echo esc_url( home_url( '/datenschutzerklaerung/' ) ); ?>" class="hover:text-white">Datenschutz</a>
				<a href="<?php echo esc_url( home_url( '/rss.xml' ) ); ?>" class="hover:text-white" title="Einsätze und Beiträge als RSS-Feed abonnieren" aria-label="RSS-Feed"><?php echo elfzwo_icon( 'rss', 'h-4 w-4' ); ?></a>
			</span>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
