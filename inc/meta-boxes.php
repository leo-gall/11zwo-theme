<?php
/**
 * Generisches Meta-Box-Framework: Felder werden pro Post-Type oder Page-Template
 * deklarativ definiert (elfzwo_meta_box_schemas) und automatisch gerendert/gespeichert.
 * Ersetzt ein ACF-artiges Repeater-Feld durch feste, klar benannte Einzelfelder —
 * das reicht für die überschaubare, feste Anzahl an Karten/Listenpunkten pro Seite.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function elfzwo_meta_box_schemas() {
	return array(
		// ---------------------------------------------------------------- Fahrzeug
		'post_type:fahrzeug' => array(
			array(
				'id'     => 'elfzwo_fahrzeug_details',
				'title'  => 'Fahrzeug-Details',
				'fields' => array(
					array( 'key' => 'tag', 'label' => 'Funkrufname', 'type' => 'text', 'placeholder' => 'Florian Greifenberg 40/1' ),
				),
			),
			array(
				'id'     => 'elfzwo_fahrzeug_specs',
				'title'  => 'Technische Daten',
				'fields' => array(
					array( 'key' => 'besatzung', 'label' => 'Besatzung', 'type' => 'text', 'placeholder' => '1 + 8' ),
					array( 'key' => 'hersteller_aufbau', 'label' => 'Hersteller / Aufbau', 'type' => 'text', 'placeholder' => 'Rosenbauer' ),
					array( 'key' => 'baujahr', 'label' => 'Baujahr', 'type' => 'text', 'placeholder' => '2011' ),
					array( 'key' => 'besonderheiten', 'label' => 'Besonderheiten', 'type' => 'textarea' ),
				),
			),
		),

		// ------------------------------------------------------------------ Einsatz
		'post_type:einsatz' => array(
			array(
				'id'     => 'elfzwo_einsatz_details',
				'title'  => 'Einsatzdaten',
				'fields' => array(
					array( 'key' => 'stichwort', 'label' => 'Einsatzstichwort', 'type' => 'taxonomy_select', 'taxonomy' => 'einsatzstichwort' ),
					array( 'key' => 'datum', 'label' => 'Datum & Uhrzeit', 'type' => 'datetime' ),
					array( 'key' => 'einsatznummer', 'label' => 'Einsatz-Nr. im Jahr', 'type' => 'readonly', 'placeholder' => 'wird beim Speichern automatisch vergeben' ),
					array( 'key' => 'ort', 'label' => 'Einsatzort', 'type' => 'taxonomy_select', 'taxonomy' => 'einsatzort' ),
					array( 'key' => 'fahrzeuge', 'label' => 'Eingesetzte Fahrzeuge', 'type' => 'post_multiselect', 'post_type' => 'fahrzeug' ),
					array( 'key' => 'einsatzkraefte', 'label' => 'Externe Kräfte', 'type' => 'post_multiselect', 'post_type' => 'externe_kraft' ),
				),
			),
		),

		// ------------------------------------------------------------------- Termin
		'post_type:termin' => array(
			array(
				'id'     => 'elfzwo_termin_details',
				'title'  => 'Termin-Details',
				'fields' => array(
					array( 'key' => 'datum', 'label' => 'Datum', 'type' => 'date' ),
					array( 'key' => 'zeit', 'label' => 'Uhrzeit', 'type' => 'text', 'placeholder' => '18:00 - 22:00' ),
					array( 'key' => 'ort', 'label' => 'Ort', 'type' => 'text' ),
					array( 'key' => 'kurzbeschreibung', 'label' => 'Kurzbeschreibung', 'type' => 'textarea' ),
				),
			),
		),

		// ----------------------------------------------------------- Externe Kraft
		'post_type:externe_kraft' => array(
			array(
				'id'     => 'elfzwo_externe_kraft_details',
				'title'  => 'Details',
				'fields' => array(
					array( 'key' => 'url', 'label' => 'Link (optional)', 'type' => 'text', 'placeholder' => 'https://…' ),
				),
			),
		),

		// ----------------------------------------------------------------- Download
		'post_type:download' => array(
			array(
				'id'     => 'elfzwo_download_details',
				'title'  => 'Datei',
				'fields' => array(
					array( 'key' => 'datei_id', 'label' => 'Datei', 'type' => 'media' ),
					array( 'key' => 'beschreibung', 'label' => 'Beschreibung', 'type' => 'textarea' ),
				),
			),
		),

		// -------------------------------------------------------- Beitrag (Aktuelles)
		'post_type:post' => array(
			array(
				'id'     => 'elfzwo_post_details',
				'title'  => 'Aktuelles — Zusatzangaben',
				'fields' => array(
					array( 'key' => 'einsatz_bezug', 'label' => 'Zugehöriger Einsatz (optional)', 'type' => 'post_select', 'post_type' => 'einsatz' ),
					array( 'key' => 'bilder', 'label' => 'Weitere Bilder (werden zusammen mit dem Beitragsbild als Carousel angezeigt)', 'type' => 'media_gallery' ),
				),
			),
		),

	);
}

function elfzwo_current_meta_contexts( $post ) {
	$contexts = array( 'post_type:' . $post->post_type );
	if ( 'page' === $post->post_type ) {
		$template = get_page_template_slug( $post );
		if ( $template ) {
			$contexts[] = 'page_template:' . $template;
		}
	}
	return $contexts;
}

function elfzwo_add_meta_boxes( $post_type, $post ) {
	$schemas  = elfzwo_meta_box_schemas();
	$contexts = elfzwo_current_meta_contexts( $post );

	foreach ( $contexts as $context ) {
		if ( empty( $schemas[ $context ] ) ) {
			continue;
		}
		foreach ( $schemas[ $context ] as $box ) {
			add_meta_box(
				$box['id'],
				$box['title'],
				'elfzwo_render_meta_box',
				$post_type,
				'normal',
				in_array( $post_type, array( 'einsatz', 'fahrzeug', 'download', 'post', 'externe_kraft' ), true ) ? 'high' : 'default',
				array( 'fields' => $box['fields'] )
			);
		}
	}
}
add_action( 'add_meta_boxes', 'elfzwo_add_meta_boxes', 10, 2 );

function elfzwo_render_meta_box( $post, $box ) {
	wp_nonce_field( 'elfzwo_save_meta_' . $post->ID, 'elfzwo_meta_nonce' );
	$fields = $box['args']['fields'];
	echo '<table class="form-table"><tbody>';
	foreach ( $fields as $field ) {
		if ( 'hint' === $field['type'] ) {
			printf( '<tr><td colspan="2"><p class="description">%s: <code>%s</code></p></td></tr>', esc_html( $field['label'] ), esc_html( $field['text'] ) );
			continue;
		}
		if ( 'taxonomy_select' === $field['type'] ) {
			$current = wp_get_object_terms( $post->ID, $field['taxonomy'], array( 'fields' => 'ids' ) );
			$value   = $current && ! is_wp_error( $current ) ? (string) $current[0] : '';
		} else {
			$value = get_post_meta( $post->ID, '_elfzwo_' . $field['key'], true );
		}
		echo '<tr>';
		printf( '<th style="width:280px;text-align:left;"><label for="elfzwo_%s">%s</label></th>', esc_attr( $field['key'] ), esc_html( $field['label'] ) );
		echo '<td>';
		elfzwo_render_meta_field( $field, $value );
		echo '</td></tr>';
	}
	echo '</tbody></table>';
}

function elfzwo_render_meta_field( $field, $value ) {
	$id   = 'elfzwo_' . $field['key'];
	$name = 'elfzwo_meta[' . $field['key'] . ']';

	switch ( $field['type'] ) {
		case 'textarea':
			printf(
				'<textarea id="%1$s" name="%2$s" rows="4" class="large-text">%3$s</textarea>',
				esc_attr( $id ),
				esc_attr( $name ),
				esc_textarea( $value )
			);
			break;

		case 'checkbox':
			printf(
				'<input type="checkbox" id="%1$s" name="%2$s" value="1" %3$s />',
				esc_attr( $id ),
				esc_attr( $name ),
				checked( $value, '1', false )
			);
			break;

		case 'number':
			printf(
				'<input type="number" id="%1$s" name="%2$s" value="%3$s" class="small-text" />',
				esc_attr( $id ),
				esc_attr( $name ),
				esc_attr( $value )
			);
			break;

		case 'readonly':
			printf(
				'<p class="description" style="margin:6px 0;font-size:14px;">%1$s</p>',
				$value ? '<strong>' . esc_html( $value ) . '</strong>' : esc_html( $field['placeholder'] ?? '' )
			);
			break;

		case 'date':
			printf(
				'<input type="date" id="%1$s" name="%2$s" value="%3$s" />',
				esc_attr( $id ),
				esc_attr( $name ),
				esc_attr( $value )
			);
			break;

		case 'datetime':
			printf(
				'<input type="datetime-local" id="%1$s" name="%2$s" value="%3$s" />',
				esc_attr( $id ),
				esc_attr( $name ),
				esc_attr( $value )
			);
			break;

		case 'select':
			printf( '<select id="%1$s" name="%2$s">', esc_attr( $id ), esc_attr( $name ) );
			foreach ( $field['options'] as $opt_value => $opt_label ) {
				printf(
					'<option value="%1$s" %2$s>%3$s</option>',
					esc_attr( $opt_value ),
					selected( $value, $opt_value, false ),
					esc_html( $opt_label )
				);
			}
			echo '</select>';
			break;

		case 'post_select':
			$posts = get_posts( array( 'post_type' => $field['post_type'], 'numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC' ) );
			printf( '<select id="%1$s" name="%2$s">', esc_attr( $id ), esc_attr( $name ) );
			echo '<option value="">— keine Auswahl —</option>';
			foreach ( $posts as $p ) {
				printf(
					'<option value="%1$s" %2$s>%3$s</option>',
					esc_attr( $p->ID ),
					selected( $value, (string) $p->ID, false ),
					esc_html( get_the_title( $p ) )
				);
			}
			echo '</select>';
			break;

		case 'taxonomy_select':
			if ( ! is_taxonomy_hierarchical( $field['taxonomy'] ) ) {
				// Flache Taxonomie (z. B. Einsatzort): einfaches <select>, im Backend per Taxonomie-Verwaltung erweiterbar.
				$terms = get_terms( array( 'taxonomy' => $field['taxonomy'], 'hide_empty' => false, 'orderby' => 'name' ) );
				printf( '<select id="%1$s" name="%2$s">', esc_attr( $id ), esc_attr( $name ) );
				echo '<option value="">— keine Auswahl —</option>';
				foreach ( $terms as $term ) {
					printf(
						'<option value="%1$s" %2$s>%3$s</option>',
						esc_attr( $term->term_id ),
						selected( (string) $value, (string) $term->term_id, false ),
						esc_html( $term->name )
					);
				}
				echo '</select>';
				break;
			}
			$groups  = get_terms(
				array(
					'taxonomy'   => $field['taxonomy'],
					'hide_empty' => false,
					'parent'     => 0,
					'orderby'    => 'name',
				)
			);
			$options   = array();
			$current   = '';
			foreach ( $groups as $group ) {
				$children = get_terms(
					array(
						'taxonomy'   => $field['taxonomy'],
						'hide_empty' => false,
						'parent'     => $group->term_id,
						'orderby'    => 'name',
					)
				);
				if ( ! $children ) {
					// Top-Level-Begriff ganz ohne Kinder (z. B. "THL RETTUNGSKORB
					// RD Drehleiter") -- sonst taucht er in dieser Auswahl nirgends
					// auf, obwohl er ein gültiger, zuweisbarer Begriff ist.
					$options[] = array(
						'id'    => $group->term_id,
						'name'  => $group->name,
						'group' => $group->name,
					);
					if ( (string) $group->term_id === (string) $value ) {
						$current = $group->name;
					}
					continue;
				}
				foreach ( $children as $child ) {
					$options[] = array(
						'id'    => $child->term_id,
						'name'  => $child->name,
						'group' => $group->name,
					);
					if ( (string) $child->term_id === (string) $value ) {
						$current = $child->name;
					}
				}
			}
			?>
			<div class="elfzwo-combobox">
				<input type="hidden" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>">
				<input type="text" class="large-text elfzwo-combobox-input" id="<?php echo esc_attr( $id ); ?>_search" value="<?php echo esc_attr( $current ); ?>" placeholder="Stichwort suchen …" autocomplete="off">
				<div class="elfzwo-combobox-results"></div>
				<script type="application/json" class="elfzwo-combobox-data"><?php echo wp_json_encode( $options ); ?></script>
			</div>
			<?php
			break;

		case 'post_multiselect':
			$selected = $value ? array_map( 'intval', explode( ',', $value ) ) : array();
			$posts    = get_posts( array( 'post_type' => $field['post_type'], 'numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC' ) );
			if ( ! $posts ) {
				printf( '<p class="description">Keine %s vorhanden.</p>', esc_html( $field['post_type'] ) );
				break;
			}
			echo '<div class="elfzwo-checkbox-list">';
			foreach ( $posts as $p ) {
				$sub_label = elfzwo_meta( $p->ID, 'tag', '' );
				printf(
					'<label class="elfzwo-checkbox-list-item"><input type="checkbox" name="%1$s[]" value="%2$s" %3$s /> %4$s%5$s</label>',
					esc_attr( $name ),
					esc_attr( $p->ID ),
					checked( in_array( $p->ID, $selected, true ), true, false ),
					esc_html( get_the_title( $p ) ),
					$sub_label ? ' <span class="description">(' . esc_html( $sub_label ) . ')</span>' : ''
				);
			}
			echo '</div>';
			break;

		case 'repeater':
			$rows = $value ? json_decode( $value, true ) : array();
			if ( ! is_array( $rows ) ) {
				$rows = array();
			}
			$subfields    = $field['subfields'];
			$sub_keys     = array_keys( $subfields );
			$primary_key  = $sub_keys[0];
			$suggestions  = ! empty( $field['suggest'] ) ? elfzwo_repeater_suggestions_get( $field['suggest'] ) : array();
			?>
			<div class="elfzwo-repeater">
				<?php if ( $suggestions ) : ?>
					<script type="application/json" class="elfzwo-repeater-suggestions"><?php echo wp_json_encode( $suggestions ); ?></script>
				<?php endif; ?>
				<div class="elfzwo-repeater-rows">
					<?php foreach ( $rows as $i => $row ) : ?>
						<?php echo elfzwo_render_repeater_row( $name, $i, $subfields, $row, $suggestions ? $primary_key : '' ); // phpcs:ignore -- bereits escaped ?>
					<?php endforeach; ?>
				</div>
				<button type="button" class="button elfzwo-repeater-add">+ hinzufügen</button>
				<template class="elfzwo-repeater-template">
					<?php echo elfzwo_render_repeater_row( $name, '__INDEX__', $subfields, array(), $suggestions ? $primary_key : '' ); // phpcs:ignore -- bereits escaped ?>
				</template>
			</div>
			<?php
			break;

		case 'media':
			$image_html = $value ? wp_get_attachment_image( $value, 'thumbnail' ) : '';
			printf(
				'<div class="elfzwo-media-field">
					<div class="elfzwo-media-preview">%1$s</div>
					<input type="hidden" class="elfzwo-media-input" id="%2$s" name="%3$s" value="%4$s" />
					<button type="button" class="button elfzwo-media-select">Datei auswählen</button>
					<button type="button" class="button elfzwo-media-remove" %5$s>Entfernen</button>
				</div>',
				$image_html,
				esc_attr( $id ),
				esc_attr( $name ),
				esc_attr( $value ),
				$value ? '' : 'style="display:none"'
			);
			break;

		case 'media_gallery':
			$gallery_ids = $value ? array_filter( array_map( 'intval', explode( ',', $value ) ) ) : array();
			?>
			<div class="elfzwo-gallery-field">
				<input type="hidden" class="elfzwo-gallery-input" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( implode( ',', $gallery_ids ) ); ?>" />
				<div class="elfzwo-gallery-preview">
					<?php foreach ( $gallery_ids as $gallery_id ) : ?>
						<div class="elfzwo-gallery-item" data-id="<?php echo esc_attr( $gallery_id ); ?>">
							<?php echo wp_get_attachment_image( $gallery_id, 'thumbnail' ); ?>
							<button type="button" class="elfzwo-gallery-item-remove" title="Entfernen">&times;</button>
						</div>
					<?php endforeach; ?>
				</div>
				<button type="button" class="button elfzwo-gallery-select">Bilder auswählen</button>
				<p class="description">Reihenfolge der Auswahl bestimmt die Reihenfolge im Carousel.</p>
			</div>
			<?php
			break;

		default:
			printf(
				'<input type="text" id="%1$s" name="%2$s" value="%3$s" class="large-text" placeholder="%4$s" />',
				esc_attr( $id ),
				esc_attr( $name ),
				esc_attr( $value ),
				esc_attr( $field['placeholder'] ?? '' )
			);
			break;
	}
}

/**
 * Baut eine einzelne Repeater-Zeile. Ist eine Vorschlagsliste (Registry)
 * hinterlegt, wird das erste Unterfeld als Such-Combobox gerendert, die
 * beim Auswählen auch benachbarte Felder (z. B. den Link) mit ausfüllt.
 */
function elfzwo_render_repeater_row( $name, $index, $subfields, $row, $suggest_key ) {
	ob_start();
	?>
	<div class="elfzwo-repeater-row">
		<?php foreach ( $subfields as $sub_key => $sub ) : ?>
			<?php if ( $sub_key === $suggest_key ) : ?>
				<div class="elfzwo-partner-combobox">
					<input type="text" name="<?php echo esc_attr( $name . '[' . $index . '][' . $sub_key . ']' ); ?>" value="<?php echo esc_attr( $row[ $sub_key ] ?? '' ); ?>" placeholder="<?php echo esc_attr( $sub['placeholder'] ?? '' ); ?>" class="regular-text elfzwo-partner-combobox-input" data-subfield="<?php echo esc_attr( $sub_key ); ?>" autocomplete="off" />
					<div class="elfzwo-combobox-results"></div>
				</div>
			<?php else : ?>
				<input type="text" name="<?php echo esc_attr( $name . '[' . $index . '][' . $sub_key . ']' ); ?>" value="<?php echo esc_attr( $row[ $sub_key ] ?? '' ); ?>" placeholder="<?php echo esc_attr( $sub['placeholder'] ?? '' ); ?>" class="regular-text" data-subfield="<?php echo esc_attr( $sub_key ); ?>" />
			<?php endif; ?>
		<?php endforeach; ?>
		<button type="button" class="button elfzwo-repeater-remove">Entfernen</button>
	</div>
	<?php
	return ob_get_clean();
}

/** Gespeicherte Vorschlagsliste (z. B. schon einmal erfasste Partner-Einsatzkräfte) für Repeater-Comboboxen. */
function elfzwo_repeater_suggestions_get( $option_name ) {
	$list = get_option( $option_name, array() );
	return is_array( $list ) ? $list : array();
}

/** Merkt sich neue/geänderte Repeater-Zeilen (nach Name dedupliziert) für spätere Wiederverwendung per Combobox. */
function elfzwo_repeater_suggestions_remember( $option_name, $rows ) {
	$by_name = array();
	foreach ( elfzwo_repeater_suggestions_get( $option_name ) as $item ) {
		if ( ! empty( $item['name'] ) ) {
			$by_name[ mb_strtolower( trim( $item['name'] ) ) ] = $item;
		}
	}
	foreach ( $rows as $row ) {
		if ( empty( $row['name'] ) ) {
			continue;
		}
		$by_name[ mb_strtolower( trim( $row['name'] ) ) ] = array(
			'name' => $row['name'],
			'url'  => $row['url'] ?? '',
		);
	}
	ksort( $by_name );
	update_option( $option_name, array_values( $by_name ) );
}

function elfzwo_save_meta_boxes( $post_id, $post ) {
	if ( ! isset( $_POST['elfzwo_meta_nonce'] ) || ! wp_verify_nonce( $_POST['elfzwo_meta_nonce'], 'elfzwo_save_meta_' . $post_id ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$schemas  = elfzwo_meta_box_schemas();
	$contexts = elfzwo_current_meta_contexts( $post );
	$posted   = isset( $_POST['elfzwo_meta'] ) && is_array( $_POST['elfzwo_meta'] ) ? wp_unslash( $_POST['elfzwo_meta'] ) : array();

	foreach ( $contexts as $context ) {
		if ( empty( $schemas[ $context ] ) ) {
			continue;
		}
		foreach ( $schemas[ $context ] as $box ) {
			foreach ( $box['fields'] as $field ) {
				if ( 'hint' === $field['type'] ) {
					continue;
				}
				$key      = $field['key'];
				$meta_key = '_elfzwo_' . $key;

				if ( 'checkbox' === $field['type'] ) {
					update_post_meta( $post_id, $meta_key, isset( $posted[ $key ] ) ? '1' : '0' );
					continue;
				}
				if ( 'post_multiselect' === $field['type'] ) {
					$ids = isset( $posted[ $key ] ) && is_array( $posted[ $key ] ) ? array_map( 'intval', $posted[ $key ] ) : array();
					update_post_meta( $post_id, $meta_key, implode( ',', $ids ) );
					continue;
				}
				if ( 'media_gallery' === $field['type'] ) {
					$ids = isset( $posted[ $key ] ) ? array_filter( array_map( 'intval', explode( ',', $posted[ $key ] ) ) ) : array();
					update_post_meta( $post_id, $meta_key, implode( ',', $ids ) );
					continue;
				}
				if ( 'repeater' === $field['type'] ) {
					$rows = array();
					if ( isset( $posted[ $key ] ) && is_array( $posted[ $key ] ) ) {
						foreach ( $posted[ $key ] as $row ) {
							$row_name = isset( $row['name'] ) ? sanitize_text_field( $row['name'] ) : '';
							if ( '' === $row_name ) {
								continue;
							}
							$rows[] = array(
								'name' => $row_name,
								'url'  => isset( $row['url'] ) && $row['url'] ? esc_url_raw( $row['url'] ) : '',
							);
						}
					}
					update_post_meta( $post_id, $meta_key, wp_json_encode( $rows, JSON_UNESCAPED_UNICODE ) );
					if ( ! empty( $field['suggest'] ) ) {
						elfzwo_repeater_suggestions_remember( $field['suggest'], $rows );
					}
					continue;
				}
				if ( ! isset( $posted[ $key ] ) ) {
					continue;
				}
				$raw = $posted[ $key ];

				if ( 'taxonomy_select' === $field['type'] ) {
					$term_id = (int) $raw;
					if ( 'einsatzstichwort' === $field['taxonomy'] && $term_id && ! elfzwo_einsatzstichwort_is_valid_term( $term_id ) ) {
						continue;
					}
					wp_set_object_terms( $post_id, $term_id ? array( $term_id ) : array(), $field['taxonomy'], false );
					continue;
				}
				if ( 'textarea' === $field['type'] ) {
					update_post_meta( $post_id, $meta_key, sanitize_textarea_field( $raw ) );
				} else {
					update_post_meta( $post_id, $meta_key, sanitize_text_field( $raw ) );
				}
			}
		}
	}

}
add_action( 'save_post', 'elfzwo_save_meta_boxes', 10, 2 );

/**
 * Läuft unabhängig vom Meta-Box-Formular (kein Nonce nötig) bei JEDEM
 * Speichern eines Einsatzes, damit die Einsatz-Nr. wirklich immer und
 * für jeden Einsatz automatisch vergeben wird — auch wenn der Beitrag
 * z. B. per REST-API oder Skript ohne das Formular gespeichert wurde.
 */
function elfzwo_ensure_einsatz_nummer( $post_id, $post ) {
	if ( 'einsatz' !== $post->post_type ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( wp_is_post_revision( $post_id ) ) {
		return;
	}
	elfzwo_autogenerate_einsatz_title( $post_id );
}
add_action( 'save_post', 'elfzwo_ensure_einsatz_nummer', 20, 2 );

/**
 * Einsätze haben keinen frei wählbaren Titel: er wird aus dem gewählten
 * Einsatzstichwort gebildet. Die Einsatz-Nr. vergibt
 * elfzwo_einsatz_renumber_year() (inc/einsatz-nummern.php) chronologisch.
 * Der Slug wird nur einmal vergeben und bleibt danach stabil, damit alte
 * Links nach einem Neu-Nummerieren weiter zum selben Einsatz führen; die
 * öffentliche URL ist ohnehin /einsatz/{jahr}/{nr}/.
 */
function elfzwo_autogenerate_einsatz_title( $post_id ) {
	static $running = false;
	if ( $running ) {
		return;
	}

	$terms = wp_get_object_terms( $post_id, 'einsatzstichwort' );
	if ( empty( $terms ) || is_wp_error( $terms ) ) {
		return;
	}
	$stichwort_name = $terms[0]->name;

	$post   = get_post( $post_id );
	$update = array();
	if ( $post->post_title !== $stichwort_name ) {
		$update['post_title'] = $stichwort_name;
	}
	if ( ! get_post_meta( $post_id, '_elfzwo_slug_fixed', true ) ) {
		$nummer = (int) elfzwo_meta( $post_id, 'einsatznummer', 0 );
		$update['post_name'] = sanitize_title( $stichwort_name . '-' . elfzwo_einsatz_jahr( $post_id ) . '-' . str_pad( $nummer, 2, '0', STR_PAD_LEFT ) );
		update_post_meta( $post_id, '_elfzwo_slug_fixed', 1 );
	}
	if ( ! $update ) {
		return;
	}

	$running = true;
	wp_update_post( array( 'ID' => $post_id ) + $update );
	$running = false;
}

/**
 * Aus Datenschutzgründen darf für Einsätze mit einem Rettungsdienst-
 * bezogenen Stichwort (RD 1/2/…, RD KTP, RD BERGRETTUNG, "THL 1 RD
 * Unterstützung", "THL RETTUNGSKORB RD Drehleiter" …) kein öffentlicher
 * Einsatzbericht (post_content) veröffentlicht werden -- dort könnten
 * medizinische bzw. personenbezogene Angaben zu Patienten landen.
 *
 * "RD" wird als eigenständiges Wort/Token im Stichwort-Namen erkannt
 * (durch Zeilenanfang/Leerzeichen/Klammer begrenzt, gefolgt von
 * Leerzeichen/Klammer/Bindestrich/Ziffer/Zeilenende) -- das trifft sowohl
 * die "RD …"-Stichwörter als auch die THL-Stichwörter, die "RD" nicht am
 * Anfang tragen, vermeidet aber Treffer wie "STANDARD".
 */
function elfzwo_stichwort_name_is_rd( $name ) {
	return (bool) preg_match( '/(^|[\s(])RD([\s)\-]|\d|$)/i', $name );
}

/** Prüft das (einzige) Einsatzstichwort eines Einsatzes inkl. Eltern-Begriff. */
function elfzwo_einsatz_stichwort_requires_datenschutz_block( $post_id ) {
	$terms = wp_get_object_terms( $post_id, 'einsatzstichwort' );
	if ( empty( $terms ) || is_wp_error( $terms ) ) {
		return false;
	}
	$term = $terms[0];
	while ( $term ) {
		if ( elfzwo_stichwort_name_is_rd( $term->name ) ) {
			return true;
		}
		if ( ! $term->parent ) {
			break;
		}
		$term = get_term( $term->parent, 'einsatzstichwort' );
		if ( is_wp_error( $term ) ) {
			break;
		}
	}
	return false;
}

/**
 * Autoritative Sperre: läuft NACH der Stichwort-Zuweisung (Priorität 30,
 * nach elfzwo_save_meta_boxes/elfzwo_ensure_einsatz_nummer) und greift
 * unabhängig davon, ob über das klassische Formular oder die REST-API
 * gespeichert wurde -- beide laufen über wp_insert_post() und lösen
 * save_post_einsatz aus.
 */
function elfzwo_block_einsatzbericht_for_rd( $post_id, $post ) {
	static $running = false;
	if ( $running ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( wp_is_post_revision( $post_id ) ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	if ( '' === $post->post_content ) {
		return;
	}
	if ( ! elfzwo_einsatz_stichwort_requires_datenschutz_block( $post_id ) ) {
		return;
	}

	$running = true;
	wp_update_post( array( 'ID' => $post_id, 'post_content' => '' ) );
	$running = false;
}
add_action( 'save_post_einsatz', 'elfzwo_block_einsatzbericht_for_rd', 30, 2 );

/**
 * Komfort/Klarheit im Backend: blendet das Editor-Feld für bestehende
 * Einsätze mit Rettungsdienst-Stichwort ganz aus und erklärt warum --
 * die eigentliche Sperre ist elfzwo_block_einsatzbericht_for_rd() oben.
 */
function elfzwo_hide_editor_for_rd_einsatz() {
	if ( empty( $_GET['post'] ) ) {
		return;
	}
	$post_id = (int) $_GET['post'];
	if ( 'einsatz' !== get_post_type( $post_id ) ) {
		return;
	}
	if ( ! elfzwo_einsatz_stichwort_requires_datenschutz_block( $post_id ) ) {
		return;
	}
	remove_post_type_support( 'einsatz', 'editor' );
	add_action(
		'edit_form_after_title',
		function () {
			echo '<div class="notice notice-warning inline"><p><strong>Kein Einsatzbericht möglich:</strong> Für dieses Einsatzstichwort dürfen aus Datenschutzgründen keine öffentlichen Berichte veröffentlicht werden.</p></div>';
		}
	);
}
add_action( 'load-post.php', 'elfzwo_hide_editor_for_rd_einsatz' );

/** Kleines Helferlein zum Auslesen im Template: elfzwo_meta($post_id, 'feldname', 'fallback') */
function elfzwo_meta( $post_id, $key, $default = '' ) {
	$value = get_post_meta( $post_id, '_elfzwo_' . $key, true );
	return ( '' === $value || false === $value ) ? $default : $value;
}

/** Name des zugewiesenen Einsatzort-Begriffs (Taxonomie statt Freitext). */
function elfzwo_einsatzort_name( $post_id ) {
	$terms = wp_get_object_terms( $post_id, 'einsatzort' );
	if ( empty( $terms ) || is_wp_error( $terms ) ) {
		return '';
	}
	return $terms[0]->name;
}

function elfzwo_media_field_assets( $hook ) {
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}
	wp_enqueue_media();
	wp_add_inline_script(
		'jquery-core',
		"jQuery(function($){
			$(document).on('click', '.elfzwo-media-select', function(e){
				e.preventDefault();
				var wrap = $(this).closest('.elfzwo-media-field');
				var frame = wp.media({ title: 'Datei auswählen', multiple: false });
				frame.on('select', function(){
					var att = frame.state().get('selection').first().toJSON();
					wrap.find('.elfzwo-media-input').val(att.id);
					wrap.find('.elfzwo-media-preview').html('<img src=\"'+ (att.sizes && att.sizes.thumbnail ? att.sizes.thumbnail.url : att.icon) +'\" style=\"max-width:100px;height:auto;display:block;margin-bottom:6px;\" />');
					wrap.find('.elfzwo-media-remove').show();
				});
				frame.open();
			});
			$(document).on('click', '.elfzwo-media-remove', function(e){
				e.preventDefault();
				var wrap = $(this).closest('.elfzwo-media-field');
				wrap.find('.elfzwo-media-input').val('');
				wrap.find('.elfzwo-media-preview').empty();
				$(this).hide();
			});

			function galleryIds(wrap){
				var val = wrap.find('.elfzwo-gallery-input').val();
				return val ? val.split(',').filter(Boolean) : [];
			}
			function galleryItemHtml(id, url){
				return '<div class=\"elfzwo-gallery-item\" data-id=\"'+ id +'\"><img src=\"'+ url +'\" /><button type=\"button\" class=\"elfzwo-gallery-item-remove\" title=\"Entfernen\">&times;</button></div>';
			}
			$(document).on('click', '.elfzwo-gallery-select', function(e){
				e.preventDefault();
				var wrap = $(this).closest('.elfzwo-gallery-field');
				var frame = wp.media({ title: 'Bilder auswählen', multiple: true, library: { type: 'image' } });
				frame.on('select', function(){
					var selection = frame.state().get('selection').toJSON();
					var ids = galleryIds(wrap);
					selection.forEach(function(att){
						if (ids.indexOf(String(att.id)) === -1) {
							ids.push(String(att.id));
							var thumbUrl = att.sizes && att.sizes.thumbnail ? att.sizes.thumbnail.url : att.url;
							wrap.find('.elfzwo-gallery-preview').append(galleryItemHtml(att.id, thumbUrl));
						}
					});
					wrap.find('.elfzwo-gallery-input').val(ids.join(','));
				});
				frame.open();
			});
			$(document).on('click', '.elfzwo-gallery-item-remove', function(e){
				e.preventDefault();
				var item = $(this).closest('.elfzwo-gallery-item');
				var wrap = item.closest('.elfzwo-gallery-field');
				var removeId = String(item.data('id'));
				var ids = galleryIds(wrap).filter(function(id){ return id !== removeId; });
				wrap.find('.elfzwo-gallery-input').val(ids.join(','));
				item.remove();
			});
		});"
	);
}
add_action( 'admin_enqueue_scripts', 'elfzwo_media_field_assets' );

/** Such-Combobox für taxonomy_select-Felder (z.B. die Einsatzstichwörter). */
function elfzwo_combobox_assets( $hook ) {
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}
	wp_enqueue_style( 'elfzwo-admin', get_template_directory_uri() . '/assets/css/admin.css', array(), filemtime( get_template_directory() . '/assets/css/admin.css' ) );
	wp_enqueue_script( 'elfzwo-admin-combobox', get_template_directory_uri() . '/assets/js/admin-combobox.js', array(), filemtime( get_template_directory() . '/assets/js/admin-combobox.js' ), true );
	wp_enqueue_script( 'elfzwo-admin-repeater', get_template_directory_uri() . '/assets/js/admin-repeater.js', array(), filemtime( get_template_directory() . '/assets/js/admin-repeater.js' ), true );
}
add_action( 'admin_enqueue_scripts', 'elfzwo_combobox_assets' );
