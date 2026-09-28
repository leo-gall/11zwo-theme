<?php
/**
 * Einmalige Migration vom alten Theme "feuerwehr-greifenberg" (Praefix ffg)
 * auf "11zwo" (Praefix elfzwo).
 *
 * Vorher unbedingt ein Backup ziehen:
 *   wp db export backup-vor-11zwo.sql
 * Ausfuehren:
 *   wp eval-file wp-content/themes/11zwo/bin/migrate-from-ffg.php
 *
 * Das Skript ist idempotent: ein zweiter Lauf findet nichts mehr zum Umbenennen.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit( 1 );
}

global $wpdb;

$old_slug = 'feuerwehr-greifenberg';
$new_slug = '11zwo';

// 1. Optionen: ffg_* -> elfzwo_*; alte Transients werden verworfen.
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '\\_transient\\_ffg\\_%' OR option_name LIKE '\\_transient\\_timeout\\_ffg\\_%'" );
$options = $wpdb->query( "UPDATE {$wpdb->options} SET option_name = CONCAT('elfzwo_', SUBSTRING(option_name, 5)) WHERE option_name LIKE 'ffg\\_%'" );

// 2. Meta-Keys: ffg_* und _ffg_* -> elfzwo_* und _elfzwo_*.
$meta = 0;
foreach ( array( $wpdb->postmeta, $wpdb->termmeta, $wpdb->usermeta ) as $table ) {
	$meta += (int) $wpdb->query( "UPDATE {$table} SET meta_key = CONCAT('elfzwo_', SUBSTRING(meta_key, 5)) WHERE meta_key LIKE 'ffg\\_%'" );
	$meta += (int) $wpdb->query( "UPDATE {$table} SET meta_key = CONCAT('_elfzwo_', SUBSTRING(meta_key, 6)) WHERE meta_key LIKE '\\_ffg\\_%'" );
}

// 3. Gespeicherte Bloecke im Content: wp:ffg/... und wp-block-ffg-...
$posts = $wpdb->query(
	"UPDATE {$wpdb->posts}
	SET post_content = REPLACE(REPLACE(post_content, 'wp:ffg/', 'wp:elfzwo/'), 'wp-block-ffg-', 'wp-block-elfzwo-')
	WHERE post_content LIKE '%wp:ffg/%' OR post_content LIKE '%wp-block-ffg-%'"
);

// 4. Theme-Mods (Menue-Zuordnung, Logo, ...) uebernehmen und Theme aktivieren.
// Wurde 11zwo schon manuell aktiviert, existieren dort leere Mods (z. B.
// nav_menu_locations = []); die alten Werte fuellen diese Luecken auf.
$old_mods = get_option( "theme_mods_{$old_slug}" );
if ( is_array( $old_mods ) ) {
	$new_mods = get_option( "theme_mods_{$new_slug}", array() );
	$new_mods = is_array( $new_mods ) ? array_filter( $new_mods ) : array();
	update_option( "theme_mods_{$new_slug}", array_merge( $old_mods, $new_mods ) );
}
if ( get_stylesheet() !== $new_slug ) {
	switch_theme( $new_slug );
}

WP_CLI::success( sprintf( 'Optionen: %d, Meta-Keys: %d, Beitraege: %d umbenannt. Aktives Theme: %s', $options, $meta, $posts, get_stylesheet() ) );
