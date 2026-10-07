<?php
/**
 * Einzelansicht eines Vereinsbeitrags: roter Kopf mit Datum und Titel, das
 * Titelbild ragt in den Kopf hinein, darunter der Text und weitere Beiträge.
 */

get_header();

while ( have_posts() ) :
	the_post();
	$elfzwo_bild    = get_post_thumbnail_id();
	$elfzwo_weitere = get_posts(
		array(
			'post_type'   => 'post',
			'numberposts' => 3,
			'exclude'     => array( get_the_ID() ),
		)
	);
	$elfzwo_lesezeit = max( 1, (int) round( preg_match_all( '/\p{L}+/u', wp_strip_all_tags( get_the_content() ) ) / 200 ) );
	?>

	<main data-beitrag="<?php echo esc_attr( get_the_title() ); ?>" data-beitrag-datum="<?php echo esc_attr( get_the_date( 'd.m.Y' ) ); ?>">
		<header class="relative isolate overflow-hidden bg-signal text-white">
			<div class="absolute inset-0 -z-10 bg-gradient-to-r from-wood via-signal to-signal" aria-hidden="true"></div>
			<div class="mx-auto max-w-3xl px-5 pt-10 md:px-8 md:pt-14 <?php echo $elfzwo_bild ? 'pb-40 md:pb-56' : 'pb-14 md:pb-20'; ?>">
				<a href="<?php echo esc_url( home_url( '/verein/#aktuelles' ) ); ?>" class="inline-flex items-center gap-2 text-sm font-semibold text-white/80 hover:text-white">
					<span aria-hidden="true">&larr;</span> Alle Neuigkeiten
				</a>
				<p class="mt-8 text-sm font-semibold uppercase tracking-widest text-white/80">
					<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'j. F Y' ) ); ?></time>
					<span class="mx-2" aria-hidden="true">·</span><?php echo esc_html( $elfzwo_lesezeit ); ?> Min. Lesezeit
				</p>
				<h1 class="mt-3 font-display text-4xl font-black leading-tight md:text-6xl"><?php the_title(); ?></h1>
			</div>
		</header>

		<?php if ( $elfzwo_bild ) : ?>
			<figure class="relative mx-auto -mt-32 max-w-5xl px-5 md:-mt-44 md:px-8">
				<?php echo wp_get_attachment_image( $elfzwo_bild, 'large', false, array( 'class' => 'aspect-[16/9] w-full object-cover shadow-xl' ) ); ?>
				<?php $elfzwo_bu = wp_get_attachment_caption( $elfzwo_bild ); ?>
				<?php if ( $elfzwo_bu ) : ?><figcaption class="mt-2 text-sm text-smoke"><?php echo esc_html( $elfzwo_bu ); ?></figcaption><?php endif; ?>
			</figure>
		<?php endif; ?>

		<article class="elfzwo-beitrag prose mx-auto max-w-3xl px-5 py-12 text-lg md:px-8 md:py-16">
			<?php the_content(); ?>
		</article>

		<?php if ( $elfzwo_weitere ) : ?>
			<section class="bg-ash">
				<div class="mx-auto max-w-7xl px-5 py-12 md:px-8 md:py-16">
					<div class="flex items-end justify-between gap-4">
						<h2 class="font-display text-3xl font-black text-signal md:text-4xl">Weitere Neuigkeiten</h2>
						<a href="<?php echo esc_url( home_url( '/verein/#aktuelles' ) ); ?>" class="shrink-0 text-sm font-semibold text-signal hover:underline">Alle Neuigkeiten</a>
					</div>
					<div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
						<?php foreach ( $elfzwo_weitere as $elfzwo_eintrag ) {
							echo elfzwo_beitrag_karte( $elfzwo_eintrag ); // phpcs:ignore -- bereits escaped
						} ?>
					</div>
				</div>
			</section>
		<?php endif; ?>
	</main>

<?php endwhile; ?>

<?php get_footer(); ?>
