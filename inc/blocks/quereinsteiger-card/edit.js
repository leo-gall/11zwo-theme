( function () {
	var el = wp.element.createElement;
	var ServerSideRender = window.elfzwoBlocks.ServerSideRender;
	var ImagePicker = window.elfzwoBlocks.ImagePicker;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;

	wp.blocks.registerBlockType( 'elfzwo/quereinsteiger-card', {
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
				el( InspectorControls, {}, el( PanelBody, { title: 'Quereinsteiger-Porträt' },
					el( ImagePicker, { label: 'Foto', imageId: a.imageId, imageUrl: a.imageUrl, onSelect: function ( id, url ) { props.setAttributes( { imageId: id, imageUrl: url } ); }, onRemove: function () { props.setAttributes( { imageId: 0, imageUrl: '' } ); } } ),
					el( TextControl, { label: 'Zeile unter dem Namen', value: a.badge, onChange: set( 'badge' ) } ),
					el( TextControl, { label: 'Name', value: a.name, onChange: set( 'name' ) } )
				) ),
				el( ServerSideRender, { block: 'elfzwo/quereinsteiger-card', attributes: a } )
			);
		},
		save: function () {
			return null;
		},
	} );
} )();
