( function () {
	var el = wp.element.createElement;
	var ServerSideRender = window.elfzwoBlocks.ServerSideRender;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;
	var TextareaControl = wp.components.TextareaControl;
	var SelectControl = wp.components.SelectControl;

	wp.blocks.registerBlockType( 'elfzwo/katastrophenwarnung', {
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
				el( InspectorControls, {}, el( PanelBody, { title: 'Warnung' },
					el( SelectControl, { label: 'Stufe', value: a.level, options: [ { label: 'Warnung', value: 'Warnung' }, { label: 'Gefahr', value: 'Gefahr' }, { label: 'Extreme Gefahr', value: 'Extreme Gefahr' } ], onChange: set( 'level' ) } ),
					el( TextControl, { label: 'Titel', value: a.title, onChange: set( 'title' ) } ),
					el( TextareaControl, { label: 'Nachricht', value: a.message, onChange: set( 'message' ) } ),
					el( TextControl, { label: 'Quelle', value: a.source, onChange: set( 'source' ) } )
				) ),
				el( ServerSideRender, { block: 'elfzwo/katastrophenwarnung', attributes: a } )
			);
		},
		save: function () {
			return null;
		},
	} );
} )();
