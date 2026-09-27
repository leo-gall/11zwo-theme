( function () {
	var el = wp.element.createElement;
	var ServerSideRender = window.elfzwoBlocks.ServerSideRender;
	var IconControl = window.elfzwoBlocks.IconControl;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;
	var TextareaControl = wp.components.TextareaControl;
	var SelectControl = wp.components.SelectControl;
	var ToggleControl = wp.components.ToggleControl;

	wp.blocks.registerBlockType( 'elfzwo/cta-banner', {
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
				el( InspectorControls, {}, el( PanelBody, { title: 'Aufruf-Banner' },
					el( SelectControl, { label: 'Stilvariante', value: a.variant, options: [ { label: 'Signal (Rot)', value: 'signal' }, { label: 'Karte (hell)', value: 'card' } ], onChange: set( 'variant' ) } ),
					el( TextControl, { label: 'Titel', value: a.title, onChange: set( 'title' ) } ),
					el( TextareaControl, { label: 'Text', value: a.text, onChange: set( 'text' ) } ),
					el( TextControl, { label: 'Button-Text', value: a.buttonText, onChange: set( 'buttonText' ) } ),
					el( TextControl, { label: 'Button-URL', value: a.buttonUrl, onChange: set( 'buttonUrl' ) } ),
					el( IconControl, { label: 'Zusatzzeile – Icon', value: a.infoIcon, onChange: set( 'infoIcon' ) } ),
					el( TextControl, { label: 'Zusatzzeile – Text', value: a.infoText, onChange: set( 'infoText' ) } ),
					el( ToggleControl, { label: 'Ohne Außenabstand (für Einsatz in Spalten/Grid)', checked: !! a.noSection, onChange: set( 'noSection' ) } )
				) ),
				el( ServerSideRender, { block: 'elfzwo/cta-banner', attributes: a } )
			);
		},
		save: function () {
			return null;
		},
	} );
} )();
