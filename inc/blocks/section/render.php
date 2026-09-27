<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$class = $attributes['className'] ?? '';
?>
<div class="<?php echo esc_attr( $class ); ?>"><?php echo $content; // phpcs:ignore ?></div>
