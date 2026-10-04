( function () {
	var el = wp.element.createElement;
	var ServerSideRender = window.elfzwoBlocks.ServerSideRender;
	var ImagePicker = window.elfzwoBlocks.ImagePicker;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;
	var TextareaControl = wp.components.TextareaControl;

	wp.blocks.registerBlockType( 'elfzwo/jugend-teaser', {
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
				el( InspectorControls, {}, el( PanelBody, { title: 'Kinder- & Jugendfeuerwehr', initialOpen: true },
					el( TextControl, { label: 'Überschrift', value: a.titel, onChange: set( 'titel' ) } ),
					el( TextareaControl, { label: 'Text', value: a.text, onChange: set( 'text' ) } ),
					el( TextareaControl, { label: 'Stichpunkte', help: 'Eine Zeile pro Stichpunkt.', rows: 4, value: ( a.punkte || [] ).join( '\n' ), onChange: function ( v ) { props.setAttributes( { punkte: v.split( '\n' ).filter( function ( z ) { return z.trim(); } ) } ); } } ),
					el( ImagePicker, { label: 'Foto', imageId: a.bildId, imageUrl: a.bildUrl, onSelect: function ( id, url ) { props.setAttributes( { bildId: id, bildUrl: url } ); }, onRemove: function () { props.setAttributes( { bildId: 0, bildUrl: '' } ); } } ),
					el( TextControl, { label: 'Button – Text', value: a.buttonText, onChange: set( 'buttonText' ) } ),
					el( TextControl, { label: 'Button – URL', value: a.buttonUrl, onChange: set( 'buttonUrl' ) } )
				) ),
				el( ServerSideRender, { block: 'elfzwo/jugend-teaser', attributes: a } )
			);
		},
		save: function () {
			return null;
		},
	} );
} )();
