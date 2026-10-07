<?php
/**
 * Seite nicht gefunden: roter Bereich im Stil des Jumbotrons, der den Platz
 * zwischen Header und Footer füllt (nie scrollbar, siehe theme.css).
 */

get_header();
?>

<main class="flex min-h-0 flex-1 flex-col">
	<section class="relative isolate flex min-h-0 flex-1 items-center justify-center overflow-hidden bg-signal text-white">
		<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/hero-hintergrund.jpg' ); ?>" alt="" class="absolute inset-0 -z-20 h-full w-full object-cover">
		<div class="absolute inset-0 -z-10 bg-gradient-to-r from-wood/95 via-signal/90 to-signal/75" aria-hidden="true"></div>
		<div class="px-5 py-4 text-center">
			<p class="font-display text-[clamp(2.5rem,min(30vw,22dvh),12rem)] font-black leading-none">404</p>
			<h1 class="mt-[2dvh] font-display text-[clamp(1.25rem,min(8vw,6dvh),3rem)] font-black leading-tight">Seite nicht gefunden</h1>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="elfzwo-btn elfzwo-btn-light mt-[4dvh]">Zur Startseite</a>
		</div>
	</section>
</main>

<?php get_footer(); ?>
