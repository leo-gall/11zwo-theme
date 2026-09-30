<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$title   = $attributes['title'] ?? '';
$columns = (int) ( $attributes['columns'] ?? 2 );
$columns = max( 1, min( 4, $columns ) );
$people  = $attributes['people'] ?? array();
?>
<div class="mx-auto max-w-7xl px-5 pb-8 md:px-8">
	<?php if ( $title ) : ?><h3 class="mb-5 font-display text-2xl"><?php echo esc_html( $title ); ?></h3><?php endif; ?>
	<div class="grid gap-4 sm:grid-cols-<?php echo esc_attr( $columns ); ?>">
		<?php foreach ( $people as $person ) :
			$name      = $person['name'] ?? '';
			$rolle     = $person['rolle'] ?? '';
			$tel       = $person['telefon'] ?? '';
			$email     = $person['email'] ?? '';
			$show_mail = ! empty( $person['showEmail'] );

			if ( ! $name ) {
				if ( current_user_can( 'edit_posts' ) ) {
					echo '<p class="rounded-2xl border border-dashed border-border p-5 text-sm text-muted-foreground">Ansprechpartner-Karte: kein Name eingetragen.</p>';
				}
				continue;
			}
			?>
			<div class="flex h-full items-center gap-4 rounded-2xl border border-border bg-card p-6">
				<div class="shrink-0"><?php echo elfzwo_person_wappen_svg( $name, $rolle, 50 ); // phpcs:ignore -- bereits escaped ?></div>
				<div class="min-w-0">
					<p class="font-display text-lg leading-tight"><?php echo esc_html( $name ); ?></p>
					<?php if ( $rolle ) : ?><span class="mt-1.5 inline-block rounded-full bg-signal/10 px-3 py-1 text-xs font-semibold text-signal"><?php echo esc_html( $rolle ); ?></span><?php endif; ?>
					<?php if ( $tel || ( $show_mail && $email ) ) : ?>
						<div class="mt-3 flex flex-wrap gap-2">
							<?php if ( $tel ) : ?><a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $tel ) ); ?>" class="elfzwo-btn elfzwo-btn-secondary elfzwo-btn-sm"><?php echo elfzwo_icon( 'phone', 'h-4 w-4 shrink-0' ); ?><?php echo esc_html( $tel ); ?></a><?php endif; ?>
							<?php if ( $show_mail && $email ) : ?><a href="mailto:<?php echo esc_attr( $email ); ?>" class="elfzwo-btn elfzwo-btn-secondary elfzwo-btn-sm min-w-0 max-w-full"><?php echo elfzwo_icon( 'mail', 'h-4 w-4 shrink-0' ); ?><span class="truncate"><?php echo esc_html( $email ); ?></span></a><?php endif; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</div>
