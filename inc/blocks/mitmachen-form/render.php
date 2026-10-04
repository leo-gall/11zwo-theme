<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$interests   = $attributes['interests'] ?? array();
$kicker      = $attributes['kicker'] ?? '';
$title       = $attributes['title'] ?? '';
$title_hand  = $attributes['titleHand'] ?? '';
$description = $attributes['description'] ?? '';
$steps       = array_values( array_filter( $attributes['steps'] ?? array(), function ( $step ) {
	return ! empty( $step['title'] );
} ) );
$datenschutz_id  = (int) ( $attributes['datenschutzPageId'] ?? 0 );
$datenschutz_url = $datenschutz_id ? get_permalink( $datenschutz_id ) : '';
$datenschutz_url = $datenschutz_url ?: home_url( '/datenschutzerklaerung/' );
$post_id = get_the_ID();
// Vorauswahl per Link, z. B. /mitmachen/?interesse=verein — passt auf das erste Interesse, dessen Name das Stichwort enthält.
$vorauswahl = 0;
$wunsch     = isset( $_GET['interesse'] ) ? sanitize_title( wp_unslash( $_GET['interesse'] ) ) : '';
$stichworte = array( 'verein' => array( 'foerder', 'verein' ), 'aktive' => array( 'aktiv' ), 'jugend' => array( 'jugend' ), 'kinder' => array( 'kinder' ) );
if ( $wunsch ) {
	foreach ( $interests as $i => $interest ) {
		$name = sanitize_title( $interest['label'] ?? '' );
		foreach ( $stichworte[ $wunsch ] ?? array( $wunsch ) as $wort ) {
			if ( false !== strpos( $name, $wort ) ) {
				$vorauswahl = $i;
				break 2;
			}
		}
	}
}
$status  = isset( $_GET['mitmachen'] ) ? sanitize_text_field( wp_unslash( $_GET['mitmachen'] ) ) : '';
?>
<section class="mx-auto max-w-2xl px-5 pt-10 pb-16 md:pt-16 lg:pb-20">

	<?php if ( ! elfzwo_kopf_uebernommen( 'elfzwo/mitmachen-form' ) ) : ?>
	<div class="text-center">
		<?php if ( $kicker ) : ?><p class="elfzwo-kicker"><?php echo esc_html( $kicker ); ?></p><?php endif; ?>
		<h1 class="mt-1 text-4xl font-bold leading-tight tracking-tight md:text-5xl">
			<?php echo esc_html( $title ); ?>
			<?php if ( $title_hand ) : ?><span class="text-signal"><?php echo esc_html( $title_hand ); ?></span><?php endif; ?>
		</h1>
		<?php if ( $description ) : ?><p class="mx-auto mt-4 max-w-xl text-lg leading-relaxed text-smoke"><?php echo esc_html( $description ); ?></p><?php endif; ?>
	</div>
	<?php endif; ?>

	<?php if ( $steps ) : ?>
		<div class="mt-8 md:mt-10">
			<h2 class="text-center text-sm font-semibold text-smoke">So geht's weiter</h2>
			<ol class="mt-4 grid gap-3 <?php echo esc_attr( array( 1 => 'sm:grid-cols-1', 2 => 'sm:grid-cols-2', 3 => 'sm:grid-cols-3' )[ min( count( $steps ), 3 ) ] ); ?>">
				<?php foreach ( $steps as $i => $step ) : ?>
					<li class="flex gap-3 rounded-md bg-ash p-4">
						<span class="w-7 shrink-0 font-display text-2xl font-black leading-none text-signal"><?php echo esc_html( $i + 1 ); ?></span>
						<span class="min-w-0">
							<span class="block font-semibold leading-tight"><?php echo esc_html( $step['title'] ); ?></span>
							<?php if ( ! empty( $step['text'] ) ) : ?><span class="mt-1 block text-sm text-smoke"><?php echo esc_html( $step['text'] ); ?></span><?php endif; ?>
						</span>
					</li>
				<?php endforeach; ?>
			</ol>
		</div>
	<?php endif; ?>

	<div id="formular" class="mt-8 scroll-mt-28 rounded-lg bg-ash md:mt-10">
		<?php if ( 'success' === $status ) : ?>
			<div class="px-6 py-12 text-center md:px-10">
				<span class="mx-auto grid h-16 w-16 place-items-center rounded-full bg-ember text-signal-foreground"><?php echo elfzwo_icon( 'circle-check', 'h-8 w-8' ); ?></span>
				<h2 class="mt-5 font-display text-3xl">Angekommen!</h2>
				<p class="mx-auto mt-3 max-w-md text-smoke">Wir melden uns in den nächsten Tagen.</p>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="elfzwo-btn elfzwo-btn-secondary mt-6">Zur Startseite</a>
			</div>
		<?php else : ?>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="elfzwo-mitmachen-form p-5 sm:p-8">
				<input type="hidden" name="action" value="elfzwo_mitmachen">
				<input type="hidden" name="redirect_id" value="<?php echo esc_attr( $post_id ); ?>">
				<?php wp_nonce_field( 'elfzwo_mitmachen', 'elfzwo_mitmachen_nonce' ); ?>
				<?php elfzwo_mitmachen_spamschutz_felder(); ?>

				<?php if ( 'error' === $status ) : ?>
					<p class="mb-6 flex items-center gap-2 rounded-md border border-leaf/40 bg-leaf/10 px-4 py-3 text-sm font-medium text-leaf"><?php echo elfzwo_icon( 'triangle-alert', 'h-4 w-4 shrink-0' ); ?> Bitte gib deinen Namen und eine gültige E-Mail-Adresse an.</p>
				<?php endif; ?>

				<fieldset>
					<legend class="text-sm font-semibold">Was interessiert dich?</legend>
					<div class="mt-2 grid gap-2 sm:grid-cols-2">
						<?php foreach ( $interests as $i => $interest ) : ?>
							<label class="flex cursor-pointer items-center gap-3 rounded-md border-2 border-border bg-background px-4 py-3 transition hover:border-signal/50 has-[:checked]:border-signal has-[:checked]:bg-signal/5 has-[:focus-visible]:ring-4 has-[:focus-visible]:ring-signal/30">
								<input type="radio" name="interesse" value="<?php echo esc_attr( $interest['label'] ?? '' ); ?>" class="h-4 w-4 shrink-0 accent-[var(--signal)]" <?php checked( $vorauswahl === $i ); ?> required>
								<span class="min-w-0">
									<span class="block font-semibold leading-tight"><?php echo esc_html( $interest['label'] ?? '' ); ?></span>
									<?php if ( ! empty( $interest['hint'] ) ) : ?><span class="block text-sm text-smoke"><?php echo esc_html( $interest['hint'] ); ?></span><?php endif; ?>
								</span>
							</label>
						<?php endforeach; ?>
					</div>
				</fieldset>

				<div class="mt-6 grid gap-4 sm:grid-cols-2">
					<label class="block">
						<span class="text-sm font-semibold">Dein Name</span>
						<input required name="name" autocomplete="name" placeholder="Max Muster" class="mt-1.5 w-full rounded-md border-2 border-border bg-background px-4 py-3 text-base outline-none transition focus:border-signal">
					</label>
					<label class="block">
						<span class="text-sm font-semibold">Deine E-Mail-Adresse</span>
						<input required type="email" name="kontakt" autocomplete="email" placeholder="max@example.de" class="mt-1.5 w-full rounded-md border-2 border-border bg-background px-4 py-3 text-base outline-none transition focus:border-signal">
					</label>
				</div>

				<button type="submit" class="elfzwo-btn elfzwo-btn-primary mt-6 w-full disabled:cursor-wait disabled:opacity-80">
					<span class="elfzwo-submit-idle inline-flex items-center gap-2">Ich bin dabei!</span>
					<span class="elfzwo-submit-busy hidden items-center gap-2"><span class="h-4 w-4 animate-spin rounded-full border-2 border-current border-t-transparent" aria-hidden="true"></span> Wird gesendet …</span>
				</button>
				<p class="mt-3 text-center text-xs text-smoke">Infos zum Umgang mit deinen Daten findest du in unserer <a href="<?php echo esc_url( $datenschutz_url ); ?>" class="underline underline-offset-2 hover:text-signal">Datenschutzerklärung</a>.</p>
			</form>
		<?php endif; ?>
	</div>

</section>
