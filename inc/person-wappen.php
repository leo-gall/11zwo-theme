<?php
/**
 * Wappen-Badge (Name + Funktion) als Ersatz für Ansprechpartner-Fotos.
 * Gleiches Design/Geometrie wie das Wappen auf der Startseite.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Rendert das Wappen-SVG für eine Person: Name (ein oder zwei Zeilen)
 * oben, Funktion auf dem unteren Bogen, Vereinswappen mittig.
 */
function elfzwo_person_wappen_svg( $name, $role, $width = 100 ) {
	$height = round( $width * 255 / 198, 2 );

	$name_parts = preg_split( '/\s+/', trim( $name ), 2 );
	$line1      = mb_strtoupper( $name_parts[0] ?? '' );
	$line2      = mb_strtoupper( $name_parts[1] ?? '' );

	$role_primary = trim( explode( '·', $role )[0] ?? '' );
	$role_text    = mb_strtoupper( $role_primary );

	$crest_url = get_template_directory_uri() . '/assets/images/wappen-gemeinde-greifenberg.png';
	$path_id   = 'elfzwo-wappen-path-' . wp_unique_id();

	ob_start();
	?>
	<svg viewBox="0 0 198 255" width="<?php echo esc_attr( $width ); ?>" height="<?php echo esc_attr( $height ); ?>" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" role="img" aria-label="<?php echo esc_attr( trim( $name . ( $role_primary ? ', ' . $role_primary : '' ) ) ); ?>">
		<path fill="#d44c47" stroke="#d44c47" d="M0,0v154.413h0.032c1.33,55.854,45.226,100.704,99.181,100.704c53.954,0,97.85-44.851,99.181-100.704h0.032V0H0z"></path>
		<path fill="none" stroke="#f5f5f5" stroke-width="5" d="M8.504,8.504v144.119h0.029c1.216,52.13,41.35,93.99,90.68,93.99c49.329,0,89.462-41.86,90.68-93.99h0.029V8.504H8.504z"></path>

		<?php if ( $line2 ) : ?>
			<text text-anchor="middle" x="50%" y="38.8857" fill="#f5f5f5" font-family="Arial" font-weight="bold" font-size="26"><?php echo esc_html( $line1 ); ?></text>
			<text text-anchor="middle" x="50%" y="65.8857" fill="#f5f5f5" font-family="Arial" font-weight="bold" font-size="26"><?php echo esc_html( $line2 ); ?></text>
		<?php elseif ( $line1 ) : ?>
			<text text-anchor="middle" x="50%" y="56" fill="#f5f5f5" font-family="Arial" font-weight="bold" font-size="26"><?php echo esc_html( $line1 ); ?></text>
		<?php endif; ?>

		<defs>
			<path id="<?php echo esc_attr( $path_id ); ?>" fill="none" d="M20.7,102.942v50.139h0.025c1.063,43.821,36.104,79.012,79.175,79.012c43.073,0,78.113-35.19,79.176-79.012h0.025v-51.7"></path>
		</defs>
		<?php if ( $role_text ) : ?>
			<text fill="#f5f5f5" font-family="Arial" font-weight="bold" font-size="22" text-anchor="middle">
				<textPath xlink:href="#<?php echo esc_attr( $path_id ); ?>" letter-spacing="1.5" startOffset="50%"><?php echo esc_html( $role_text ); ?></textPath>
			</text>
		<?php endif; ?>

		<g transform="translate(-125.76,-46.34) scale(1.3787)">
			<image xlink:href="<?php echo esc_url( $crest_url ); ?>" x="114.92624" y="81.963554" width="96.197998" height="96.197998" preserveAspectRatio="xMidYMid meet"></image>
		</g>
	</svg>
	<?php
	return ob_get_clean();
}
