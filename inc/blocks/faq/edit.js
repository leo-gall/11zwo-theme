( function () {
	var el = wp.element.createElement;
	var ServerSideRender = window.elfzwoBlocks.ServerSideRender;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;
	var TextareaControl = wp.components.TextareaControl;
	var Button = wp.components.Button;

	wp.blocks.registerBlockType( 'elfzwo/faq', {
		edit: function ( props ) {
			var a = props.attributes;
			var items = a.items || [];

			function updateItem( idx, key, value ) {
				var next = items.slice();
				var patch = {};
				patch[ key ] = value;
				next[ idx ] = Object.assign( {}, next[ idx ], patch );
				props.setAttributes( { items: next } );
			}
			function removeItem( idx ) {
				var next = items.slice();
				next.splice( idx, 1 );
				props.setAttributes( { items: next } );
			}
			function moveItem( idx, dir ) {
				var target = idx + dir;
				if ( target < 0 || target >= items.length ) { return; }
				var next = items.slice();
				var tmp = next[ idx ];
				next[ idx ] = next[ target ];
				next[ target ] = tmp;
				props.setAttributes( { items: next } );
			}
			function addItem() {
				props.setAttributes( { items: items.concat( [ { question: '', answer: '' } ] ) } );
			}

			return el(
				'div', {},
				el(
					InspectorControls,
					{},
					el( PanelBody, { title: 'FAQ-Einträge', initialOpen: true },
						items.map( function ( item, idx ) {
							return el(
								'div',
								{ key: idx, style: { marginBottom: '16px', paddingBottom: '12px', borderBottom: '1px solid #ddd' } },
								el( TextControl, { label: 'Frage', value: item.question, onChange: function ( v ) { updateItem( idx, 'question', v ); } } ),
								el( TextareaControl, { label: 'Antwort', value: item.answer, onChange: function ( v ) { updateItem( idx, 'answer', v ); } } ),
								el( Button, { variant: 'link', onClick: function () { moveItem( idx, -1 ); }, disabled: idx === 0 }, '↑ Hoch' ),
								el( Button, { variant: 'link', onClick: function () { moveItem( idx, 1 ); }, disabled: idx === items.length - 1 }, '↓ Runter' ),
								el( Button, { variant: 'link', isDestructive: true, onClick: function () { removeItem( idx ); } }, 'Entfernen' )
							);
						} ),
						el( Button, { variant: 'secondary', onClick: addItem }, 'Eintrag hinzufügen' )
					)
				),
				el( ServerSideRender, { block: 'elfzwo/faq', attributes: a } )
			);
		},
		save: function () {
			return null;
		},
	} );
} )();
