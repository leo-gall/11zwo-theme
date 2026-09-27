<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$items = $attributes['items'] ?? array();
?>
<div class="divide-y divide-border rounded-3xl border border-border bg-card">
	<?php foreach ( $items as $item ) :
		$question = $item['question'] ?? '';
		$answer   = $item['answer'] ?? '';
		?>
		<details class="elfzwo-faq-item px-6 py-5">
			<summary class="flex cursor-pointer items-center justify-between gap-4 font-display text-base">
				<?php echo esc_html( $question ); ?>
				<?php echo elfzwo_icon( 'chevron-down', 'elfzwo-faq-chevron h-4 w-4 shrink-0 text-muted-foreground' ); ?>
			</summary>
			<div class="elfzwo-faq-answer">
				<p class="mt-3 text-sm text-muted-foreground"><?php echo esc_html( $answer ); ?></p>
			</div>
		</details>
	<?php endforeach; ?>
</div>
