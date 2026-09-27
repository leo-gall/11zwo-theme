<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$interests = $attributes['interests'] ?? array();
$post_id   = get_the_ID();
$status    = isset( $_GET['mitmachen'] ) ? sanitize_text_field( wp_unslash( $_GET['mitmachen'] ) ) : '';
?>
<section class="mx-auto max-w-3xl px-5 pb-16 md:px-8">
	<div class="rounded-[2.5rem] border border-border bg-card p-6 shadow-[0_20px_60px_-30px_var(--wood)] md:p-10">
		<?php if ( 'success' === $status ) : ?>
			<div class="py-6 text-center">
				<span class="mx-auto grid h-20 w-20 place-items-center rounded-full bg-ember text-ember-foreground"><?php echo elfzwo_icon( 'sparkles', 'h-10 w-10' ); ?></span>
				<h2 class="mt-6 font-display text-4xl">Angekommen!</h2>
				<p class="mx-auto mt-3 max-w-md text-muted-foreground">Wir melden uns in den nächsten Tagen. Bis dahin — sei stolz auf dich, das war der erste Schritt.</p>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="mt-8 inline-flex items-center gap-2 rounded-full border border-ink px-6 py-3 text-sm font-semibold">Zur Startseite</a>
			</div>
		<?php else : ?>
			<?php if ( 'error' === $status ) : ?>
				<p class="mb-6 rounded-xl border border-destructive bg-destructive/25 px-4 py-3 text-sm text-destructive">Bitte fülle Name und Kontakt aus.</p>
			<?php endif; ?>

			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="elfzwo_mitmachen">
				<input type="hidden" name="redirect_id" value="<?php echo esc_attr( $post_id ); ?>">
				<?php wp_nonce_field( 'elfzwo_mitmachen', 'elfzwo_mitmachen_nonce' ); ?>

				<h2 class="font-display text-3xl">Was passt zu dir?</h2>
				<p class="mt-2 text-muted-foreground">Wähl aus, was am ehesten zu dir passt.</p>
				<div class="mt-6 grid gap-3 sm:grid-cols-2">
					<?php foreach ( $interests as $i => $interest ) : ?>
						<label class="block cursor-pointer rounded-2xl border border-border bg-background p-5 text-left transition hover:border-ink has-[:checked]:border-signal has-[:checked]:bg-signal/20">
							<input type="radio" name="interesse" value="<?php echo esc_attr( $interest['label'] ?? '' ); ?>" class="sr-only" <?php checked( 0 === $i ); ?> required>
							<p class="font-display text-lg"><?php echo esc_html( $interest['label'] ?? '' ); ?></p>
							<p class="mt-1 text-sm text-muted-foreground"><?php echo esc_html( $interest['hint'] ?? '' ); ?></p>
						</label>
					<?php endforeach; ?>
				</div>

				<h2 class="mt-10 font-display text-3xl">Wie erreichen wir dich?</h2>
				<p class="mt-2 text-muted-foreground">Wir melden uns bei dir!</p>
				<div class="mt-6 space-y-4">
					<label class="block">
						<span class="text-sm font-medium">Dein Name</span>
						<input required name="name" placeholder="Max Muster" class="mt-1 w-full rounded-2xl border border-border bg-background px-4 py-3 text-base outline-none transition focus:border-signal">
					</label>
					<label class="block">
						<span class="text-sm font-medium">E-Mail oder Handynummer</span>
						<input required name="kontakt" placeholder="max@example.de oder 0170 …" class="mt-1 w-full rounded-2xl border border-border bg-background px-4 py-3 text-base outline-none transition focus:border-signal">
					</label>
				</div>

				<div class="mt-8 flex justify-end">
					<button type="submit" class="inline-flex items-center gap-3 rounded-full bg-signal px-7 py-4 text-base font-semibold text-signal-foreground transition-colors hover:bg-signal/90">
						Absenden <?php echo elfzwo_icon( 'arrow-right', 'h-4 w-4' ); ?>
					</button>
				</div>
			</form>
		<?php endif; ?>
	</div>

	<p class="mt-6 text-center text-xs text-muted-foreground">Deine Daten bleiben bei uns — kein Newsletter, kein Weitergeben. Ehrenwort.</p>
</section>
