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

	wp.blocks.registerBlockType( 'elfzwo/stat-tile-group', {
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
				props.setAttributes( { tiles: tiles.concat( [ { icon: 'award', value: '', label: '' } ] ) } );
			}

			return el(
				'div', {},
				el(
					InspectorControls,
					{},
					el( PanelBody, { title: 'Darstellung' },
						el( TextareaControl, { label: 'Einleitung (optional)', value: a.intro, onChange: set( 'intro' ) } ),
						el( RangeControl, { label: 'Spalten', min: 2, max: 6, value: a.columns, onChange: set( 'columns' ) } )
					),
					el( PanelBody, { title: 'Kacheln', initialOpen: true },
						tiles.map( function ( tile, idx ) {
							return el(
								'div',
								{ key: idx, style: { marginBottom: '16px', paddingBottom: '12px', borderBottom: '1px solid #ddd' } },
								el( IconControl, { label: 'Icon', value: tile.icon, onChange: function ( v ) { updateTile( idx, 'icon', v ); } } ),
								el( TextControl, { label: 'Wert', value: tile.value, onChange: function ( v ) { updateTile( idx, 'value', v ); } } ),
								el( TextControl, { label: 'Beschriftung', value: tile.label, onChange: function ( v ) { updateTile( idx, 'label', v ); } } ),
								el( Button, { variant: 'link', onClick: function () { moveTile( idx, -1 ); }, disabled: idx === 0 }, '↑ Hoch' ),
								el( Button, { variant: 'link', onClick: function () { moveTile( idx, 1 ); }, disabled: idx === tiles.length - 1 }, '↓ Runter' ),
								el( Button, { variant: 'link', isDestructive: true, onClick: function () { removeTile( idx ); } }, 'Entfernen' )
							);
						} ),
						el( Button, { variant: 'secondary', onClick: addTile }, 'Kachel hinzufügen' )
					)
				),
				el( ServerSideRender, { block: 'elfzwo/stat-tile-group', attributes: a } )
			);
		},
		save: function () {
			return null;
		},
	} );
} )();
