<?php
/**
 * Startseite: links die neuesten Beiträge (ein Aufmacher, zwei kurze),
 * rechts die letzten Einsätze als knappe Liste — alles in einer Zeile.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
$anzahl   = max( 3, min( 10, (int) ( $attributes['anzahlEinsaetze'] ?? 6 ) ) );
$link_url = $attributes['linkUrl'] ?? '/einsaetze/';
$teil     = $attributes['teil'] ?? 'beide';

$beitraege = get_posts( array( 'post_type' => 'post', 'numberposts' => 'neuigkeiten' === $teil ? 4 : 3, 'post_status' => 'publish' ) );
$aufmacher = array_shift( $beitraege );

$einsaetze = get_posts( array( 'post_type' => 'einsatz', 'numberposts' => -1, 'post_status' => 'publish' ) );
usort(
	$einsaetze,
	function ( $a, $b ) {
		return strcmp( elfzwo_meta( $b->ID, 'datum', '' ), elfzwo_meta( $a->ID, 'datum', '' ) );
	}
);
$einsaetze = array_slice( $einsaetze, 0, $anzahl );

if ( 'neuigkeiten' === $teil ) :
	// Die vier neuesten Beiträge als Karten (Vorbild: Feuerwehr Gilching).
	?>
	<section class="mx-auto max-w-7xl px-5 py-12 md:px-8 md:py-16">
		<div class="flex items-end justify-between gap-4">
			<div>
				<h2 class="font-display text-3xl font-bold text-signal md:text-4xl">Neuigkeiten aus der Feuerwehr</h2>
			</div>
			<a href="<?php echo esc_url( $link_url ); ?>" class="shrink-0 text-sm font-semibold text-signal hover:underline">Alle Neuigkeiten</a>
		</div>
		<div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
			<?php foreach ( array_filter( array_merge( array( $aufmacher ), $beitraege ) ) as $beitrag ) :
				$bild = elfzwo_post_cover_image_url( $beitrag->ID, 'medium_large' );
				?>
				<a href="<?php echo esc_url( get_permalink( $beitrag ) ); ?>" class="group flex flex-col overflow-hidden border border-border bg-card transition-colors hover:border-signal">
					<?php if ( $bild ) : ?><img src="<?php echo esc_url( $bild ); ?>" alt="" loading="lazy" class="aspect-[16/10] w-full object-cover"><?php endif; ?>
					<div class="flex flex-1 flex-col p-5">
						<h3 class="font-display text-lg font-semibold leading-snug group-hover:text-signal"><?php echo esc_html( get_the_title( $beitrag ) ); ?></h3>
						<p class="mt-2 flex-1 text-sm font-light text-foreground/75"><?php echo esc_html( elfzwo_excerpt( $beitrag->ID, 18 ) ); ?></p>
						<span class="mt-4 text-sm font-semibold text-signal">Weiterlesen</span>
					</div>
					<div class="border-t border-border px-5 py-2.5 text-xs text-smoke"><?php echo esc_html( get_the_date( 'j. F Y', $beitrag ) ); ?></div>
				</a>
			<?php endforeach; ?>
		</div>
	</section>
	<?php
	return;
endif;

if ( 'einsaetze' === $teil ) :
	// Dunkles Band über die volle Breite.
	?>
	<section class="bg-ink py-12 text-white md:py-14">
		<div class="mx-auto grid max-w-7xl gap-8 px-5 md:px-8 lg:grid-cols-[1fr_3fr]">
			<div>
				<p class="elfzwo-kicker !text-white/80">Einsätze</p>
				<h2 class="mt-1 font-display text-3xl md:text-4xl">Letzte Einsätze</h2>
				<a href="<?php echo esc_url( $link_url ); ?>" class="mt-4 inline-block text-sm font-semibold text-white/80 hover:text-white hover:underline">Alle Einsätze</a>
			</div>
			<?php if ( ! $einsaetze ) : ?>
				<p class="text-white/70">Noch keine Einsätze eingetragen.</p>
			<?php else : ?>
				<ul class="grid gap-x-8 sm:grid-cols-2 lg:grid-cols-3">
					<?php foreach ( $einsaetze as $einsatz ) :
						$datum = elfzwo_meta( $einsatz->ID, 'datum', '' );
						$ts    = $datum ? strtotime( $datum ) : false;
						$ort   = elfzwo_einsatzort_name( $einsatz->ID );
						?>
						<li class="border-t border-white/15">
							<a href="<?php echo esc_url( get_permalink( $einsatz ) ); ?>" class="group block py-4">
								<span class="text-sm font-bold text-signal"><?php echo $ts ? esc_html( date_i18n( 'j. F Y', $ts ) ) : ''; ?></span>
								<span class="mt-1 block font-semibold group-hover:underline"><?php echo esc_html( get_the_title( $einsatz ) ); ?></span>
								<?php if ( $ort ) : ?><span class="block text-sm text-white/60"><?php echo esc_html( $ort ); ?></span><?php endif; ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
	</section>
	<?php
	return;
endif;
?>
<section class="mx-auto max-w-7xl px-5 py-12 md:px-8">
	<div class="grid gap-10 lg:grid-cols-[1.75fr_1fr]">
		<div>
			<div class="flex items-end justify-between gap-4">
				<div>
					<p class="elfzwo-kicker">Aktuelles</p>
					<h2 class="mt-1 font-display text-3xl md:text-4xl">Neuigkeiten</h2>
				</div>
				<a href="<?php echo esc_url( $link_url ); ?>" class="shrink-0 text-sm font-semibold text-signal hover:underline">Alle Neuigkeiten</a>
			</div>

			<?php if ( ! $aufmacher ) : ?>
				<p class="mt-6 text-smoke">Noch keine Neuigkeiten.</p>
			<?php else :
				$bild = elfzwo_post_cover_image_url( $aufmacher->ID, 'large' );
				?>
				<a href="<?php echo esc_url( get_permalink( $aufmacher ) ); ?>" class="group mt-6 grid gap-5 md:grid-cols-[1.2fr_1fr] md:items-center">
					<?php if ( $bild ) : ?><img src="<?php echo esc_url( $bild ); ?>" alt="" class="aspect-[4/3] w-full object-cover"><?php endif; ?>
					<div class="<?php echo $bild ? '' : 'md:col-span-2'; ?>">
						<p class="text-sm font-semibold text-signal"><?php echo esc_html( elfzwo_post_category_line( $aufmacher->ID ) ); ?></p>
						<h3 class="mt-1 font-display text-2xl leading-tight group-hover:underline"><?php echo esc_html( get_the_title( $aufmacher ) ); ?></h3>
						<p class="mt-2 text-smoke"><?php echo esc_html( elfzwo_excerpt( $aufmacher->ID, 30 ) ); ?></p>
						<p class="mt-3 text-sm text-smoke"><?php echo esc_html( get_the_date( 'd.m.Y', $aufmacher ) ); ?></p>
					</div>
				</a>

				<?php if ( $beitraege ) : ?>
					<div class="mt-6 grid gap-5 border-t border-border pt-6 sm:grid-cols-2">
						<?php foreach ( $beitraege as $beitrag ) :
							$bild = elfzwo_post_cover_image_url( $beitrag->ID, 'thumbnail' );
							?>
							<a href="<?php echo esc_url( get_permalink( $beitrag ) ); ?>" class="group flex gap-4">
								<?php if ( $bild ) : ?><img src="<?php echo esc_url( $bild ); ?>" alt="" loading="lazy" class="h-20 w-24 shrink-0 object-cover"><?php endif; ?>
								<span class="min-w-0">
									<span class="block font-display text-lg leading-snug group-hover:underline"><?php echo esc_html( get_the_title( $beitrag ) ); ?></span>
									<span class="mt-1 block text-sm text-smoke"><?php echo esc_html( get_the_date( 'd.m.Y', $beitrag ) ); ?></span>
								</span>
							</a>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			<?php endif; ?>
		</div>

		<aside class="border border-border bg-card p-6">
			<div class="flex items-end justify-between gap-4">
				<div>
					<p class="elfzwo-kicker">Einsätze</p>
					<h2 class="mt-1 font-display text-3xl">Letzte Einsätze</h2>
				</div>
			</div>
			<?php if ( ! $einsaetze ) : ?>
				<p class="mt-5 text-smoke">Noch keine Einsätze eingetragen.</p>
			<?php else : ?>
				<ul class="mt-5 divide-y divide-border">
					<?php foreach ( $einsaetze as $einsatz ) :
						$datum = elfzwo_meta( $einsatz->ID, 'datum', '' );
						$ts    = $datum ? strtotime( $datum ) : false;
						$ort   = elfzwo_einsatzort_name( $einsatz->ID );
						?>
						<li>
							<a href="<?php echo esc_url( get_permalink( $einsatz ) ); ?>" class="group flex gap-4 py-3">
								<span class="w-12 shrink-0 pt-0.5 text-sm font-bold text-signal"><?php echo $ts ? esc_html( date_i18n( 'd.m.', $ts ) ) : '–'; ?></span>
								<span class="min-w-0">
									<span class="block truncate font-semibold group-hover:underline"><?php echo esc_html( get_the_title( $einsatz ) ); ?></span>
									<?php if ( $ort ) : ?><span class="block truncate text-sm text-smoke"><?php echo esc_html( $ort ); ?></span><?php endif; ?>
								</span>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
			<a href="<?php echo esc_url( $link_url ); ?>" class="mt-4 inline-block text-sm font-semibold text-signal hover:underline">Alle Einsätze</a>
		</aside>
	</div>
</section>
