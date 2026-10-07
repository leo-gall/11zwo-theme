<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$columns = (int) ( $attributes['columns'] ?? 4 );
$columns = max( 2, min( 4, $columns ) );
$cards   = $attributes['cards'] ?? array();
?>
<section class="mx-auto max-w-7xl px-5 py-8 md:px-8">
	<div class="grid gap-6 <?php echo esc_attr( array( 2 => 'md:grid-cols-2', 3 => 'md:grid-cols-3', 4 => 'md:grid-cols-4' )[ $columns ] ); ?>">
		<?php foreach ( $cards as $card ) :
			$icon  = $card['icon'] ?? '';
			$title = $card['title'] ?? '';
			$body  = $card['body'] ?? '';
			?>
			<div class="border border-border bg-card p-8">
				<?php if ( $icon ) : ?>
					<span class="grid h-8 w-8 place-items-center text-signal">
						<?php echo elfzwo_icon( $icon, 'h-6 w-6', 2.2 ); ?>
					</span>
				<?php endif; ?>
				<h3 class="mt-5 font-display text-2xl"><?php echo esc_html( $title ); ?></h3>
				<p class="mt-2 text-smoke"><?php echo esc_html( $body ); ?></p>
			</div>
		<?php endforeach; ?>
	</div>
</section>
