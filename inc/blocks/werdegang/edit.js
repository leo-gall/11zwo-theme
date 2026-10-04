( function () {
	var el = wp.element.createElement;
	var ServerSideRender = window.elfzwoBlocks.ServerSideRender;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;
	var TextareaControl = wp.components.TextareaControl;
	var Button = wp.components.Button;

	wp.blocks.registerBlockType( 'elfzwo/werdegang', {
		edit: function ( props ) {
			var a = props.attributes;
			var stufen = a.stufen || [];
			function update( idx, key, value ) {
				var next = stufen.slice();
				var patch = {};
				patch[ key ] = value;
				next[ idx ] = Object.assign( {}, next[ idx ], patch );
				props.setAttributes( { stufen: next } );
			}
			function move( idx, dir ) {
				var t = idx + dir;
				if ( t < 0 || t >= stufen.length ) { return; }
				var next = stufen.slice();
				var tmp = next[ idx ];
				next[ idx ] = next[ t ];
				next[ t ] = tmp;
				props.setAttributes( { stufen: next } );
			}
			function remove( idx ) {
				var next = stufen.slice();
				next.splice( idx, 1 );
				props.setAttributes( { stufen: next } );
			}
			return el(
				'div', {},
				el( InspectorControls, {},
					el( PanelBody, { title: 'Stufen', initialOpen: true },
						stufen.map( function ( s, idx ) {
							return el( 'div', { key: idx, style: { marginBottom: '16px', paddingBottom: '12px', borderBottom: '1px solid #ddd' } },
								el( TextControl, { label: 'Stufe ' + ( idx + 1 ) + ' – Titel', value: s.titel, onChange: function ( v ) { update( idx, 'titel', v ); } } ),
								el( TextControl, { label: 'Kurzinfo (z. B. „ab 9 Jahren“)', value: s.info, onChange: function ( v ) { update( idx, 'info', v ); } } ),
								el( TextareaControl, { label: 'Text', value: s.text, onChange: function ( v ) { update( idx, 'text', v ); } } ),
								el( Button, { variant: 'link', onClick: function () { move( idx, -1 ); }, disabled: idx === 0 }, '↑ Hoch' ),
								el( Button, { variant: 'link', onClick: function () { move( idx, 1 ); }, disabled: idx === stufen.length - 1 }, '↓ Runter' ),
								el( Button, { variant: 'link', isDestructive: true, onClick: function () { remove( idx ); } }, 'Entfernen' )
							);
						} ),
						el( Button, { variant: 'secondary', onClick: function () { props.setAttributes( { stufen: stufen.concat( [ { titel: '', info: '', text: '' } ] ) } ); } }, 'Stufe hinzufügen' )
					),
					el( PanelBody, { title: 'Button (optional)', initialOpen: false },
						el( TextControl, { label: 'Text', value: a.buttonText, onChange: function ( v ) { props.setAttributes( { buttonText: v } ); } } ),
						el( TextControl, { label: 'URL', value: a.buttonUrl, onChange: function ( v ) { props.setAttributes( { buttonUrl: v } ); } } )
					)
				),
				el( ServerSideRender, { block: 'elfzwo/werdegang', attributes: a } )
			);
		},
		save: function () {
			return null;
		},
	} );
} )();
