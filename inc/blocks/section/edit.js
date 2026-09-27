( function () {
	var el = wp.element.createElement;
	var InnerBlocks = wp.blockEditor.InnerBlocks;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;

	wp.blocks.registerBlockType( 'elfzwo/section', {
		edit: function ( props ) {
			var a = props.attributes;
			return el(
				'div', { className: a.className || '' },
				el( InspectorControls, {}, el( PanelBody, { title: 'Layout' },
					el( TextControl, { label: 'Tailwind-Klassen', value: a.className, onChange: function ( v ) { props.setAttributes( { className: v } ); } } )
				) ),
				el( InnerBlocks, { templateLock: false } )
			);
		},
		save: function () {
			return el( InnerBlocks.Content );
		},
	} );
} )();
