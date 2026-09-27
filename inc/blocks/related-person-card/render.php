<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$name      = $attributes['name'] ?? '';
$rolle     = $attributes['rolle'] ?? '';
$tel       = $attributes['telefon'] ?? '';
$email     = $attributes['email'] ?? '';
$show_mail = ! empty( $attributes['showEmail'] );

if ( ! $name ) {
	if ( current_user_can( 'edit_posts' ) ) {
		echo '<p class="rounded-2xl border border-dashed border-border p-5 text-sm text-muted-foreground">Ansprechpartner-Karte: kein Name eingetragen.</p>';
	}
	return;
}
?>
<div class="flex h-full items-center gap-4 rounded-2xl border border-border bg-card p-6">
	<div class="shrink-0"><?php echo elfzwo_person_wappen_svg( $name, $rolle, 50 ); // phpcs:ignore -- bereits escaped ?></div>
	<div class="min-w-0">
		<p class="font-display text-lg leading-tight"><?php echo esc_html( $name ); ?></p>
		<?php if ( $rolle ) : ?><span class="mt-1.5 inline-block rounded-full bg-signal/10 px-3 py-1 text-xs font-semibold text-signal"><?php echo esc_html( $rolle ); ?></span><?php endif; ?>
		<?php if ( $tel ) : ?><a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $tel ) ); ?>" class="mt-1.5 flex items-center gap-1.5 text-sm text-muted-foreground hover:text-signal"><?php echo elfzwo_icon( 'phone', 'h-3.5 w-3.5' ); ?><?php echo esc_html( $tel ); ?></a><?php endif; ?>
		<?php if ( $show_mail && $email ) : ?><a href="mailto:<?php echo esc_attr( $email ); ?>" class="mt-1 flex items-center gap-1.5 text-sm text-muted-foreground hover:text-signal"><?php echo elfzwo_icon( 'mail', 'h-3.5 w-3.5' ); ?><?php echo esc_html( $email ); ?></a><?php endif; ?>
	</div>
</div>
