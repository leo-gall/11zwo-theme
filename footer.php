<?php
// Aufruf zum Mitmachen im Footer — nicht auf der Mitmachen-Seite selbst.
$elfzwo_footer_cta = ! ( is_singular() && has_block( 'elfzwo/mitmachen-form' ) );
?>
<footer class="relative mt-16 text-white/85 md:mt-20">
	<?php // Geschwungene Oberkante und freigestelltes HLF nach dem Vorbild der Aicher Ambulanz. ?>
	<svg class="block h-16 w-full text-signal md:h-28" viewBox="0 0 1440 120" preserveAspectRatio="none" aria-hidden="true"><path fill="currentColor" d="M0 90 C 420 20, 980 0, 1440 50 L1440 120 L0 120 Z"/></svg>
	<div class="bg-gradient-to-br from-signal via-signal to-wood">
		<?php if ( $elfzwo_footer_cta ) : ?>
			<div class="relative mx-auto grid max-w-7xl items-end gap-8 px-5 pb-12 md:grid-cols-[1fr_1fr] md:px-8">
				<div class="pt-2 md:pb-10">
					<h2 class="font-display text-3xl text-white md:text-5xl">Werde Teil der Mannschaft!</h2>
					<p class="mt-4 max-w-lg text-lg text-white/90">Ob Quereinsteiger, Jugendliche oder Fördermitglied – wir freuen uns über alle, die mit anpacken.</p>
					<a href="<?php echo esc_url( home_url( '/mitmachen/' ) ); ?>" class="elfzwo-btn elfzwo-btn-outline-light mt-6">Mach mit</a>
				</div>
				<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/footer-hlf.png' ); ?>" alt="Hilfeleistungslöschfahrzeug der Feuerwehr Greifenberg" loading="lazy" width="760" height="590" class="mx-auto -mt-4 w-full max-w-md drop-shadow-2xl md:-mt-44 md:max-w-lg md:justify-self-end">
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
