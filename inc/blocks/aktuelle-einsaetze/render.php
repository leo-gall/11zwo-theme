<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

$post_id = get_the_ID();

$einsatzliste_year = isset( $_GET['einsatz_jahr'] ) ? sanitize_text_field( wp_unslash( $_GET['einsatz_jahr'] ) ) : '';
$einsatzliste_page  = isset( $_GET['einsatz_seite'] ) ? (int) $_GET['einsatz_seite'] : 1;
$einsatzliste_data  = elfzwo_einsatzliste_data( $einsatzliste_year, $einsatzliste_page );

$hero_post = get_posts( array( 'post_type' => 'post', 'posts_per_page' => 1, 'orderby' => 'date', 'order' => 'DESC' ) );
$hero_post = $hero_post ? $hero_post[0] : null;
?>
<section class="mx-auto max-w-7xl px-5 py-8 md:px-8">
	<div class="grid gap-10 lg:grid-cols-[1.6fr_1fr]">
		<article class="min-w-0">
			<?php if ( ! $hero_post ) : ?>
				<div class="flex h-[420px] items-center justify-center rounded-[2rem] border border-border text-center text-muted-foreground">Aktuell gibt es keine vorgestellte Meldung.</div>
			<?php else :
				$hero_image     = has_post_thumbnail( $hero_post ) ? get_the_post_thumbnail_url( $hero_post, 'large' ) : get_template_directory_uri() . '/assets/images/news-1.jpg';
				$hero_kategorie = elfzwo_post_category_line( $hero_post->ID );
				?>
				<a href="<?php echo esc_url( get_permalink( $hero_post ) ); ?>" class="block">
					<div class="relative overflow-hidden rounded-[2rem] border border-border">
						<img src="<?php echo esc_url( $hero_image ); ?>" alt="<?php echo esc_attr( $hero_post->post_title ); ?>" width="1200" height="800" loading="lazy" class="h-[420px] w-full object-cover transition-transform duration-300 hover:scale-[1.02]">
					</div>
					<?php if ( $hero_kategorie ) : ?><p class="mt-6 text-xs font-semibold uppercase tracking-[0.18em] text-signal"><?php echo esc_html( $hero_kategorie ); ?></p><?php endif; ?>
					<h2 class="mt-3 font-display text-4xl leading-tight md:text-5xl"><?php echo esc_html( $hero_post->post_title ); ?></h2>
					<p class="mt-4 max-w-2xl text-lg text-muted-foreground"><?php echo esc_html( elfzwo_excerpt( $hero_post->ID, 55 ) ); ?></p>
					<p class="mt-4 text-xs uppercase tracking-widest text-muted-foreground"><?php echo esc_html( get_the_date( '', $hero_post ) ); ?></p>
				</a>
			<?php endif; ?>
		</article>

		<aside id="elfzwo-einsatzliste-<?php echo esc_attr( $post_id ); ?>" class="elfzwo-einsatzliste flex min-w-0 flex-col rounded-[2rem] border border-border bg-card p-6 transition-opacity md:p-8" data-post-id="<?php echo esc_attr( $post_id ); ?>">
			<?php echo elfzwo_einsatzliste_render( $post_id, $einsatzliste_data ); // phpcs:ignore -- bereits escaped in elfzwo_einsatzliste_render() ?>
		</aside>
	</div>
</section>
