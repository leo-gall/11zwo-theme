( function () {
	var el = wp.element.createElement;
	var ServerSideRender = window.elfzwoBlocks.ServerSideRender;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;
	var TextareaControl = wp.components.TextareaControl;
	var SelectControl = wp.components.SelectControl;
	var Button = wp.components.Button;

	var GRUPPEN = [
		{ value: 'aktive', label: 'Aktive' },
		{ value: 'jugend', label: 'Jugend/Kinder' },
		{ value: 'verein', label: 'Verein' },
	];

	/** Wie elfzwo_mitmachen_gruppe_von() in PHP: ältere Einträge ohne Gruppe anhand des Labels einordnen. */
	function gruppeVon( item ) {
		if ( item.gruppe ) {
			return item.gruppe;
		}
		var label = ( item.label || '' ).toLowerCase();
		if ( label.indexOf( 'jugend' ) !== -1 || label.indexOf( 'kinder' ) !== -1 ) {
			return 'jugend';
		}
		if ( label.indexOf( 'förder' ) !== -1 || label.indexOf( 'verein' ) !== -1 ) {
			return 'verein';
		}
		return 'aktive';
	}

	wp.blocks.registerBlockType( 'elfzwo/mitmachen-form', {
		edit: function ( props ) {
			var a = props.attributes;
			var interests = a.interests || [];
			var mail = a.mail || {};

			function updateItem( idx, key, value ) {
				var next = interests.slice();
				next[ idx ] = Object.assign( {}, next[ idx ], ( function () {
					var o = {};
					o[ key ] = value;
					return o;
				} )() );
				props.setAttributes( { interests: next } );
			}
			function removeItem( idx ) {
				var next = interests.slice();
				next.splice( idx, 1 );
				props.setAttributes( { interests: next } );
			}
			function addItem() {
				props.setAttributes( { interests: interests.concat( [ { label: '', hint: '', gruppe: 'aktive' } ] ) } );
			}
			function updateMail( gruppe, key, value ) {
				var next = Object.assign( {}, mail );
				next[ gruppe ] = Object.assign( {}, next[ gruppe ] );
				next[ gruppe ][ key ] = value;
				props.setAttributes( { mail: next } );
			}

			return el(
				'div', {},
				el(
					InspectorControls,
					{},
					el(
						PanelBody,
						{ title: 'Auswahlmöglichkeiten' },
						interests.map( function ( item, idx ) {
							return el(
								'div',
								{ key: idx, style: { marginBottom: '16px', paddingBottom: '12px', borderBottom: '1px solid #ddd' } },
								el( TextControl, { label: 'Label ' + ( idx + 1 ), value: item.label, onChange: function ( v ) { updateItem( idx, 'label', v ); } } ),
								el( TextControl, { label: 'Hinweis', value: item.hint, onChange: function ( v ) { updateItem( idx, 'hint', v ); } } ),
								el( SelectControl, {
									label: 'Anfrage geht an',
									value: gruppeVon( item ),
									options: GRUPPEN,
									onChange: function ( v ) { updateItem( idx, 'gruppe', v ); },
								} ),
								el( Button, { variant: 'link', isDestructive: true, onClick: function () { removeItem( idx ); } }, 'Entfernen' )
							);
						} ),
						el( Button, { variant: 'secondary', onClick: addItem }, 'Option hinzufügen' )
					),
					GRUPPEN.map( function ( g ) {
						var m = mail[ g.value ] || {};
						return el(
							PanelBody,
							{ key: g.value, title: 'E-Mail: ' + g.label, initialOpen: false },
							el( TextareaControl, {
								label: 'Empfänger',
								help: 'Eine E-Mail-Adresse pro Zeile. Leer = Admin-E-Mail der Website.',
								value: m.empfaenger || '',
								rows: 3,
								onChange: function ( v ) { updateMail( g.value, 'empfaenger', v ); },
							} ),
							el( TextControl, {
								label: 'Betreff',
								help: 'Platzhalter: {name}',
								placeholder: 'Neue Mach-mit-Anfrage von {name}',
								value: m.betreff || '',
								onChange: function ( v ) { updateMail( g.value, 'betreff', v ); },
							} ),
							el( TextareaControl, {
								label: 'Text',
								help: 'Platzhalter: {name}, {kontakt}, {interesse}',
								placeholder: 'Name: {name}\nKontakt: {kontakt}\nInteresse: {interesse}',
								value: m.text || '',
								rows: 5,
								onChange: function ( v ) { updateMail( g.value, 'text', v ); },
							} )
						);
					} )
				),
				el( ServerSideRender, { block: 'elfzwo/mitmachen-form', attributes: a } )
			);
		},
		save: function () {
			return null;
		},
	} );
} )();
