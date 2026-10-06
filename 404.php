<?php
/**
 * Seite nicht gefunden: Kopfbereich wie auf den übrigen Seiten, eine Suche
 * und Wegweiser zu den wichtigsten Bereichen.
 */

get_header();

$elfzwo_wegweiser = array(
	array( 'icon' => 'flame', 'titel' => 'Startseite', 'text' => 'Zurück zum Anfang', 'url' => home_url( '/' ) ),
	array( 'icon' => 'siren', 'titel' => 'Einsätze', 'text' => 'Alle Einsätze mit Berichten', 'url' => home_url( '/einsaetze/' ) ),
	array( 'icon' => 'users', 'titel' => 'Verein', 'text' => 'Aktuelles aus dem Vereinsleben', 'url' => home_url( '/verein/' ) ),
	array( 'icon' => 'heart-handshake', 'titel' => 'Mach mit!', 'text' => 'Werde Teil der Mannschaft', 'url' => home_url( '/mitmachen/' ) ),
);
?>

<main>
	<?php
	echo elfzwo_render_jumbotron( // phpcs:ignore -- bereits escaped
		array(
			'titel'      => 'Seite nicht gefunden',
			'untertitel' => 'Fehler 404',
			'text'       => 'Hier gibt es nichts zu löschen: Die Seite wurde verschoben, umbenannt oder hat nie existiert.',
		)
	);
	?>

	<section class="mx-auto max-w-7xl px-5 py-12 md:px-8 md:py-16">
		<div class="max-w-2xl">
			<h2 class="font-display text-3xl font-black text-signal md:text-4xl">Wonach suchst du?</h2>
			<p class="mt-3 text-foreground/80">Vielleicht hilft die Suche weiter, oder du nimmst einen der Wege unten.</p>
			<form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="mt-6 flex">
				<label class="sr-only" for="elfzwo-404-suche">Suchbegriff</label>
				<input id="elfzwo-404-suche" type="search" name="s" placeholder="z. B. Jugendfeuerwehr, Übungsplan …" class="min-w-0 flex-1 border-2 border-r-0 border-border bg-background px-4 py-3 text-base outline-none focus:border-signal">
				<button type="submit" class="elfzwo-btn elfzwo-btn-primary">Suchen</button>
			</form>
		</div>

		<ul class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
			<?php foreach ( $elfzwo_wegweiser as $elfzwo_weg ) : ?>
				<li>
					<a href="<?php echo esc_url( $elfzwo_weg['url'] ); ?>" class="group flex h-full flex-col border border-border bg-card p-6 transition-colors hover:border-signal">
						<span class="text-signal"><?php echo elfzwo_icon( $elfzwo_weg['icon'], 'h-8 w-8' ); ?></span>
						<span class="mt-4 font-display text-xl font-bold group-hover:text-signal"><?php echo esc_html( $elfzwo_weg['titel'] ); ?></span>
						<span class="mt-1 text-smoke"><?php echo esc_html( $elfzwo_weg['text'] ); ?></span>
						<span class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-signal">Hierher <?php echo elfzwo_icon( 'arrow-right', 'h-4 w-4 transition-transform group-hover:translate-x-1' ); ?></span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>

		<p class="mt-12 text-smoke">Im Notfall gilt immer: <a href="tel:112" class="font-bold text-signal hover:underline">Notruf 112</a>.</p>
	</section>
</main>

<?php get_footer(); ?>
