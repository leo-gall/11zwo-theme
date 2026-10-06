<?php
/**
 * Abschnitt "Lizenzen" am Ende der Impressum-Seite: alle Fremdbestandteile der
 * Website (elfzwo_lizenzen()) mit Lizenz und Quelle.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function elfzwo_lizenzen_abschnitt() {
	ob_start();
	?>
	<section id="lizenzen" class="mx-auto max-w-3xl scroll-mt-28 px-5 pb-16 md:px-8">
		<h2 class="font-display text-3xl font-black text-signal md:text-4xl">Lizenzen</h2>
		<p class="mt-3 text-foreground/80">Diese Website nutzt freie Software, Schriften und Grafiken.</p>
<?php foreach ( elfzwo_lizenzen() as $gruppe => $eintraege ) : ?>
			<h3 class="mt-10 font-display text-xl font-bold"><?php echo esc_html( $gruppe ); ?></h3>
			<ul class="mt-4 border-t border-border">
				<?php foreach ( $eintraege as $e ) : ?>
					<li class="border-b border-border py-4">
						<p class="flex flex-wrap items-baseline justify-between gap-x-4">
							<a href="<?php echo esc_url( $e['url'] ); ?>" class="font-bold hover:text-signal hover:underline" rel="noopener"><?php echo esc_html( $e['name'] ); ?></a>
							<a href="<?php echo esc_url( $e['lizenz_url'] ); ?>" class="text-sm font-semibold text-signal hover:underline" rel="noopener"><?php echo esc_html( $e['lizenz'] ); ?></a>
						</p>
						<?php if ( ! empty( $e['hinweis'] ) ) : ?><p class="mt-1 text-sm text-smoke"><?php echo esc_html( $e['hinweis'] ); ?></p><?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endforeach; ?>
	</section>
	<?php
	return ob_get_clean();
}
