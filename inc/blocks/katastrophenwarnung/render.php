<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$level   = $attributes['level'] ?? 'Warnung';
$title   = $attributes['title'] ?? '';
$message = $attributes['message'] ?? '';
$source  = $attributes['source'] ?? '';
?>
<div class="border-b border-leaf/40 bg-leaf text-signal-foreground">
	<div class="mx-auto flex max-w-7xl flex-wrap items-start gap-3 px-5 py-4 md:px-8">
		<?php echo elfzwo_icon( 'triangle-alert', 'h-5 w-5 shrink-0 mt-0.5' ); ?>
		<div class="min-w-0">
			<p class="text-xs font-bold uppercase tracking-widest opacity-90"><?php echo esc_html( $level ); ?></p>
			<?php if ( $title ) : ?><p class="font-display text-lg leading-tight"><?php echo esc_html( $title ); ?></p><?php endif; ?>
			<?php if ( $message ) : ?><p class="mt-1 text-sm opacity-95"><?php echo esc_html( $message ); ?></p><?php endif; ?>
			<?php if ( $source ) : ?><p class="mt-1 text-xs opacity-75">Quelle: <?php echo esc_html( $source ); ?></p><?php endif; ?>
		</div>
	</div>
</div>
