<?php
$footer_persons = array();
for ( $i = 1; $i <= 3; $i++ ) {
	$name = elfzwo_option( "elfzwo_footer_person{$i}_name", '' );
	if ( '' === $name ) {
		continue;
	}
	$footer_persons[] = array(
		'name'    => $name,
		'rolle'   => elfzwo_option( "elfzwo_footer_person{$i}_rolle", '' ),
		'telefon' => elfzwo_option( "elfzwo_footer_person{$i}_telefon", '' ),
		'email'   => elfzwo_option( "elfzwo_footer_person{$i}_email", '' ),
	);
}
?>
<footer class="mt-24 border-t border-border bg-background">
	<!-- Notruf-Karte -->
	<div class="mx-auto max-w-7xl px-5 pt-12 md:px-8">
		<div class="flex flex-wrap items-center justify-between gap-6 rounded-[2rem] bg-signal p-6 text-signal-foreground md:p-8">
			<div class="flex items-center gap-4">
				<span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-signal-foreground/30">
					<?php echo elfzwo_icon( 'siren', 'h-6 w-6', 2.4 ); ?>
				</span>
				<div>
					<p class="text-[11px] font-bold uppercase tracking-[0.2em] text-signal-foreground/75">Im Notfall</p>
					<p class="font-display text-3xl leading-none">Notruf 112</p>
				</div>
			</div>
			<p class="max-w-md text-sm text-signal-foreground/85">
				Ein Notruf kostet nichts — außer der Sekunde, die ihr euch nehmt. Wählt bei Feuer, Unfall oder Verletzung sofort die 112.
			</p>
		</div>
	</div>

	<div class="mx-auto grid max-w-7xl gap-12 px-5 py-16 md:px-8 lg:grid-cols-[1.1fr_0.8fr_1.15fr_1.35fr]">
		<!-- Brand -->
		<div>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="group flex items-center gap-3">
				<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/logo.png' ); ?>" alt="Freiwillige Feuerwehr Greifenberg" class="h-16 w-auto transition-transform duration-200 group-hover:scale-105">
				<span class="flex flex-col leading-tight">
					<span class="font-display text-lg font-semibold text-foreground">Feuerwehr Greifenberg</span>
					<span class="text-[11px] uppercase tracking-[0.18em] text-muted-foreground">Seit <?php echo esc_html( elfzwo_option( 'elfzwo_gegruendet', '1899' ) ); ?></span>
				</span>
			</a>

			<p class="mt-6 max-w-sm text-sm text-muted-foreground">
				<?php echo esc_html( elfzwo_option( 'elfzwo_footer_text', 'Nachbarn, die füreinander da sind. 39 Aktive, 19 in der Jugendfeuerwehr — und im Ernstfall rund um die Uhr für Greifenberg, Neugreifenberg, Beuern, Painhofen und die A96 unterwegs.' ) ); ?>
			</p>

			<p class="mt-6 font-hand text-3xl text-signal"><?php echo esc_html( elfzwo_option( 'elfzwo_footer_claim', 'Nicht ohne dich!' ) ); ?></p>
		</div>

		<!-- Über uns + Einsatz -->
		<div>
			<p class="text-xs font-bold uppercase tracking-[0.18em] text-ember">Über uns</p>
			<ul class="mt-5 space-y-2.5 text-sm">
				<?php
				if ( has_nav_menu( 'footer-ueber-uns' ) ) {
					wp_nav_menu(
						array(
							'theme_location' => 'footer-ueber-uns',
							'container'      => false,
							'items_wrap'     => '%3$s',
							'walker'         => new ELFZWO_Footer_Nav_Walker(),
						)
					);
				}
				?>
			</ul>

			<p class="mt-8 text-xs font-bold uppercase tracking-[0.18em] text-ember">Einsatz</p>
			<ul class="mt-5 space-y-2.5 text-sm">
				<?php
				if ( has_nav_menu( 'footer-einsatz' ) ) {
					wp_nav_menu(
						array(
							'theme_location' => 'footer-einsatz',
							'container'      => false,
							'items_wrap'     => '%3$s',
							'walker'         => new ELFZWO_Footer_Nav_Walker(),
						)
					);
				}
				?>
			</ul>
		</div>

		<!-- Kontakt -->
		<div>
			<p class="text-xs font-bold uppercase tracking-[0.18em] text-ember">Gerätehaus</p>
			<ul class="mt-5 space-y-4 text-sm text-foreground/80">
				<li class="flex gap-3">
					<?php echo elfzwo_icon( 'map-pin', 'mt-0.5 h-4 w-4 shrink-0 text-signal' ); ?>
					<span><?php echo esc_html( elfzwo_option( 'elfzwo_geraetehaus_strasse', 'Lindenweg 12' ) ); ?><br><?php echo esc_html( elfzwo_option( 'elfzwo_geraetehaus_plz_ort', '86926 Greifenberg' ) ); ?></span>
				</li>
				<li class="flex gap-3">
					<?php echo elfzwo_icon( 'clock', 'mt-0.5 h-4 w-4 shrink-0 text-signal' ); ?>
					<span><?php echo esc_html( elfzwo_option( 'elfzwo_geraetehaus_zeiten', 'Sa 11:00 – 12:30 Uhr' ) ); ?><br><span class="text-muted-foreground"><?php echo esc_html( elfzwo_option( 'elfzwo_geraetehaus_zeiten_2', 'Kameraden vor Ort' ) ); ?></span></span>
				</li>
				<li class="flex gap-3">
					<?php echo elfzwo_icon( 'mail', 'mt-0.5 h-4 w-4 shrink-0 text-signal' ); ?>
					<?php $kontakt_email = elfzwo_option( 'elfzwo_kontakt_email', 'feuerwehr@greifenberg-ammersee.de' ); ?>
					<a href="mailto:<?php echo esc_attr( $kontakt_email ); ?>" class="break-words text-xs transition-colors hover:text-signal"><?php echo elfzwo_wbr_email( $kontakt_email ); // phpcs:ignore -- bereits escaped ?></a>
				</li>
			</ul>
		</div>

		<!-- Ansprechpartner -->
		<div>
			<p class="text-xs font-bold uppercase tracking-[0.18em] text-ember">Ansprechpartner</p>
			<div class="mt-5 space-y-5 text-sm">
				<?php foreach ( $footer_persons as $person ) :
					$rolle = $person['rolle'];
					$tel   = $person['telefon'];
					$email = $person['email'];
					?>
					<div>
						<p class="font-display text-base leading-tight"><?php echo esc_html( $person['name'] ); ?></p>
						<?php if ( $rolle ) : ?><p class="mt-0.5 text-xs uppercase tracking-widest text-muted-foreground"><?php echo esc_html( $rolle ); ?></p><?php endif; ?>
						<?php if ( $tel ) : ?>
							<a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $tel ) ); ?>" class="mt-1.5 flex items-center gap-2 text-xs text-foreground/80 transition-colors hover:text-signal"><?php echo elfzwo_icon( 'phone', 'h-3.5 w-3.5 shrink-0 text-signal' ); ?> <?php echo esc_html( $tel ); ?></a>
						<?php endif; ?>
						<?php if ( $email ) : ?>
							<a href="mailto:<?php echo esc_attr( $email ); ?>" class="mt-1.5 flex items-start gap-2 break-words text-xs text-foreground/80 transition-colors hover:text-signal"><?php echo elfzwo_icon( 'mail', 'mt-0.5 h-3.5 w-3.5 shrink-0 text-signal' ); ?> <span><?php echo elfzwo_wbr_email( $email ); // phpcs:ignore -- bereits escaped ?></span></a>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>

	<div class="border-t border-border">
		<div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-3 px-5 py-6 text-xs text-muted-foreground md:px-8">
			<span>© <?php echo esc_html( gmdate( 'Y' ) ); ?> Freiwillige Feuerwehr Greifenberg e.V. · Gemeinnützig · Ehrenamtlich</span>
			<span class="flex items-center gap-4">
				<a href="<?php echo esc_url( home_url( '/impressum/' ) ); ?>" class="transition-colors hover:text-signal">Impressum</a>
				<a href="<?php echo esc_url( home_url( '/datenschutzerklarung/' ) ); ?>" class="transition-colors hover:text-signal">Datenschutz</a>
				<a href="<?php echo esc_url( home_url( '/comments/feed/' ) ); ?>" class="inline-flex items-center gap-1.5 transition-colors hover:text-signal" title="Kommentare als RSS-Feed abonnieren"><?php echo elfzwo_icon( 'rss', 'h-3.5 w-3.5' ); ?> RSS</a>
			</span>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
