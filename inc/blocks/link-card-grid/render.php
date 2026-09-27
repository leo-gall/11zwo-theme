<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$cards = $attributes['cards'] ?? array();
?>
<section class="mx-auto max-w-7xl px-5 py-8 md:px-8">
	<div class="grid gap-6 md:grid-cols-3">
		<?php foreach ( $cards as $card ) :
			$tag    = $card['tag'] ?? '';
			$title  = $card['title'] ?? '';
			$body   = $card['body'] ?? '';
			$url    = $card['url'] ?? '#';
			$icon   = $card['icon'] ?? '';
			$accent = $card['accent'] ?? 'bg-wood/35';
			?>
			<a href="<?php echo esc_url( $url ); ?>" class="group flex flex-col rounded-3xl border border-border bg-card p-8 transition hover:-translate-y-1 hover:shadow-xl">
				<div class="flex items-center justify-between">
					<span class="grid h-14 w-14 place-items-center rounded-2xl <?php echo esc_attr( $accent ); ?>">
						<?php echo elfzwo_icon( $icon, 'h-6 w-6', 2.2 ); ?>
					</span>
					<span class="text-[11px] uppercase tracking-widest text-muted-foreground"><?php echo esc_html( $tag ); ?></span>
				</div>
				<h3 class="mt-6 font-display text-2xl"><?php echo esc_html( $title ); ?></h3>
				<p class="mt-2 flex-1 text-muted-foreground"><?php echo esc_html( $body ); ?></p>
				<span class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-signal">
					Ansehen <?php echo elfzwo_icon( 'arrow-right', 'h-4 w-4 transition group-hover:translate-x-1' ); ?>
				</span>
			</a>
		<?php endforeach; ?>
	</div>
</section>
