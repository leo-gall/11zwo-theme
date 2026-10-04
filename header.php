<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'min-h-screen overflow-x-clip' ); ?>>
<?php wp_body_open(); ?>

<?php
if ( ! function_exists( 'elfzwo_render_nav_group' ) ) {
	function elfzwo_render_nav_group( $nodes, $justify ) {
		$pill_class = 'px-3.5 py-2 text-[15px] font-medium text-foreground transition-colors duration-200 hover:text-signal';
		?>
		<nav class="hidden items-center gap-1 lg:flex <?php echo esc_attr( $justify ); ?>">
			<?php foreach ( $nodes as $node ) :
				$item = $node['item'];
				if ( $node['children'] ) :
					?>
					<div class="nav-dropdown relative">
						<button type="button" class="nav-dropdown-trigger <?php echo esc_attr( $pill_class ); ?> flex items-center gap-1 cursor-pointer" aria-expanded="false" aria-haspopup="menu">
							<?php echo esc_html( $item->title ); ?>
							<?php echo elfzwo_icon( 'chevron-down', 'nav-chevron h-3.5 w-3.5 transition-transform duration-200' ); ?>
						</button>
						<div role="menu" class="nav-dropdown-panel absolute left-0 top-full z-50 mt-2 w-64 rounded-2xl border border-border bg-card p-2 shadow-lg">
							<?php foreach ( $node['children'] as $child ) : ?>
								<a href="<?php echo esc_url( $child->url ); ?>" role="menuitem" class="block rounded-xl px-3 py-2.5 text-sm text-foreground/80 transition-colors hover:bg-secondary hover:text-foreground"><?php echo esc_html( $child->title ); ?></a>
							<?php endforeach; ?>
						</div>
					</div>
				<?php else : ?>
					<a href="<?php echo esc_url( $item->url ); ?>" class="<?php echo esc_attr( $pill_class ); ?>"><?php echo esc_html( $item->title ); ?></a>
				<?php
				endif;
			endforeach;
			?>
		</nav>
		<?php
	}
}

$elfzwo_nav_items = elfzwo_get_menu_tree( 'primary' );
?>

<header class="sticky top-0 z-40 bg-white shadow-sm">
	<div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-5 py-2.5 md:px-8">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="group shrink-0" aria-label="Freiwillige Feuerwehr Greifenberg — Startseite">
			<img
				src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/logo.png' ); ?>"
				alt="Freiwillige Feuerwehr Greifenberg"
				class="h-14 w-auto transition-transform duration-200 group-hover:scale-105 md:h-16"
			>
		</a>

		<div class="flex items-center gap-2">
			<?php elfzwo_render_nav_group( $elfzwo_nav_items, 'mr-3' ); ?>
			<a href="<?php echo esc_url( home_url( '/mitmachen/' ) ); ?>" class="elfzwo-btn elfzwo-btn-primary elfzwo-btn-sm hidden sm:inline-flex">
				Mach mit!
			</a>
			<button id="mobile-toggle" type="button" class="relative grid h-11 w-11 place-items-center rounded-lg border border-border bg-card transition-colors hover:bg-secondary lg:hidden" aria-label="Menü" aria-expanded="false">
				<span id="menu-icon-open"><?php echo elfzwo_icon( 'menu', 'h-5 w-5' ); ?></span>
				<span id="menu-icon-close" class="hidden"><?php echo elfzwo_icon( 'x', 'h-5 w-5' ); ?></span>
			</button>
		</div>
	</div>

	<div id="mobile-menu" class="elfzwo-mobile-menu absolute inset-x-0 top-full hidden max-h-[calc(100dvh-5rem)] overflow-y-auto border-y border-border/60 bg-background shadow-md lg:hidden">
		<div class="mx-auto flex max-w-7xl flex-col gap-1 px-5 py-4">
			<?php foreach ( $elfzwo_nav_items as $elfzwo_mnode ) :
				$elfzwo_mitem = $elfzwo_mnode['item'];
				if ( $elfzwo_mnode['children'] ) :
					?>
					<div>
						<span class="block px-4 pt-3 pb-1 text-xs font-semibold text-muted-foreground"><?php echo esc_html( $elfzwo_mitem->title ); ?></span>
						<div class="ml-2 flex flex-col gap-0.5 border-l border-border pl-3">
							<?php foreach ( $elfzwo_mnode['children'] as $elfzwo_mchild ) : ?>
								<a href="<?php echo esc_url( $elfzwo_mchild->url ); ?>" class="rounded-lg px-3 py-2 text-sm text-foreground/80 hover:bg-secondary hover:text-foreground"><?php echo esc_html( $elfzwo_mchild->title ); ?></a>
							<?php endforeach; ?>
						</div>
					</div>
				<?php else : ?>
					<a href="<?php echo esc_url( $elfzwo_mitem->url ); ?>" class="rounded-xl px-4 py-3 text-base font-medium text-foreground/80 hover:bg-secondary"><?php echo esc_html( $elfzwo_mitem->title ); ?></a>
				<?php
				endif;
			endforeach;
			?>
			<a href="<?php echo esc_url( home_url( '/mitmachen/' ) ); ?>" class="elfzwo-btn elfzwo-btn-primary mt-2">Mach mit!</a>
		</div>
	</div>
</header>

<?php
// Einsätze mit ihrem Einsatzdatum (nicht dem Veröffentlichungsdatum), Beiträge mit dem Datum des Beitrags; die fünf neuesten.
$elfzwo_ticker = array();
foreach ( get_posts( array( 'post_type' => array( 'post', 'einsatz' ), 'numberposts' => 15, 'post_status' => 'publish' ) ) as $elfzwo_tp ) {
	$elfzwo_datum = 'einsatz' === $elfzwo_tp->post_type ? elfzwo_meta( $elfzwo_tp->ID, 'datum', '' ) : '';
	$elfzwo_ticker[] = array(
		'post' => $elfzwo_tp,
		'ts'   => $elfzwo_datum ? strtotime( $elfzwo_datum ) : get_post_time( 'U', false, $elfzwo_tp ),
	);
}
usort( $elfzwo_ticker, function ( $a, $b ) { return $b['ts'] <=> $a['ts']; } );
$elfzwo_ticker = array_slice( $elfzwo_ticker, 0, 5 );
if ( $elfzwo_ticker ) :
	ob_start();
	foreach ( $elfzwo_ticker as $elfzwo_te ) {
		$elfzwo_tp = $elfzwo_te['post'];
		printf(
			'<a href="%1$s" class="inline-flex items-center gap-2 px-6 hover:underline"><strong class="font-semibold">%2$s:</strong> %3$s</a><span aria-hidden="true">•</span>',
			esc_url( get_permalink( $elfzwo_tp ) ),
			'einsatz' === $elfzwo_tp->post_type ? 'Einsatz' : 'Neuigkeit',
			esc_html( get_the_title( $elfzwo_tp ) . ' (' . date_i18n( 'j. F Y', $elfzwo_te['ts'] ) . ')' )
		);
	}
	$elfzwo_ticker_html = ob_get_clean();
	?>
	<div class="elfzwo-ticker overflow-hidden bg-signal text-sm text-white">
		<div class="mx-auto flex max-w-7xl items-center md:px-8">
			<span class="relative z-10 shrink-0 bg-signal py-2 pl-5 pr-3 font-semibold md:pl-0">Neuigkeiten:</span>
			<div class="min-w-0 flex-1 overflow-hidden py-2">
				<div class="elfzwo-ticker-track flex w-max whitespace-nowrap">
					<div class="flex items-center"><?php echo $elfzwo_ticker_html; // phpcs:ignore -- bereits escaped ?></div>
					<div class="flex items-center" aria-hidden="true"><?php echo $elfzwo_ticker_html; // phpcs:ignore -- bereits escaped ?></div>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>
