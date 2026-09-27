( function () {
	var el = wp.element.createElement;
	var ServerSideRender = window.elfzwoBlocks.ServerSideRender;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;
	var TextareaControl = wp.components.TextareaControl;
	var SelectControl = wp.components.SelectControl;
	var ImagePicker = window.elfzwoBlocks.ImagePicker;

	wp.blocks.registerBlockType( 'elfzwo/simple-hero', {
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
				el( InspectorControls, {}, el( PanelBody, { title: 'Seiten-Hero', initialOpen: true },
					el( SelectControl, { label: 'Breite', value: a.width, options: [ { label: 'Schmal (Formular-/Textseiten)', value: '3xl' }, { label: 'Mittel', value: '5xl' }, { label: 'Breit (Standard-Seiten)', value: '7xl' } ], onChange: set( 'width' ) } ),
					el( TextControl, { label: 'Vorspann', value: a.kicker, onChange: set( 'kicker' ) } ),
					el( TextControl, { label: 'Titel (H1)', value: a.title, onChange: set( 'title' ) } ),
					el( TextareaControl, { label: 'Beschreibung', value: a.description, onChange: set( 'description' ) } ),
					el( ImagePicker, { label: 'Bild (optional)', imageId: a.imageId, imageUrl: a.imageUrl, onSelect: function ( id, url ) { props.setAttributes( { imageId: id, imageUrl: url } ); }, onRemove: function () { props.setAttributes( { imageId: 0, imageUrl: '' } ); } } ),
					el( TextControl, { label: 'Button 1 – Text', value: a.ctaPrimaryText, onChange: set( 'ctaPrimaryText' ) } ),
					el( TextControl, { label: 'Button 1 – URL', value: a.ctaPrimaryUrl, onChange: set( 'ctaPrimaryUrl' ) } ),
					el( TextControl, { label: 'Button 2 – Text', value: a.ctaSecondaryText, onChange: set( 'ctaSecondaryText' ) } ),
					el( TextControl, { label: 'Button 2 – URL', value: a.ctaSecondaryUrl, onChange: set( 'ctaSecondaryUrl' ) } )
				) ),
				el( ServerSideRender, { block: 'elfzwo/simple-hero', attributes: a } )
			);
		},
		save: function () {
			return null;
		},
	} );
} )();
