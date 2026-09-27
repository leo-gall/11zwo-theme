<?php
/**
 * Settings API: sitweite, nicht WordPress-native Angaben (Gerätehaus/
 * Kontakt, NINA-Warnungs-PLZ), gebündelt auf einer Einstellungen-Seite.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function elfzwo_register_settings_page() {
	add_options_page(
		'Feuerwehr Greifenberg',
		'Feuerwehr Greifenberg',
		'manage_options',
		'elfzwo-einstellungen',
		'elfzwo_render_settings_page'
	);
}
add_action( 'admin_menu', 'elfzwo_register_settings_page' );

function elfzwo_settings_sections() {
	return array(
		'kontakt' => array(
			'title'  => 'Gerätehaus & Kontakt',
			'intro'  => 'Diese Angaben erscheinen im Footer auf allen Seiten.',
			'fields' => array(
				'elfzwo_geraetehaus_strasse'  => 'Straße & Hausnummer',
				'elfzwo_geraetehaus_plz_ort'  => 'PLZ & Ort',
				'elfzwo_geraetehaus_zeiten'   => 'Öffnungszeiten (Zeile 1)',
				'elfzwo_geraetehaus_zeiten_2' => 'Öffnungszeiten (Zeile 2, klein)',
				'elfzwo_kontakt_email'        => 'Allgemeine Kontakt-E-Mail',
				'elfzwo_footer_claim'         => 'Footer-Slogan (handschriftlich)',
				'elfzwo_footer_text'          => 'Footer-Beschreibungstext',
				'elfzwo_gegruendet'           => 'Gegründet (Jahr, für "Seit …")',
			),
		),
		'nina'    => array(
			'title'  => 'Gefahrenwarnungen (NINA)',
			'intro'  => 'Postleitzahl, für die auf der Seite „Einsätze & Aktuelles" echte Warnmeldungen der NINA-Warn-App des Bundes angezeigt werden.',
			'fields' => array(
				'elfzwo_nina_plz' => 'Postleitzahl',
			),
		),
		'footer_ansprechpartner' => array(
			'title'  => 'Footer-Ansprechpartner',
			'intro'  => 'Diese bis zu 3 Personen erscheinen unten im Footer auf jeder Seite. Leer lassen, um eine Person auszublenden.',
			'fields' => array(
				'elfzwo_footer_person1_name'    => 'Person 1 — Name',
				'elfzwo_footer_person1_rolle'   => 'Person 1 — Rolle',
				'elfzwo_footer_person1_telefon' => 'Person 1 — Telefon',
				'elfzwo_footer_person1_email'   => 'Person 1 — E-Mail',
				'elfzwo_footer_person2_name'    => 'Person 2 — Name',
				'elfzwo_footer_person2_rolle'   => 'Person 2 — Rolle',
				'elfzwo_footer_person2_telefon' => 'Person 2 — Telefon',
				'elfzwo_footer_person2_email'   => 'Person 2 — E-Mail',
				'elfzwo_footer_person3_name'    => 'Person 3 — Name',
				'elfzwo_footer_person3_rolle'   => 'Person 3 — Rolle',
				'elfzwo_footer_person3_telefon' => 'Person 3 — Telefon',
				'elfzwo_footer_person3_email'   => 'Person 3 — E-Mail',
			),
		),
		'mitmachen' => array(
			'title'  => 'Mach-mit-Anfragen',
			'intro'  => 'Wird jemand über das „Mach mit"-Formular auf der Website aktiv, geht direkt eine E-Mail an die untenstehenden Empfänger raus — es wird nichts mehr im Backend gespeichert.',
			'fields' => array(
				'elfzwo_mitmachen_empfaenger' => 'Empfänger (eine E-Mail-Adresse pro Zeile)',
				'elfzwo_mitmachen_betreff'    => 'Betreff der E-Mail',
				'elfzwo_mitmachen_template'   => 'Text der E-Mail',
			),
		),
	);
}

function elfzwo_settings_fields() {
	$fields = array();
	foreach ( elfzwo_settings_sections() as $section ) {
		$fields = array_merge( $fields, $section['fields'] );
	}
	return $fields;
}

function elfzwo_register_settings() {
	foreach ( elfzwo_settings_fields() as $key => $label ) {
		register_setting( 'elfzwo_einstellungen_group', $key, array( 'sanitize_callback' => 'sanitize_text_field' ) );
	}
	register_setting( 'elfzwo_einstellungen_group', 'elfzwo_footer_text', array( 'sanitize_callback' => 'sanitize_textarea_field' ) );
	register_setting( 'elfzwo_einstellungen_group', 'elfzwo_nina_plz', array( 'sanitize_callback' => 'elfzwo_sanitize_plz' ) );
	register_setting( 'elfzwo_einstellungen_group', 'elfzwo_mitmachen_empfaenger', array( 'sanitize_callback' => 'sanitize_textarea_field' ) );
	register_setting( 'elfzwo_einstellungen_group', 'elfzwo_mitmachen_template', array( 'sanitize_callback' => 'sanitize_textarea_field' ) );
}
add_action( 'admin_init', 'elfzwo_register_settings' );

function elfzwo_sanitize_plz( $value ) {
	$digits = preg_replace( '/\D/', '', (string) $value );
	return substr( $digits, 0, 5 );
}

function elfzwo_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h1>Feuerwehr Greifenberg &ndash; Einstellungen</h1>
		<form method="post" action="options.php">
			<?php settings_fields( 'elfzwo_einstellungen_group' ); ?>
			<?php foreach ( elfzwo_settings_sections() as $section ) : ?>
				<h2><?php echo esc_html( $section['title'] ); ?></h2>
				<?php if ( ! empty( $section['intro'] ) ) : ?><p><?php echo esc_html( $section['intro'] ); ?></p><?php endif; ?>
				<table class="form-table">
					<?php foreach ( $section['fields'] as $key => $label ) : ?>
						<tr>
							<th style="width:280px;text-align:left;"><label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label></th>
							<td>
								<?php if ( in_array( $key, array( 'elfzwo_footer_text', 'elfzwo_mitmachen_empfaenger', 'elfzwo_mitmachen_template' ), true ) ) : ?>
									<textarea id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>" rows="<?php echo 'elfzwo_footer_text' === $key ? '3' : '4'; ?>" class="large-text"><?php echo esc_textarea( get_option( $key ) ); ?></textarea>
									<?php if ( 'elfzwo_mitmachen_template' === $key ) : ?>
										<p class="description">Platzhalter: <code>{name}</code>, <code>{kontakt}</code>, <code>{interesse}</code></p>
									<?php endif; ?>
								<?php elseif ( 'elfzwo_nina_plz' === $key ) : ?>
									<input type="text" id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( get_option( $key ) ); ?>" class="regular-text" pattern="\d{5}" maxlength="5" placeholder="86926" />
								<?php else : ?>
									<input type="text" id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( get_option( $key ) ); ?>" class="regular-text" />
									<?php if ( 'elfzwo_mitmachen_betreff' === $key ) : ?>
										<p class="description">Platzhalter: <code>{name}</code></p>
									<?php endif; ?>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</table>
			<?php endforeach; ?>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}

function elfzwo_option( $key, $default = '' ) {
	$value = get_option( $key );
	return ( '' === $value || false === $value ) ? $default : $value;
}
