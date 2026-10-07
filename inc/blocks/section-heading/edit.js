( function () {
	var el = wp.element.createElement;
	var ServerSideRender = window.elfzwoBlocks.ServerSideRender;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var SelectControl = wp.components.SelectControl;
	var TextControl = wp.components.TextControl;
	var TextareaControl = wp.components.TextareaControl;

	wp.blocks.registerBlockType( 'elfzwo/section-heading', {
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
				el(
					InspectorControls,
					{},
					el( PanelBody, { title: 'Abschnitts-Überschrift' },
						el( TextControl, { label: 'Überschrift', value: a.title, onChange: set( 'title' ) } ),
						el( TextareaControl, { label: 'Beschreibung', value: a.description, onChange: set( 'description' ) } ),
						el( SelectControl, {
							label: 'Anordnung',
							value: a.layout,
							options: [ { label: 'Nebeneinander (Titel | Beschreibung)', value: 'split' }, { label: 'Untereinander', value: 'stacked' } ],
							onChange: set( 'layout' ),
						} )
					)
				),
				el( ServerSideRender, { block: 'elfzwo/section-heading', attributes: a } )
			);
		},
		save: function () {
			return null;
		},
	} );
} )();
