( function () {
	var el = wp.element.createElement;
	var ServerSideRender = window.elfzwoBlocks.ServerSideRender;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;
	var SelectControl = wp.components.SelectControl;
	var Button = wp.components.Button;

	wp.blocks.registerBlockType( 'elfzwo/termin-grid', {
		edit: function ( props ) {
			var a = props.attributes;
			var termine = a.termine || [];
			var downloads = wp.data.useSelect( function ( select ) {
				return select( 'core' ).getEntityRecords( 'postType', 'download', { per_page: -1, status: 'publish', orderby: 'title', order: 'asc' } );
			}, [] );
			var downloadOptions = [ { value: '0', label: downloads ? '— kein Download —' : 'Downloads werden geladen …' } ].concat(
				( downloads || [] ).map( function ( download ) {
					return { value: String( download.id ), label: download.title.rendered || '(ohne Titel)' };
				} )
			);

			function set( key ) {
				return function ( v ) {
					var o = {};
					o[ key ] = v;
					props.setAttributes( o );
				};
			}
			function updateTermin( idx, key, value ) {
				var next = termine.slice();
				var patch = {};
				patch[ key ] = value;
				next[ idx ] = Object.assign( {}, next[ idx ], patch );
				props.setAttributes( { termine: next } );
			}
			function removeTermin( idx ) {
				var next = termine.slice();
				next.splice( idx, 1 );
				props.setAttributes( { termine: next } );
			}
			function moveTermin( idx, dir ) {
				var target = idx + dir;
				if ( target < 0 || target >= termine.length ) { return; }
				var next = termine.slice();
				var tmp = next[ idx ];
				next[ idx ] = next[ target ];
				next[ target ] = tmp;
				props.setAttributes( { termine: next } );
			}
			function addTermin() {
				props.setAttributes( { termine: termine.concat( [ { zeit: '', was: '' } ] ) } );
			}

			return el(
				'div', {},
				el(
					InspectorControls,
					{},
					el( PanelBody, { title: 'Termin-Übersicht' },
						el( TextControl, { label: 'Ferien-Hinweis (optional)', value: a.ferienHinweis, onChange: set( 'ferienHinweis' ) } )
					),
					el( PanelBody, { title: 'Termine', initialOpen: true },
						termine.map( function ( termin, idx ) {
							return el(
								'div',
								{ key: idx, style: { marginBottom: '16px', paddingBottom: '12px', borderBottom: '1px solid #ddd' } },
								el( TextControl, { label: 'Zeit', value: termin.zeit, onChange: function ( v ) { updateTermin( idx, 'zeit', v ); } } ),
								el( TextControl, { label: 'Was', value: termin.was, onChange: function ( v ) { updateTermin( idx, 'was', v ); } } ),
								el( SelectControl, { label: 'Download (optional)', value: String( termin.downloadId || 0 ), options: downloadOptions, onChange: function ( v ) { updateTermin( idx, 'downloadId', parseInt( v, 10 ) || 0 ); } } ),
								el( Button, { variant: 'link', onClick: function () { moveTermin( idx, -1 ); }, disabled: idx === 0 }, '↑ Hoch' ),
								el( Button, { variant: 'link', onClick: function () { moveTermin( idx, 1 ); }, disabled: idx === termine.length - 1 }, '↓ Runter' ),
								el( Button, { variant: 'link', isDestructive: true, onClick: function () { removeTermin( idx ); } }, 'Entfernen' )
							);
						} ),
						el( Button, { variant: 'secondary', onClick: addTermin }, 'Termin hinzufügen' )
					)
				),
				el( ServerSideRender, { block: 'elfzwo/termin-grid', attributes: a } )
			);
		},
		save: function () {
			return null;
		},
	} );
} )();
