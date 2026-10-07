( function () {
	var el = wp.element.createElement;
	var ServerSideRender = window.elfzwoBlocks.ServerSideRender;
	var ImagePicker = window.elfzwoBlocks.ImagePicker;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;
	var TextareaControl = wp.components.TextareaControl;

	wp.blocks.registerBlockType( 'elfzwo/seitenkopf', {
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
				el( InspectorControls, {}, el( PanelBody, { title: 'Seitenkopf' },
					el( TextControl, { label: 'Titel', value: a.titel, onChange: set( 'titel' ) } ),
					el( TextControl, { label: 'Zeile unter dem Titel', value: a.untertitel, onChange: set( 'untertitel' ) } ),
					el( TextareaControl, { label: 'Text', value: a.text, onChange: set( 'text' ) } ),
					el( ImagePicker, { label: 'Hintergrundfoto', imageId: a.bildId, imageUrl: a.bildUrl, onSelect: function ( id, url ) { props.setAttributes( { bildId: id, bildUrl: url } ); }, onRemove: function () { props.setAttributes( { bildId: 0, bildUrl: '' } ); } } )
				) ),
				el( ServerSideRender, { block: 'elfzwo/seitenkopf', attributes: a } )
			);
		},
		save: function () {
			return null;
		},
	} );
} )();
