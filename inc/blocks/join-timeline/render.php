<?php
/**
 * "Warum mitmachen?" als senkrechter Zeitstrahl: Linie mit Punkten, darunter
 * der Button. Ein Schritt ohne Nummer, aber mit Button-Text ist der Aufruf
 * am Ende.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
$steps = $attributes['steps'] ?? array();
$kicker = $attributes['kicker'] ?? '';
$titel  = $attributes['titel'] ?? '';
$intro  = $attributes['intro'] ?? '';
$punkte = array();
$aufruf = null;
foreach ( $steps as $step ) {
	if ( ! empty( $step['buttonText'] ) && empty( $step['number'] ) ) {
		$aufruf = $step;
	} elseif ( ! empty( $step['title'] ) ) {
		$punkte[] = $step;
	}
}
?>
<section class="mx-auto max-w-7xl px-5 py-8 md:px-8 <?php echo $titel ? 'grid gap-10 md:grid-cols-[1fr_1.4fr] md:py-14' : ''; ?>">
	<?php if ( $titel ) : ?>
		<div class="md:sticky md:top-28 md:self-start">
			<?php if ( $kicker ) : ?><p class="elfzwo-kicker"><?php echo esc_html( $kicker ); ?></p><?php endif; ?>
			<h2 class="mt-1 font-display text-4xl md:text-5xl"><?php echo esc_html( $titel ); ?></h2>
			<?php if ( $intro ) : ?><p class="mt-4 max-w-md text-muted-foreground"><?php echo esc_html( $intro ); ?></p><?php endif; ?>
			<?php if ( $aufruf ) : ?>
				<a href="<?php echo esc_url( $aufruf['buttonUrl'] ?: home_url( '/mitmachen/' ) ); ?>" class="elfzwo-btn elfzwo-btn-primary mt-6"><?php echo esc_html( $aufruf['buttonText'] ); ?></a>
				<?php $aufruf = null; ?>
			<?php endif; ?>
		</div>
	<?php endif; ?>
	<ol class="relative max-w-3xl border-l-2 border-signal/30 pl-8">
		<?php foreach ( $punkte as $step ) : ?>
			<li class="relative pb-8 last:pb-0">
				<span class="absolute -left-[2.5625rem] top-1.5 h-4 w-4 rounded-full border-[3px] border-background bg-signal" aria-hidden="true"></span>
				<h3 class="font-display text-xl"><?php echo esc_html( $step['title'] ); ?></h3>
				<?php if ( ! empty( $step['body'] ) ) : ?><p class="mt-1.5 text-muted-foreground"><?php echo esc_html( $step['body'] ); ?></p><?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ol>
	<?php if ( $aufruf ) : ?>
		<div class="mt-8 flex flex-wrap items-center gap-x-5 gap-y-3 sm:pl-10">
			<a href="<?php echo esc_url( $aufruf['buttonUrl'] ?: home_url( '/mitmachen/' ) ); ?>" class="elfzwo-btn elfzwo-btn-primary"><?php echo esc_html( $aufruf['buttonText'] ); ?></a>
			<?php if ( ! empty( $aufruf['title'] ) ) : ?><span class="font-semibold"><?php echo esc_html( $aufruf['title'] ); ?></span><?php endif; ?>
		</div>
	<?php endif; ?>
</section>
