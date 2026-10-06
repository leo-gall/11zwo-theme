<?php
/**
 * Werdegang als Treppe: jede Stufe eine Karte, nach rechts jeweils etwas
 * höher versetzt (z. B. Kinderfeuerwehr → Jugendfeuerwehr → Einsatzdienst).
 * Die Stufen-Nummer steht groß und blass in der Ecke.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
$stufen = array_values( array_filter( $attributes['stufen'] ?? array(), function ( $s ) { return ! empty( $s['titel'] ); } ) );
$anzahl = count( $stufen );
if ( ! $anzahl ) {
	return;
}
// Treppen-Versatz: die erste Stufe sitzt am tiefsten, die letzte ganz oben.
$versatz = array( 'lg:mt-0', 'lg:mt-10', 'lg:mt-20', 'lg:mt-28' );
$spalten = array( 1 => 'lg:grid-cols-1', 2 => 'lg:grid-cols-2', 3 => 'lg:grid-cols-3', 4 => 'lg:grid-cols-4' );
?>
<section class="mx-auto max-w-7xl px-5 py-10 md:px-8">
	<ol class="grid gap-5 sm:grid-cols-2 <?php echo esc_attr( $spalten[ min( $anzahl, 4 ) ] ); ?> lg:items-start">
		<?php foreach ( $stufen as $i => $stufe ) : ?>
			<li class="relative overflow-hidden border border-border bg-card p-7 <?php echo esc_attr( $versatz[ min( $anzahl - 1 - $i, 3 ) ] ); ?>">
				<span class="pointer-events-none absolute -right-1 -top-4 font-display text-[7rem] font-black leading-none text-signal/10" aria-hidden="true"><?php echo esc_html( $i + 1 ); ?></span>
				<?php if ( ! empty( $stufe['info'] ) ) : ?><p class="relative text-sm font-semibold text-signal"><?php echo esc_html( $stufe['info'] ); ?></p><?php endif; ?>
				<h3 class="relative mt-1 font-display text-2xl font-bold"><?php echo esc_html( $stufe['titel'] ); ?></h3>
				<?php if ( ! empty( $stufe['text'] ) ) : ?><p class="relative mt-3 font-light leading-relaxed text-foreground/80"><?php echo esc_html( $stufe['text'] ); ?></p><?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ol>
	<?php if ( ! empty( $attributes['buttonText'] ) ) : ?>
		<a href="<?php echo esc_url( $attributes['buttonUrl'] ?: elfzwo_mitmachen_url() ); ?>" class="elfzwo-btn elfzwo-btn-primary mt-8"><?php echo esc_html( $attributes['buttonText'] ); ?></a>
	<?php endif; ?>
</section>
