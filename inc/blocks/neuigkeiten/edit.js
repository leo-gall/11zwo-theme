( function () {
	var el = wp.element.createElement;
	var ServerSideRender = window.elfzwoBlocks.ServerSideRender;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;
	var RangeControl = wp.components.RangeControl;

	wp.blocks.registerBlockType( 'elfzwo/neuigkeiten', {
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
				el( InspectorControls, {}, el( PanelBody, { title: 'Neuigkeiten', initialOpen: true },
					el( RangeControl, { label: 'Anzahl Beiträge', min: 2, max: 8, value: a.anzahl, onChange: set( 'anzahl' ) } ),
					el( TextControl, { label: 'Button – Text', value: a.buttonText, onChange: set( 'buttonText' ) } ),
					el( TextControl, { label: 'Button – URL', value: a.buttonUrl, onChange: set( 'buttonUrl' ) } )
				) ),
				el( ServerSideRender, { block: 'elfzwo/neuigkeiten', attributes: a } )
			);
		},
		save: function () {
			return null;
		},
	} );
} )();
