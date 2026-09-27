( function () {
	var el = wp.element.createElement;
	var ServerSideRender = window.elfzwoBlocks.ServerSideRender;
	var ImagePicker = window.elfzwoBlocks.ImagePicker;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;
	var TextareaControl = wp.components.TextareaControl;

	wp.blocks.registerBlockType( 'elfzwo/home-hero', {
		edit: function ( props ) {
			var a = props.attributes;
			function set( key ) {
				return function ( v ) {
					var o = {};
					o[ key ] = v;
					props.setAttributes( o );
				};
			}
			function imgField( n ) {
				return el( ImagePicker, {
					label: 'Foto ' + n,
					imageId: a[ 'image' + n + 'Id' ],
					imageUrl: a[ 'image' + n + 'Url' ],
					onSelect: function ( id, url ) {
						var o = {};
						o[ 'image' + n + 'Id' ] = id;
						o[ 'image' + n + 'Url' ] = url;
						props.setAttributes( o );
					},
					onRemove: function () {
						var o = {};
						o[ 'image' + n + 'Id' ] = 0;
						o[ 'image' + n + 'Url' ] = '';
						props.setAttributes( o );
					},
				} );
			}
			return el(
				'div', {},
				el( InspectorControls, {}, el( PanelBody, { title: 'Hero-Texte' },
					el( TextControl, { label: 'Titel Zeile 1', value: a.title1, onChange: set( 'title1' ) } ),
					el( TextControl, { label: 'Titel Zeile 2', value: a.title2, onChange: set( 'title2' ) } ),
					el( TextControl, { label: 'Handschrift-Zeile', value: a.subtitle, onChange: set( 'subtitle' ) } ),
					el( TextareaControl, { label: 'Beschreibung', value: a.description, onChange: set( 'description' ) } ),
					el( TextControl, { label: 'Button primär – Text', value: a.ctaPrimaryText, onChange: set( 'ctaPrimaryText' ) } ),
					el( TextControl, { label: 'Button sekundär – Text', value: a.ctaSecondaryText, onChange: set( 'ctaSecondaryText' ) } ),
					el( TextControl, { label: 'Button sekundär – URL', value: a.ctaSecondaryUrl, onChange: set( 'ctaSecondaryUrl' ) } ),
					el( TextControl, { label: 'Sprechblasen-Text', value: a.emergencyBadge, onChange: set( 'emergencyBadge' ) } )
				), el( PanelBody, { title: 'Fotos' },
					imgField( 1 ), imgField( 2 ), imgField( 3 )
				) ),
				el( ServerSideRender, { block: 'elfzwo/home-hero', attributes: a } )
			);
		},
		save: function () {
			return null;
		},
	} );
} )();
