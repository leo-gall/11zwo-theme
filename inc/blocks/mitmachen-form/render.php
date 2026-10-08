<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$interests       = $attributes['interests'] ?? array();
$title           = $attributes['title'] ?? '';
$text            = $attributes['text'] ?? '';
$datenschutz_url = get_privacy_policy_url() ?: home_url( '/datenschutzerklaerung/' );
$post_id         = get_the_ID();
// Vorauswahl per Link, z. B. /?interesse=verein#mitmachen — passt auf das erste Interesse, dessen Name das Stichwort enthält.
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
$status = isset( $_GET['mitmachen'] ) ? sanitize_text_field( wp_unslash( $_GET['mitmachen'] ) ) : '';
$feld   = 'elfzwo-feld mt-2';
?>
<section id="mitmachen" class="scroll-mt-28 bg-signal text-white">
	<div class="mx-auto max-w-7xl px-5 py-14 md:px-8 md:py-20">
	<div class="grid items-center gap-10 lg:grid-cols-[2fr_3fr] lg:gap-16">
		<div>
			<h2 class="font-display text-3xl font-black text-white md:text-5xl"><?php echo esc_html( $title ); ?></h2>
			<p class="mt-4 max-w-md text-lg text-white/90"><?php echo esc_html( $text ); ?></p>
		</div>
		<div id="formular" class="scroll-mt-28">
			<?php if ( 'success' === $status ) : ?>
				<p class="border-l-4 border-white pl-4 text-lg font-semibold">Danke! Wir melden uns in den nächsten Tagen bei dir.</p>
			<?php else : ?>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="elfzwo-mitmachen-form space-y-4">
					<input type="hidden" name="action" value="elfzwo_mitmachen">
					<input type="hidden" name="redirect_id" value="<?php echo esc_attr( $post_id ); ?>">

					<?php if ( 'error' === $status ) : ?>
						<p class="elfzwo-mitmachen-fehler bg-white px-3 py-2 font-semibold text-signal">Das hat nicht geklappt. Bitte versuche es noch einmal.</p>
					<?php endif; ?>

					<div class="grid gap-4 sm:grid-cols-2">
						<label class="block sm:col-span-2">
							<span class="text-sm font-semibold tracking-wide">Ich interessiere mich für</span>
							<select name="interesse" class="<?php echo esc_attr( $feld ); ?>">
								<?php foreach ( $interests as $i => $interest ) : ?>
									<option value="<?php echo esc_attr( $interest['label'] ?? '' ); ?>" <?php selected( $vorauswahl === $i ); ?>><?php echo esc_html( $interest['label'] ?? '' ); ?></option>
								<?php endforeach; ?>
							</select>
						</label>
						<label class="block">
							<span class="text-sm font-semibold tracking-wide">Dein Name</span>
							<input required name="name" autocomplete="name" placeholder="Vor- und Nachname" class="<?php echo esc_attr( $feld ); ?>">
						</label>
						<label class="block">
							<span class="text-sm font-semibold tracking-wide">Deine E-Mail-Adresse</span>
							<input required type="email" name="kontakt" autocomplete="email" placeholder="name@beispiel.de" class="<?php echo esc_attr( $feld ); ?>">
						</label>
					</div>
					<div class="flex flex-col items-start gap-3 pt-2">
						<button type="submit" class="elfzwo-btn elfzwo-btn-light disabled:cursor-wait disabled:opacity-70">Absenden</button>
						<p class="elfzwo-mitmachen-hinweis text-sm text-white/80">Mit dem Absenden dieses Formulars wird unsere <a href="<?php echo esc_url( $datenschutz_url ); ?>" class="underline underline-offset-2 hover:text-white">Datenschutzerklärung</a> akzeptiert.</p>
					</div>
				</form>
			<?php endif; ?>
		</div>
	</div>
	</div>
</section>
