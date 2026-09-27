( function () {
	var el = wp.element.createElement;
	var ServerSideRender = window.elfzwoBlocks.ServerSideRender;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;
	var Button = wp.components.Button;

	wp.blocks.registerBlockType( 'elfzwo/mitmachen-form', {
		edit: function ( props ) {
			var a = props.attributes;
			var interests = a.interests || [];

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
				props.setAttributes( { interests: interests.concat( [ { label: '', hint: '' } ] ) } );
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
								el( Button, { variant: 'link', isDestructive: true, onClick: function () { removeItem( idx ); } }, 'Entfernen' )
							);
						} ),
						el( Button, { variant: 'secondary', onClick: addItem }, 'Option hinzufügen' )
					)
				),
				el( ServerSideRender, { block: 'elfzwo/mitmachen-form', attributes: a } )
			);
		},
		save: function () {
			return null;
		},
	} );
} )();
