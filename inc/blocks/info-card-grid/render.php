<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$cards = $attributes['cards'] ?? array();
?>
<section class="mx-auto max-w-3xl px-5 py-6 md:px-8">
	<div class="grid gap-6 sm:grid-cols-2">
		<?php foreach ( $cards as $card ) :
			$icon  = $card['icon'] ?? '';
			$title = $card['title'] ?? '';
			$body  = $card['body'] ?? '';
			?>
			<div class="rounded-2xl border border-border bg-card p-6">
				<?php if ( $icon ) : ?>
					<span class="grid h-8 w-8 place-items-center text-signal">
						<?php echo elfzwo_icon( $icon, 'h-5 w-5', 2.2 ); ?>
					</span>
				<?php endif; ?>
				<?php if ( $title ) : ?><h2 class="mt-4 font-display text-lg"><?php echo esc_html( $title ); ?></h2><?php endif; ?>
				<div class="<?php echo $title ? 'mt-2' : 'mt-4'; ?> text-sm text-smoke"><?php echo wp_kses_post( wpautop( $body ) ); ?></div>
			</div>
		<?php endforeach; ?>
	</div>
</section>
