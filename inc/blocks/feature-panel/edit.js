( function () {
	var el = wp.element.createElement;
	var ServerSideRender = window.elfzwoBlocks.ServerSideRender;
	var IconControl = window.elfzwoBlocks.IconControl;
	var ImagePicker = window.elfzwoBlocks.ImagePicker;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;
	var TextareaControl = wp.components.TextareaControl;
	var Button = wp.components.Button;

	wp.blocks.registerBlockType( 'elfzwo/feature-panel', {
		edit: function ( props ) {
			var a = props.attributes;
			var bullets = a.bulletPoints || [];

			function set( key ) {
				return function ( v ) {
					var o = {};
					o[ key ] = v;
					props.setAttributes( o );
				};
			}
			function updateBullet( idx, value ) {
				var next = bullets.slice();
				next[ idx ] = value;
				props.setAttributes( { bulletPoints: next } );
			}
			function removeBullet( idx ) {
				var next = bullets.slice();
				next.splice( idx, 1 );
				props.setAttributes( { bulletPoints: next } );
			}
			function moveBullet( idx, dir ) {
				var target = idx + dir;
				if ( target < 0 || target >= bullets.length ) { return; }
				var next = bullets.slice();
				var tmp = next[ idx ];
				next[ idx ] = next[ target ];
				next[ target ] = tmp;
				props.setAttributes( { bulletPoints: next } );
			}
			function addBullet() {
				props.setAttributes( { bulletPoints: bullets.concat( [ '' ] ) } );
			}

			return el(
				'div', {},
				el( InspectorControls, {}, el( PanelBody, { title: 'Bild-Teaser' },
					el( TextControl, { label: 'Tag', value: a.tag, onChange: set( 'tag' ) } ),
					el( TextControl, { label: 'Titel', value: a.title, onChange: set( 'title' ) } ),
					el( TextareaControl, { label: 'Beschreibung', value: a.description, onChange: set( 'description' ) } ),
					el( TextControl, { label: 'CTA-Text', value: a.ctaText, onChange: set( 'ctaText' ) } ),
					el( TextControl, { label: 'CTA-URL', value: a.ctaUrl, onChange: set( 'ctaUrl' ) } ),
					el( TextControl, { label: 'Badge-Text', value: a.badge, onChange: set( 'badge' ) } ),
					el( IconControl, { label: 'Badge-Icon', value: a.badgeIcon, onChange: set( 'badgeIcon' ) } ),
					el( TextControl, { label: 'Zitat (Sprechblase auf dem Bild)', value: a.zitat, onChange: set( 'zitat' ) } ),
					el( ImagePicker, { label: 'Bild', imageId: a.imageId, imageUrl: a.imageUrl, onSelect: function ( id, url ) { props.setAttributes( { imageId: id, imageUrl: url } ); }, onRemove: function () { props.setAttributes( { imageId: 0, imageUrl: '' } ); } } )
				) ),
				el( InspectorControls, {}, el( PanelBody, { title: 'Stichpunkte', initialOpen: true },
					bullets.map( function ( bullet, idx ) {
						return el(
							'div',
							{ key: idx, style: { display: 'flex', alignItems: 'center', gap: '4px', marginBottom: '8px' } },
							el( TextControl, { label: 'Punkt ' + ( idx + 1 ), value: bullet, onChange: function ( v ) { updateBullet( idx, v ); } } ),
							el( Button, { variant: 'link', onClick: function () { moveBullet( idx, -1 ); }, disabled: idx === 0 }, '↑' ),
							el( Button, { variant: 'link', onClick: function () { moveBullet( idx, 1 ); }, disabled: idx === bullets.length - 1 }, '↓' ),
							el( Button, { variant: 'link', isDestructive: true, onClick: function () { removeBullet( idx ); } }, 'Entfernen' )
						);
					} ),
					el( Button, { variant: 'secondary', onClick: addBullet }, 'Punkt hinzufügen' )
				) ),
				el( ServerSideRender, { block: 'elfzwo/feature-panel', attributes: a } )
			);
		},
		save: function () {
			return null;
		},
	} );
} )();
