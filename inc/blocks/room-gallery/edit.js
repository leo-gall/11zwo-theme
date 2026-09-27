( function () {
	var el = wp.element.createElement;
	var ServerSideRender = window.elfzwoBlocks.ServerSideRender;
	var ImagePicker = window.elfzwoBlocks.ImagePicker;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;
	var ToggleControl = wp.components.ToggleControl;
	var Button = wp.components.Button;

	wp.blocks.registerBlockType( 'elfzwo/room-gallery', {
		edit: function ( props ) {
			var a = props.attributes;
			var tiles = a.tiles || [];

			function set( key ) {
				return function ( v ) {
					var o = {};
					o[ key ] = v;
					props.setAttributes( o );
				};
			}
			function updateTile( idx, key, value ) {
				var next = tiles.slice();
				var patch = {};
				patch[ key ] = value;
				next[ idx ] = Object.assign( {}, next[ idx ], patch );
				props.setAttributes( { tiles: next } );
			}
			function removeTile( idx ) {
				var next = tiles.slice();
				next.splice( idx, 1 );
				props.setAttributes( { tiles: next } );
			}
			function moveTile( idx, dir ) {
				var target = idx + dir;
				if ( target < 0 || target >= tiles.length ) { return; }
				var next = tiles.slice();
				var tmp = next[ idx ];
				next[ idx ] = next[ target ];
				next[ target ] = tmp;
				props.setAttributes( { tiles: next } );
			}
			function addTile() {
				props.setAttributes( { tiles: tiles.concat( [ { title: '', caption: '', imageId: 0, imageUrl: '', featured: false } ] ) } );
			}

			return el(
				'div', {},
				el(
					InspectorControls,
					{},
					el( PanelBody, { title: 'Rundgang-Fotoraster' },
						el( TextControl, { label: 'Vorspann', value: a.kicker, onChange: set( 'kicker' ) } ),
						el( TextControl, { label: 'Titel', value: a.title, onChange: set( 'title' ) } )
					),
					el( PanelBody, { title: 'Räume', initialOpen: true },
						tiles.map( function ( tile, idx ) {
							return el(
								'div',
								{ key: idx, style: { marginBottom: '16px', paddingBottom: '12px', borderBottom: '1px solid #ddd' } },
								el( ImagePicker, {
									label: 'Bild',
									imageId: tile.imageId,
									imageUrl: tile.imageUrl,
									onSelect: function ( id, url ) { var next = tiles.slice(); next[ idx ] = Object.assign( {}, next[ idx ], { imageId: id, imageUrl: url } ); props.setAttributes( { tiles: next } ); },
									onRemove: function () { var next = tiles.slice(); next[ idx ] = Object.assign( {}, next[ idx ], { imageId: 0, imageUrl: '' } ); props.setAttributes( { tiles: next } ); },
								} ),
								el( TextControl, { label: 'Titel', value: tile.title, onChange: function ( v ) { updateTile( idx, 'title', v ); } } ),
								el( TextControl, { label: 'Untertitel', value: tile.caption, onChange: function ( v ) { updateTile( idx, 'caption', v ); } } ),
								el( ToggleControl, { label: 'Groß (Bento-Highlight)', checked: !! tile.featured, onChange: function ( v ) { updateTile( idx, 'featured', v ); } } ),
								el( Button, { variant: 'link', onClick: function () { moveTile( idx, -1 ); }, disabled: idx === 0 }, '↑ Hoch' ),
								el( Button, { variant: 'link', onClick: function () { moveTile( idx, 1 ); }, disabled: idx === tiles.length - 1 }, '↓ Runter' ),
								el( Button, { variant: 'link', isDestructive: true, onClick: function () { removeTile( idx ); } }, 'Entfernen' )
							);
						} ),
						el( Button, { variant: 'secondary', onClick: addTile }, 'Raum hinzufügen' )
					)
				),
				el( ServerSideRender, { block: 'elfzwo/room-gallery', attributes: a } )
			);
		},
		save: function () {
			return null;
		},
	} );
} )();
