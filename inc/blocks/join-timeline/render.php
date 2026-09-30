<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$columns = (int) ( $attributes['columns'] ?? 4 );
$columns = max( 2, min( 5, $columns ) );
$steps   = $attributes['steps'] ?? array();
?>
<section class="mx-auto max-w-7xl px-5 py-8 md:px-8">
	<div class="relative">
		<div class="absolute left-0 right-0 top-6 hidden h-px bg-border md:block"></div>
		<div class="grid gap-10 md:grid-cols-<?php echo esc_attr( $columns ); ?>">
			<?php foreach ( $steps as $step ) :
				$number      = $step['number'] ?? '';
				$title       = $step['title'] ?? '';
				$body        = $step['body'] ?? '';
				$button_text = $step['buttonText'] ?? '';
				$button_url  = $step['buttonUrl'] ?? '#';
				$is_cta_slot = $button_text && ! $number;
				?>
				<div class="relative">
					<?php if ( $is_cta_slot ) : ?>
						<a href="<?php echo esc_url( $button_url ); ?>" class="elfzwo-btn elfzwo-btn-primary w-full md:hidden">
							<?php echo esc_html( $button_text ); ?> <?php echo elfzwo_icon( 'arrow-right', 'h-4 w-4 elfzwo-btn-arrow' ); ?>
						</a>
						<div class="hidden md:block">
							<a href="<?php echo esc_url( $button_url ); ?>" class="elfzwo-btn elfzwo-btn-primary elfzwo-btn-sm relative z-10 h-12">
								<?php echo esc_html( $button_text ); ?> <?php echo elfzwo_icon( 'arrow-right', 'h-4 w-4 elfzwo-btn-arrow' ); ?>
							</a>
							<?php if ( $title ) : ?><h3 class="mt-5 font-display text-xl"><?php echo esc_html( $title ); ?></h3><?php endif; ?>
							<?php if ( $body ) : ?><p class="mt-2 text-sm text-muted-foreground"><?php echo esc_html( $body ); ?></p><?php endif; ?>
						</div>
					<?php else : ?>
						<?php if ( $number ) : ?>
							<span class="relative z-10 grid h-12 w-12 place-items-center rounded-full bg-signal font-display text-lg text-signal-foreground"><?php echo esc_html( $number ); ?></span>
						<?php endif; ?>
						<?php if ( $title ) : ?><h3 class="mt-5 font-display text-xl"><?php echo esc_html( $title ); ?></h3><?php endif; ?>
						<?php if ( $body ) : ?><p class="mt-2 text-sm text-muted-foreground"><?php echo esc_html( $body ); ?></p><?php endif; ?>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
