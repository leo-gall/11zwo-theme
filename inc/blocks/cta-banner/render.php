<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$title       = $attributes['title'] ?? '';
$text        = $attributes['text'] ?? '';
$button_text = $attributes['buttonText'] ?? '';
$button_url  = $attributes['buttonUrl'] ?? '#';
$variant     = $attributes['variant'] ?? 'signal';
$info_icon   = $attributes['infoIcon'] ?? '';
$info_text   = $attributes['infoText'] ?? '';
$no_section  = ! empty( $attributes['noSection'] );

$bg_class  = 'signal' === $variant ? 'bg-signal text-signal-foreground' : 'bg-card border border-border';
$btn_class = 'signal' === $variant
	? 'bg-cream text-signal hover:bg-cream/90'
	: 'bg-signal text-signal-foreground hover:bg-signal/90';

if ( 'notruf' === $variant ) {
	// Band über die volle Breite: links die 112, daneben der Hinweis.
	echo '<section class="bg-secondary"><div class="mx-auto grid max-w-7xl md:grid-cols-[auto_1fr] md:px-8">'
		. '<a href="tel:112" class="flex flex-col justify-center bg-signal px-10 py-8 text-signal-foreground"><span class="text-sm font-semibold uppercase tracking-widest">Notruf</span><span class="font-display text-6xl font-black leading-none">112</span></a>'
		. '<div class="flex flex-wrap items-center justify-between gap-6 px-5 py-8 md:px-10">'
		. '<div>'
		. ( $title ? '<h2 class="font-display text-2xl md:text-3xl">' . esc_html( $title ) . '</h2>' : '' )
		. ( $text ? '<p class="mt-2 max-w-2xl text-muted-foreground">' . esc_html( $text ) . '</p>' : '' )
		. '</div>'
		. ( $button_text ? '<a href="' . esc_url( $button_url ) . '" class="elfzwo-btn elfzwo-btn-secondary shrink-0">' . esc_html( $button_text ) . '</a>' : '' )
		. '</div></div></section>'; // phpcs:ignore -- bereits escaped
	return;
}
$banner = '<div class="flex h-full flex-wrap items-center justify-between gap-6 rounded-lg ' . esc_attr( $bg_class ) . ' p-8 md:p-10">'
	. '<div>'
	. ( $title ? '<h2 class="font-display text-3xl md:text-4xl">' . esc_html( $title ) . '</h2>' : '' )
	. ( $text ? '<p class="mt-2 max-w-xl opacity-90">' . esc_html( $text ) . '</p>' : '' )
	. ( $info_text ? '<p class="mt-3 flex items-center gap-2 text-sm opacity-80">' . ( $info_icon ? elfzwo_icon( $info_icon, 'h-4 w-4' ) : '' ) . esc_html( $info_text ) . '</p>' : '' )
	. '</div>'
	. ( $button_text ? '<a href="' . esc_url( $button_url ) . '" class="inline-flex shrink-0 items-center gap-3 rounded ' . esc_attr( $btn_class ) . ' px-7 py-4 text-base font-semibold transition-colors">' . esc_html( $button_text ) . '</a>' : '' )
	. '</div>';

if ( $no_section ) {
	echo $banner; // phpcs:ignore -- bereits escaped
} else {
	?>
	<section class="mx-auto max-w-7xl px-5 py-8 md:px-8">
		<?php echo $banner; // phpcs:ignore -- bereits escaped ?>
	</section>
	<?php
}
