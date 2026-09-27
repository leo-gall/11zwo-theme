( function () {
	var el = wp.element.createElement;
	var ServerSideRender = window.elfzwoBlocks.ServerSideRender;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;
	var ToggleControl = wp.components.ToggleControl;

	wp.blocks.registerBlockType( 'elfzwo/related-person-card', {
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
				el( InspectorControls, {}, el( PanelBody, { title: 'Ansprechpartner' },
					el( TextControl, { label: 'Name', value: a.name, onChange: set( 'name' ) } ),
					el( TextControl, { label: 'Rolle / Funktion', value: a.rolle, onChange: set( 'rolle' ) } ),
					el( TextControl, { label: 'Telefon', value: a.telefon, onChange: set( 'telefon' ) } ),
					el( TextControl, { label: 'E-Mail', value: a.email, onChange: set( 'email' ) } ),
					el( ToggleControl, { label: 'E-Mail anzeigen', checked: a.showEmail, onChange: set( 'showEmail' ) } )
				) ),
				el( ServerSideRender, { block: 'elfzwo/related-person-card', attributes: a } )
			);
		},
		save: function () {
			return null;
		},
	} );
} )();
