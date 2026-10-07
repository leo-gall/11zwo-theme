( function () {
	var el = wp.element.createElement;
	var ServerSideRender = window.elfzwoBlocks.ServerSideRender;
	var IconControl = window.elfzwoBlocks.IconControl;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;
	var TextareaControl = wp.components.TextareaControl;
	var RangeControl = wp.components.RangeControl;
	var Button = wp.components.Button;

	wp.blocks.registerBlockType( 'elfzwo/icon-card-grid', {
		edit: function ( props ) {
			var a = props.attributes;
			var cards = a.cards || [];

			function set( key ) {
				return function ( v ) {
					var o = {};
					o[ key ] = v;
					props.setAttributes( o );
				};
			}
			function updateCard( idx, key, value ) {
				var next = cards.slice();
				var patch = {};
				patch[ key ] = value;
				next[ idx ] = Object.assign( {}, next[ idx ], patch );
				props.setAttributes( { cards: next } );
			}
			function removeCard( idx ) {
				var next = cards.slice();
				next.splice( idx, 1 );
				props.setAttributes( { cards: next } );
			}
			function moveCard( idx, dir ) {
				var target = idx + dir;
				if ( target < 0 || target >= cards.length ) { return; }
				var next = cards.slice();
				var tmp = next[ idx ];
				next[ idx ] = next[ target ];
				next[ target ] = tmp;
				props.setAttributes( { cards: next } );
			}
			function addCard() {
				props.setAttributes( { cards: cards.concat( [ { icon: 'flame', title: '', body: '' } ] ) } );
			}

			return el(
				'div', {},
				el(
					InspectorControls,
					{},
					el( PanelBody, { title: 'Raster' },
						el( RangeControl, { label: 'Spalten', min: 2, max: 4, value: a.columns, onChange: set( 'columns' ) } )
					),
					el( PanelBody, { title: 'Karten', initialOpen: true },
						cards.map( function ( card, idx ) {
							return el(
								'div',
								{ key: idx, style: { marginBottom: '16px', paddingBottom: '12px', borderBottom: '1px solid #ddd' } },
								el( IconControl, { label: 'Icon', value: card.icon, onChange: function ( v ) { updateCard( idx, 'icon', v ); } } ),
								el( TextControl, { label: 'Titel', value: card.title, onChange: function ( v ) { updateCard( idx, 'title', v ); } } ),
								el( TextareaControl, { label: 'Text', value: card.body, onChange: function ( v ) { updateCard( idx, 'body', v ); } } ),
								el( Button, { variant: 'link', onClick: function () { moveCard( idx, -1 ); }, disabled: idx === 0 }, '↑ Hoch' ),
								el( Button, { variant: 'link', onClick: function () { moveCard( idx, 1 ); }, disabled: idx === cards.length - 1 }, '↓ Runter' ),
								el( Button, { variant: 'link', isDestructive: true, onClick: function () { removeCard( idx ); } }, 'Entfernen' )
							);
						} ),
						el( Button, { variant: 'secondary', onClick: addCard }, 'Karte hinzufügen' )
					)
				),
				el( ServerSideRender, { block: 'elfzwo/icon-card-grid', attributes: a } )
			);
		},
		save: function () {
			return null;
		},
	} );
} )();
