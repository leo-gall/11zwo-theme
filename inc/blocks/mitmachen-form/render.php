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
$status  = isset( $_GET['mitmachen'] ) ? sanitize_text_field( wp_unslash( $_GET['mitmachen'] ) ) : '';

$personen = array_values( array_filter( elfzwo_footer_personen(), function ( $person ) {
	return ! empty( $person['name'] ) && ! empty( $person['telefon'] );
} ) );
$person   = $personen[0] ?? null;
?>
<section class="mx-auto max-w-2xl px-5 pt-10 pb-16 md:pt-16 lg:pb-20">

	<div class="text-center">
		<?php if ( $kicker ) : ?><p class="font-hand text-2xl text-primary"><?php echo esc_html( $kicker ); ?></p><?php endif; ?>
		<h1 class="mt-1 text-4xl font-bold leading-tight tracking-tight md:text-5xl">
			<?php echo esc_html( $title ); ?>
			<?php if ( $title_hand ) : ?><span class="text-signal"><?php echo esc_html( $title_hand ); ?></span><?php endif; ?>
		</h1>
		<?php if ( $description ) : ?><p class="mx-auto mt-4 max-w-xl text-lg leading-relaxed text-muted-foreground"><?php echo esc_html( $description ); ?></p><?php endif; ?>
	</div>

	<?php if ( $steps ) : ?>
		<div class="mt-8 md:mt-10">
			<h2 class="text-center text-sm font-semibold uppercase tracking-wider text-muted-foreground">So geht's weiter</h2>
			<ol class="mt-4 grid gap-3 sm:grid-cols-<?php echo esc_attr( min( count( $steps ), 3 ) ); ?>">
				<?php foreach ( $steps as $i => $step ) : ?>
					<li class="flex gap-3 rounded-[1.25rem] border border-border bg-card p-4">
						<span class="grid h-7 w-7 shrink-0 place-items-center rounded-full bg-signal text-sm font-bold text-signal-foreground"><?php echo esc_html( $i + 1 ); ?></span>
						<span class="min-w-0">
							<span class="block font-semibold leading-tight"><?php echo esc_html( $step['title'] ); ?></span>
							<?php if ( ! empty( $step['text'] ) ) : ?><span class="mt-1 block text-sm text-muted-foreground"><?php echo esc_html( $step['text'] ); ?></span><?php endif; ?>
						</span>
					</li>
				<?php endforeach; ?>
			</ol>
		</div>
	<?php endif; ?>

	<div id="formular" class="mt-8 scroll-mt-28 rounded-[2rem] border border-border bg-card md:mt-10">
		<?php if ( 'success' === $status ) : ?>
			<div class="px-6 py-12 text-center md:px-10">
				<span class="mx-auto grid h-16 w-16 place-items-center rounded-full bg-ember text-ember-foreground"><?php echo elfzwo_icon( 'sparkles', 'h-8 w-8' ); ?></span>
				<h2 class="mt-5 font-display text-3xl">Angekommen!</h2>
				<p class="mx-auto mt-3 max-w-md text-muted-foreground">Wir melden uns in den nächsten Tagen.</p>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="elfzwo-btn elfzwo-btn-secondary mt-6">Zur Startseite</a>
			</div>
		<?php else : ?>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="p-5 sm:p-8">
				<input type="hidden" name="action" value="elfzwo_mitmachen">
				<input type="hidden" name="redirect_id" value="<?php echo esc_attr( $post_id ); ?>">
				<?php wp_nonce_field( 'elfzwo_mitmachen', 'elfzwo_mitmachen_nonce' ); ?>

				<?php if ( 'error' === $status ) : ?>
					<p class="mb-6 flex items-center gap-2 rounded-[1rem] border border-destructive/40 bg-destructive/10 px-4 py-3 text-sm font-medium text-destructive"><?php echo elfzwo_icon( 'triangle-alert', 'h-4 w-4 shrink-0' ); ?> Bitte gib deinen Namen und eine gültige E-Mail-Adresse an.</p>
				<?php endif; ?>

				<fieldset>
					<legend class="text-sm font-semibold">Was interessiert dich?</legend>
					<div class="mt-2 grid gap-2 sm:grid-cols-2">
						<?php foreach ( $interests as $i => $interest ) : ?>
							<label class="flex cursor-pointer items-center gap-3 rounded-[1rem] border-2 border-border bg-background px-4 py-3 transition hover:border-signal/50 has-[:checked]:border-signal has-[:checked]:bg-signal/5 has-[:focus-visible]:ring-4 has-[:focus-visible]:ring-signal/30">
								<input type="radio" name="interesse" value="<?php echo esc_attr( $interest['label'] ?? '' ); ?>" class="h-4 w-4 shrink-0 accent-[var(--signal)]" <?php checked( 0 === $i ); ?> required>
								<span class="min-w-0">
									<span class="block font-semibold leading-tight"><?php echo esc_html( $interest['label'] ?? '' ); ?></span>
									<?php if ( ! empty( $interest['hint'] ) ) : ?><span class="block text-sm text-muted-foreground"><?php echo esc_html( $interest['hint'] ); ?></span><?php endif; ?>
								</span>
							</label>
						<?php endforeach; ?>
					</div>
				</fieldset>

				<div class="mt-6 grid gap-4 sm:grid-cols-2">
					<label class="block">
						<span class="text-sm font-semibold">Dein Name</span>
						<input required name="name" autocomplete="name" placeholder="Max Muster" class="mt-1.5 w-full rounded-[1rem] border-2 border-border bg-background px-4 py-3 text-base outline-none transition focus:border-signal">
					</label>
					<label class="block">
						<span class="text-sm font-semibold">Deine E-Mail-Adresse</span>
						<input required type="email" name="kontakt" autocomplete="email" placeholder="max@example.de" class="mt-1.5 w-full rounded-[1rem] border-2 border-border bg-background px-4 py-3 text-base outline-none transition focus:border-signal">
					</label>
				</div>

				<button type="submit" class="elfzwo-btn elfzwo-btn-primary mt-6 w-full">
					Ich bin dabei! <?php echo elfzwo_icon( 'arrow-right', 'h-4 w-4 elfzwo-btn-arrow' ); ?>
				</button>
				<p class="mt-3 text-center text-xs text-muted-foreground">Infos zum Umgang mit deinen Daten findest du in unserer <a href="<?php echo esc_url( $datenschutz_url ); ?>" class="underline underline-offset-2 hover:text-signal">Datenschutzerklärung</a>.</p>
			</form>
		<?php endif; ?>
	</div>

	<?php if ( $person ) : ?>
		<p class="mt-5 text-center text-sm text-muted-foreground">
			Lieber anrufen? <?php echo esc_html( $person['name'] ); ?>:
			<a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $person['telefon'] ) ); ?>" class="font-semibold text-signal underline-offset-4 hover:underline"><?php echo esc_html( $person['telefon'] ); ?></a>
		</p>
	<?php endif; ?>

</section>
