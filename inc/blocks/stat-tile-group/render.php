<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$intro   = $attributes['intro'] ?? '';
$columns = (int) ( $attributes['columns'] ?? 4 );
$columns = max( 2, min( 6, $columns ) );
$tiles   = $attributes['tiles'] ?? array();
?>
<section class="mx-auto max-w-7xl px-5 py-8 md:px-8">
	<div class="rounded-lg bg-ash p-8 md:p-10">
		<?php if ( $intro ) : ?>
			<p class="max-w-2xl text-smoke"><?php echo esc_html( $intro ); ?></p>
		<?php endif; ?>
		<div class="<?php echo $intro ? 'mt-6' : ''; ?> grid gap-4 sm:grid-cols-3 <?php echo esc_attr( array( 2 => 'md:grid-cols-2', 3 => 'md:grid-cols-3', 4 => 'md:grid-cols-4', 5 => 'md:grid-cols-5', 6 => 'md:grid-cols-6' )[ $columns ] ); ?>">
			<?php foreach ( $tiles as $tile ) :
				$icon  = $tile['icon'] ?? '';
				$value = $tile['value'] ?? '';
				$label = $tile['label'] ?? '';
				?>
				<div class="rounded-2xl bg-card p-5">
					<?php if ( $icon ) : ?>
						<span class="grid h-8 w-8 place-items-center text-signal">
							<?php echo elfzwo_icon( $icon, 'h-5 w-5', 2.2 ); ?>
						</span>
					<?php endif; ?>
					<p class="mt-3 text-3xl font-display"><?php echo esc_html( $value ); ?></p>
					<p class="mt-1 text-sm text-smoke"><?php echo esc_html( $label ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
