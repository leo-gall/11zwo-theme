<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$columns = (int) ( $attributes['columns'] ?? 4 );
$columns = max( 2, min( 4, $columns ) );
$cards   = $attributes['cards'] ?? array();
?>
<section class="mx-auto max-w-7xl px-5 py-8 md:px-8">
	<div class="grid gap-6 md:grid-cols-<?php echo esc_attr( $columns ); ?>">
		<?php foreach ( $cards as $card ) :
			$icon  = $card['icon'] ?? '';
			$title = $card['title'] ?? '';
			$body  = $card['body'] ?? '';
			?>
			<div class="group rounded-3xl border border-border bg-card p-8 transition hover:-translate-y-1 hover:shadow-xl">
				<?php if ( $icon ) : ?>
					<span class="grid h-14 w-14 place-items-center rounded-2xl bg-primary/30 text-primary transition group-hover:rotate-[-6deg]">
						<?php echo elfzwo_icon( $icon, 'h-6 w-6', 2.2 ); ?>
					</span>
				<?php endif; ?>
				<h3 class="mt-5 font-display text-2xl"><?php echo esc_html( $title ); ?></h3>
				<p class="mt-2 text-muted-foreground"><?php echo esc_html( $body ); ?></p>
			</div>
		<?php endforeach; ?>
	</div>
</section>
