( function () {
	var el = wp.element.createElement;
	var ServerSideRender = window.elfzwoBlocks.ServerSideRender;
	var ImagePicker = window.elfzwoBlocks.ImagePicker;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;
	var TextareaControl = wp.components.TextareaControl;

	wp.blocks.registerBlockType( 'elfzwo/hero-split', {
		edit: function ( props ) {
			var a = props.attributes;
			function set( key ) {
				return function ( v ) {
					var o = {};
					o[ key ] = v;
					props.setAttributes( o );
				};
			}
			return el(
				'div', {},
				el( InspectorControls, {}, el( PanelBody, { title: 'Jugendfeuerwehr-Hero' },
					el( TextControl, { label: 'Badge', value: a.badge, onChange: set( 'badge' ) } ),
					el( TextControl, { label: 'Titel Zeile 1', value: a.titleLine1, onChange: set( 'titleLine1' ) } ),
					el( TextControl, { label: 'Titel Highlight', value: a.titleHighlight, onChange: set( 'titleHighlight' ) } ),
					el( TextControl, { label: 'Titel Rest', value: a.titleLine2, onChange: set( 'titleLine2' ) } ),
					el( TextareaControl, { label: 'Beschreibung', value: a.description, onChange: set( 'description' ) } ),
					el( TextControl, { label: 'Button-Text', value: a.ctaPrimary, onChange: set( 'ctaPrimary' ) } ),
					el( TextControl, { label: 'Button-URL', value: a.ctaUrl, onChange: set( 'ctaUrl' ) } ),
					el( TextControl, { label: 'Foto-Zitat', value: a.zitat, onChange: set( 'zitat' ) } ),
					el( ImagePicker, { label: 'Bild', imageId: a.imageId, imageUrl: a.imageUrl, onSelect: function ( id, url ) { props.setAttributes( { imageId: id, imageUrl: url } ); }, onRemove: function () { props.setAttributes( { imageId: 0, imageUrl: '' } ); } } )
				) ),
				el( ServerSideRender, { block: 'elfzwo/hero-split', attributes: a } )
			);
		},
		save: function () {
			return null;
		},
	} );
} )();
