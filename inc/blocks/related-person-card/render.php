<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
if ( empty( $attributes['name'] ) ) {
	if ( current_user_can( 'edit_posts' ) ) {
		echo '<p class="border border-dashed border-border p-5 text-sm text-smoke">Ansprechpartner-Karte: kein Name eingetragen.</p>';
	}
	return;
}
echo elfzwo_render_ansprechpartner_karte( $attributes ); // phpcs:ignore -- bereits escaped
