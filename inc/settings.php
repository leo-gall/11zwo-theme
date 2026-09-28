<?php
/**
 * Sitweite, nicht WordPress-native Angaben: Die Footer-Inhalte (Gerätehaus/
 * Kontakt, Ansprechpartner) werden direkt unter "Design → Menüs" gepflegt.
 * Die Mach-mit-Einstellungen liegen im Block "Mach-mit-Formular".
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Unterfelder einer Footer-Ansprechperson (Repeater-Zeile). */
function elfzwo_footer_person_subfields() {
	return array(
		'name'    => array( 'placeholder' => 'Name' ),
		'rolle'   => array( 'placeholder' => 'Rolle' ),
		'telefon' => array( 'placeholder' => 'Telefon' ),
		'email'   => array( 'placeholder' => 'E-Mail' ),
	);
}

/** Footer-Felder "Gerätehaus & Kontakt" (Design → Menüs). */
function elfzwo_footer_kontakt_fields() {
	return array(
		'elfzwo_geraetehaus_strasse'  => 'Straße & Hausnummer',
		'elfzwo_geraetehaus_plz_ort'  => 'PLZ & Ort',
		'elfzwo_geraetehaus_zeiten'   => 'Öffnungszeiten (Zeile 1)',
		'elfzwo_geraetehaus_zeiten_2' => 'Öffnungszeiten (Zeile 2, klein)',
		'elfzwo_kontakt_email'        => 'Allgemeine Kontakt-E-Mail',
		'elfzwo_footer_claim'         => 'Footer-Slogan (handschriftlich)',
		'elfzwo_footer_text'          => 'Footer-Beschreibungstext',
		'elfzwo_gegruendet'           => 'Gegründet (Jahr, für "Seit …")',
	);
}

function elfzwo_option_sanitizer( $key ) {
	return 'elfzwo_footer_text' === $key ? 'sanitize_textarea_field' : 'sanitize_text_field';
}

function elfzwo_sanitize_footer_personen( $value ) {
	if ( ! is_array( $value ) ) {
		return array();
	}
	$personen = array();
	foreach ( $value as $row ) {
		$person = array(
			'name'    => sanitize_text_field( $row['name'] ?? '' ),
			'rolle'   => sanitize_text_field( $row['rolle'] ?? '' ),
			'telefon' => sanitize_text_field( $row['telefon'] ?? '' ),
			'email'   => sanitize_email( $row['email'] ?? '' ),
		);
		if ( '' !== $person['name'] ) {
			$personen[] = $person;
		}
	}
	return $personen;
}

/**
 * Footer-Ansprechpartner als Liste. Solange die neue Liste noch nie
 * gespeichert wurde, werden die früheren festen Felder (Person 1–3)
 * übernommen, damit der Footer nach dem Update nicht leer ist.
 */
function elfzwo_footer_personen() {
	$personen = get_option( 'elfzwo_footer_personen', null );
	if ( is_array( $personen ) ) {
		return $personen;
	}

	$personen = array();
	for ( $i = 1; $i <= 3; $i++ ) {
		$name = elfzwo_option( "elfzwo_footer_person{$i}_name", '' );
		if ( '' === $name ) {
			continue;
		}
		$personen[] = array(
			'name'    => $name,
			'rolle'   => elfzwo_option( "elfzwo_footer_person{$i}_rolle", '' ),
			'telefon' => elfzwo_option( "elfzwo_footer_person{$i}_telefon", '' ),
			'email'   => elfzwo_option( "elfzwo_footer_person{$i}_email", '' ),
		);
	}
	return $personen;
}

/**
 * Footer-Inhalte als eigene Boxen in der linken Spalte von "Design → Menüs".
 * Der Menü-Editor unterbindet das Absenden dieses Formulars, daher speichert
 * jede Box per AJAX (siehe admin-footer-settings.js).
 */
function elfzwo_footer_menu_boxes() {
	add_meta_box( 'elfzwo-footer-kontakt', 'Footer: Gerätehaus & Kontakt', 'elfzwo_render_footer_kontakt_box', 'nav-menus', 'side', 'low' );
	add_meta_box( 'elfzwo-footer-personen', 'Footer: Ansprechpartner', 'elfzwo_render_footer_personen_box', 'nav-menus', 'side', 'low' );
}
add_action( 'load-nav-menus.php', 'elfzwo_footer_menu_boxes' );

/** Footer-Boxen nie unter "Ansicht anpassen" verstecken lassen, auch nicht beim ersten Aufruf. */
function elfzwo_footer_menu_boxes_visible( $hidden, $screen ) {
	if ( $screen && 'nav-menus' === $screen->id ) {
		$hidden = array_values( array_diff( (array) $hidden, array( 'elfzwo-footer-kontakt', 'elfzwo-footer-personen' ) ) );
	}
	return $hidden;
}
add_filter( 'hidden_meta_boxes', 'elfzwo_footer_menu_boxes_visible', 10, 2 );

function elfzwo_footer_box_save_button( $box ) {
	?>
	<p class="button-controls wp-clearfix">
		<span class="add-to-menu">
			<span class="elfzwo-footer-status" aria-live="polite"></span>
			<span class="spinner"></span>
			<button type="button" class="button elfzwo-footer-save" data-box="<?php echo esc_attr( $box ); ?>">Speichern</button>
		</span>
	</p>
	<?php
}

function elfzwo_render_footer_kontakt_box() {
	?>
	<div class="elfzwo-footer-box">
		<p class="description">Diese Angaben erscheinen im Footer auf allen Seiten.</p>
		<?php foreach ( elfzwo_footer_kontakt_fields() as $key => $label ) : ?>
			<p>
				<label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label>
				<?php if ( 'elfzwo_footer_text' === $key ) : ?>
					<textarea id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>" rows="4" class="widefat"><?php echo esc_textarea( get_option( $key ) ); ?></textarea>
				<?php else : ?>
					<input type="text" id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( get_option( $key ) ); ?>" class="widefat" />
				<?php endif; ?>
			</p>
		<?php endforeach; ?>
		<?php elfzwo_footer_box_save_button( 'kontakt' ); ?>
	</div>
	<?php
}

function elfzwo_render_footer_personen_box() {
	$key       = 'elfzwo_footer_personen';
	$subfields = elfzwo_footer_person_subfields();
	?>
	<div class="elfzwo-footer-box">
		<p class="description">Diese Personen erscheinen im Footer auf jeder Seite, in dieser Reihenfolge. Einträge ohne Namen werden beim Speichern entfernt.</p>
		<div class="elfzwo-repeater">
			<div class="elfzwo-repeater-rows">
				<?php foreach ( elfzwo_footer_personen() as $i => $person ) : ?>
					<?php echo elfzwo_render_repeater_row( $key, $i, $subfields, $person, '' ); // phpcs:ignore -- bereits escaped ?>
				<?php endforeach; ?>
			</div>
			<button type="button" class="button elfzwo-repeater-add">+ Person hinzufügen</button>
			<template class="elfzwo-repeater-template">
				<?php echo elfzwo_render_repeater_row( $key, '__INDEX__', $subfields, array(), '' ); // phpcs:ignore -- bereits escaped ?>
			</template>
		</div>
		<?php elfzwo_footer_box_save_button( 'personen' ); ?>
	</div>
	<?php
}

function elfzwo_footer_menu_assets( $hook ) {
	if ( 'nav-menus.php' !== $hook ) {
		return;
	}
	wp_enqueue_style( 'elfzwo-admin', get_template_directory_uri() . '/assets/css/admin.css', array(), filemtime( get_template_directory() . '/assets/css/admin.css' ) );
	wp_enqueue_script( 'elfzwo-admin-repeater', get_template_directory_uri() . '/assets/js/admin-repeater.js', array(), filemtime( get_template_directory() . '/assets/js/admin-repeater.js' ), true );
	wp_enqueue_script( 'elfzwo-admin-footer-settings', get_template_directory_uri() . '/assets/js/admin-footer-settings.js', array(), filemtime( get_template_directory() . '/assets/js/admin-footer-settings.js' ), true );
	wp_localize_script(
		'elfzwo-admin-footer-settings',
		'elfzwoFooterSettings',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'elfzwo_footer_save' ),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'elfzwo_footer_menu_assets' );

function elfzwo_ajax_save_footer() {
	check_ajax_referer( 'elfzwo_footer_save', 'nonce' );
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		wp_send_json_error( 'Keine Berechtigung.', 403 );
	}

	$box = sanitize_key( $_POST['box'] ?? '' );
	if ( 'kontakt' === $box ) {
		foreach ( elfzwo_footer_kontakt_fields() as $key => $label ) {
			if ( isset( $_POST[ $key ] ) ) {
				$sanitize = elfzwo_option_sanitizer( $key );
				update_option( $key, $sanitize( wp_unslash( $_POST[ $key ] ) ) );
			}
		}
	} elseif ( 'personen' === $box ) {
		update_option( 'elfzwo_footer_personen', elfzwo_sanitize_footer_personen( wp_unslash( $_POST['elfzwo_footer_personen'] ?? array() ) ) ); // phpcs:ignore -- in elfzwo_sanitize_footer_personen() bereinigt
	} else {
		wp_send_json_error( 'Unbekannter Bereich.', 400 );
	}

	wp_send_json_success();
}
add_action( 'wp_ajax_elfzwo_save_footer', 'elfzwo_ajax_save_footer' );

function elfzwo_option( $key, $default = '' ) {
	$value = get_option( $key );
	return ( '' === $value || false === $value ) ? $default : $value;
}
