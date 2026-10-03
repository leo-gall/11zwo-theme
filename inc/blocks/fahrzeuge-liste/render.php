<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

$fahrzeuge = new WP_Query( array( 'post_type' => 'fahrzeug', 'posts_per_page' => -1, 'orderby' => 'menu_order date', 'order' => 'ASC' ) );
?>
<section class="mx-auto max-w-7xl space-y-16 px-5 py-8 md:px-8">
	<?php
	$i = 0;
	while ( $fahrzeuge->have_posts() ) :
		$fahrzeuge->the_post();
		$fid  = get_the_ID();
		$tag  = elfzwo_meta( $fid, 'tag', '' );
		$img_url = has_post_thumbnail() ? get_the_post_thumbnail_url( $fid, 'large' ) : get_template_directory_uri() . '/assets/images/hero-team.jpg';

		$spec_fields = array(
			array( 'key' => 'besatzung', 'icon' => 'users', 'label' => 'Besatzung' ),
			array( 'key' => 'hersteller_aufbau', 'icon' => 'truck', 'label' => 'Hersteller / Aufbau' ),
			array( 'key' => 'baujahr', 'icon' => 'calendar-days', 'label' => 'Baujahr' ),
			array( 'key' => 'besonderheiten', 'icon' => 'sparkles', 'label' => 'Besonderheiten' ),
		);
		$specs = array();
		foreach ( $spec_fields as $sf ) {
			$specs[] = array(
				'icon'  => $sf['icon'],
				'label' => $sf['label'],
				'value' => elfzwo_meta( $fid, $sf['key'], '' ) ?: '–',
			);
		}
		$reverse = ( 1 === $i % 2 );
		$i++;
		?>
		<article class="grid gap-8 md:grid-cols-2 md:items-center">
			<div class="relative <?php echo $reverse ? 'md:order-2' : ''; ?>">
				<div class="absolute -inset-3 rounded-[2.5rem] bg-primary"></div>
				<img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" width="1200" height="800" loading="lazy" class="relative rounded-[2.5rem] object-cover shadow-xl">
			</div>
			<div>
				<?php if ( $tag ) : ?><p class="text-xs font-semibold uppercase tracking-[0.18em] text-muted-foreground"><?php echo esc_html( $tag ); ?></p><?php endif; ?>
				<h2 class="mt-2 font-display text-4xl md:text-5xl"><?php the_title(); ?></h2>
				<?php if ( get_the_content() ) : ?><div class="mt-4 text-lg text-muted-foreground"><?php the_content(); ?></div><?php endif; ?>
				<?php if ( $specs ) : ?>
					<dl class="mt-6 grid gap-3 sm:grid-cols-2">
						<?php foreach ( $specs as $s ) : ?>
							<div class="flex min-w-0 items-center gap-3 rounded-2xl border border-border bg-card p-4 <?php echo mb_strlen( $s['value'] ) > 28 ? 'sm:col-span-2' : ''; ?>">
								<span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-signal/25 text-signal"><?php echo elfzwo_icon( $s['icon'], 'h-5 w-5', 2.2 ); ?></span>
								<div class="min-w-0">
									<dt class="text-[11px] uppercase tracking-widest text-muted-foreground [overflow-wrap:anywhere]"><?php echo esc_html( $s['label'] ); ?></dt>
									<dd class="font-display text-lg leading-tight hyphens-auto [overflow-wrap:anywhere]"><?php echo esc_html( $s['value'] ); ?></dd>
								</div>
							</div>
						<?php endforeach; ?>
					</dl>
				<?php endif; ?>
			</div>
		</article>
	<?php endwhile; wp_reset_postdata(); ?>
</section>
