<?php
/**
 * Kontakt-Sektion ohne Formular — Gegenstück zum Mach-mit-Formular mit
 * derselben Überschrift. Gerätehaus-Daten und Ansprechpartner kommen aus
 * den Footer-Einstellungen, damit sie nur an einer Stelle gepflegt werden.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
$kicker      = $attributes['kicker'] ?? '';
$title       = $attributes['title'] ?? '';
$title_hand  = $attributes['titleHand'] ?? '';
$description = $attributes['description'] ?? '';

$strasse  = elfzwo_option( 'elfzwo_geraetehaus_strasse', '' );
$plz_ort  = elfzwo_option( 'elfzwo_geraetehaus_plz_ort', '' );
$zeiten   = elfzwo_option( 'elfzwo_geraetehaus_zeiten', '' );
$zeiten_2 = elfzwo_option( 'elfzwo_geraetehaus_zeiten_2', '' );
$email    = elfzwo_option( 'elfzwo_kontakt_email', '' );
$maps_url = $strasse ? 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode( trim( $strasse . ', ' . $plz_ort ) ) : '';

$show_geraetehaus = ! empty( $attributes['showGeraetehaus'] ) && ( $strasse || $zeiten || $email );
$personen         = ! empty( $attributes['showPersonen'] ) ? array_values( array_filter( elfzwo_footer_personen(), function ( $person ) {
	return ! empty( $person['name'] );
} ) ) : array();
?>
<section class="mx-auto max-w-5xl px-5 pt-10 pb-16 md:px-8 md:pt-16 lg:pb-20">

	<div class="text-center">
		<?php if ( $kicker ) : ?><p class="font-hand text-2xl text-primary"><?php echo esc_html( $kicker ); ?></p><?php endif; ?>
		<?php if ( $title || $title_hand ) : ?>
			<h1 class="mt-1 text-4xl font-bold leading-tight tracking-tight md:text-5xl">
				<?php echo esc_html( $title ); ?>
				<?php if ( $title_hand ) : ?><span class="text-signal"><?php echo esc_html( $title_hand ); ?></span><?php endif; ?>
			</h1>
		<?php endif; ?>
		<?php if ( $description ) : ?><p class="mx-auto mt-4 max-w-xl text-lg leading-relaxed text-muted-foreground"><?php echo esc_html( $description ); ?></p><?php endif; ?>
	</div>

	<div class="mt-8 grid gap-5 md:mt-10 <?php echo $show_geraetehaus && $personen ? 'md:grid-cols-[2fr_3fr]' : 'mx-auto max-w-2xl'; ?>">
		<?php if ( $show_geraetehaus ) : ?>
			<div class="rounded-[2rem] border border-border bg-card p-6 md:p-8">
				<p class="text-xs font-bold uppercase tracking-[0.18em] text-ember">Gerätehaus</p>
				<ul class="mt-5 space-y-5">
					<?php if ( $strasse || $plz_ort ) : ?>
						<li class="flex gap-4">
							<span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-signal/15 text-signal"><?php echo elfzwo_icon( 'map-pin', 'h-5 w-5' ); ?></span>
							<span class="pt-0.5">
								<span class="block font-semibold"><?php echo esc_html( $strasse ); ?></span>
								<span class="block text-muted-foreground"><?php echo esc_html( $plz_ort ); ?></span>
								<?php if ( $maps_url ) : ?><a href="<?php echo esc_url( $maps_url ); ?>" target="_blank" rel="noopener" class="mt-1 inline-block text-sm font-semibold text-signal hover:underline">Route planen</a><?php endif; ?>
							</span>
						</li>
					<?php endif; ?>
					<?php if ( $zeiten ) : ?>
						<li class="flex gap-4">
							<span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-signal/15 text-signal"><?php echo elfzwo_icon( 'clock', 'h-5 w-5' ); ?></span>
							<span class="pt-0.5">
								<span class="block font-semibold"><?php echo esc_html( $zeiten ); ?></span>
								<?php if ( $zeiten_2 ) : ?><span class="block text-muted-foreground"><?php echo esc_html( $zeiten_2 ); ?></span><?php endif; ?>
							</span>
						</li>
					<?php endif; ?>
					<?php if ( $email ) : ?>
						<li class="flex gap-4">
							<span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-signal/15 text-signal"><?php echo elfzwo_icon( 'mail', 'h-5 w-5' ); ?></span>
							<a href="mailto:<?php echo esc_attr( $email ); ?>" class="min-w-0 self-center break-words font-semibold hover:text-signal"><?php echo elfzwo_wbr_email( $email ); // phpcs:ignore -- bereits escaped ?></a>
						</li>
					<?php endif; ?>
				</ul>
			</div>
		<?php endif; ?>

		<?php if ( $personen ) : ?>
			<div class="rounded-[2rem] border border-border bg-card p-6 md:p-8">
				<p class="text-xs font-bold uppercase tracking-[0.18em] text-ember">Ansprechpartner</p>
				<div class="mt-5 divide-y divide-border">
					<?php foreach ( $personen as $person ) : ?>
						<div class="py-4 first:pt-0 last:pb-0">
							<p class="font-display text-lg leading-tight"><?php echo esc_html( $person['name'] ); ?></p>
							<?php if ( ! empty( $person['rolle'] ) ) : ?><p class="mt-0.5 text-xs uppercase tracking-widest text-muted-foreground"><?php echo esc_html( $person['rolle'] ); ?></p><?php endif; ?>
							<?php if ( ! empty( $person['telefon'] ) || ! empty( $person['email'] ) ) : ?>
								<div class="mt-3 flex flex-wrap gap-2">
									<?php if ( ! empty( $person['telefon'] ) ) : ?>
										<a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $person['telefon'] ) ); ?>" class="elfzwo-btn elfzwo-btn-secondary elfzwo-btn-sm whitespace-nowrap"><?php echo elfzwo_icon( 'phone', 'h-4 w-4 shrink-0' ); ?> <?php echo esc_html( $person['telefon'] ); ?></a>
									<?php endif; ?>
									<?php if ( ! empty( $person['email'] ) ) : ?>
										<a href="mailto:<?php echo esc_attr( $person['email'] ); ?>" class="elfzwo-btn elfzwo-btn-secondary elfzwo-btn-sm max-w-full"><?php echo elfzwo_icon( 'mail', 'h-4 w-4 shrink-0' ); ?> <span class="truncate"><?php echo esc_html( $person['email'] ); ?></span></a>
									<?php endif; ?>
								</div>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>
	</div>

	<?php if ( ! empty( $attributes['showNotruf'] ) ) : ?>
		<p class="mt-6 flex items-center justify-center gap-2 text-center text-sm text-muted-foreground">
			<?php echo elfzwo_icon( 'siren', 'h-4 w-4 shrink-0 text-signal' ); ?>
			Im Notfall immer direkt die <a href="tel:112" class="font-semibold text-signal hover:underline">112</a> wählen.
		</p>
	<?php endif; ?>

</section>
