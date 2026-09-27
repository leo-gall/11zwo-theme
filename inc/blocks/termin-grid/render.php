<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$ferien  = $attributes['ferienHinweis'] ?? '';
$termine = $attributes['termine'] ?? array();
?>
<section class="mx-auto max-w-7xl px-5 py-8 md:px-8">
	<div class="rounded-3xl border border-border bg-card p-6 md:p-8">
		<?php if ( $ferien ) : ?>
			<p class="mb-6 flex items-center gap-2 text-sm text-muted-foreground">
				<?php echo elfzwo_icon( 'calendar-clock', 'h-4 w-4 shrink-0' ); ?> <?php echo esc_html( $ferien ); ?>
			</p>
		<?php endif; ?>
		<div class="grid gap-6 sm:grid-cols-2">
			<?php foreach ( $termine as $termin ) :
				$zeit = $termin['zeit'] ?? '';
				$was  = $termin['was'] ?? '';
				?>
				<div class="rounded-2xl border border-border bg-background p-6">
					<div class="font-display text-2xl text-signal"><?php echo esc_html( $zeit ); ?></div>
					<p class="mt-3 text-base text-muted-foreground"><?php echo esc_html( $was ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
