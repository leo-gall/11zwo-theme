( function () {
	var el = wp.element.createElement;
	var ServerSideRender = window.elfzwoBlocks.ServerSideRender;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var SelectControl = wp.components.SelectControl;
	var TextControl = wp.components.TextControl;
	var TextareaControl = wp.components.TextareaControl;
	var AccentColorControl = window.elfzwoBlocks.AccentColorControl;

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
					el( PanelBody, { title: 'Section-Heading' },
						el( TextControl, { label: 'Vorspann (Tag)', value: a.tag, onChange: set( 'tag' ) } ),
						el( TextControl, { label: 'Überschrift', value: a.title, onChange: set( 'title' ) } ),
						el( SelectControl, {
							label: 'Anordnung',
							value: a.layout,
							options: [ { label: 'Nebeneinander (Tag/Titel | Beschreibung)', value: 'split' }, { label: 'Gestapelt', value: 'stacked' } ],
							onChange: set( 'layout' ),
						} ),
						el( SelectControl, {
							label: 'Beschreibung',
							value: a.descriptionSource || 'custom',
							options: [ { label: 'Freitext', value: 'custom' }, { label: 'Live NINA-Warnungen', value: 'nina' } ],
							onChange: set( 'descriptionSource' ),
						} ),
						'nina' === ( a.descriptionSource || 'custom' ) && el( TextControl, {
							label: 'Postleitzahl für NINA-Warnungen',
							help: 'Für diese PLZ werden echte Warnmeldungen der NINA-Warn-App des Bundes angezeigt.',
							value: a.ninaPlz,
							placeholder: '86926',
							maxLength: 5,
							inputMode: 'numeric',
							onChange: function ( v ) {
								props.setAttributes( { ninaPlz: v.replace( /\D/g, '' ).slice( 0, 5 ) } );
							},
						} ),
						el( TextareaControl, {
							label: 'Beschreibung (Freitext)',
							help: 'nina' === ( a.descriptionSource || 'custom' ) ? 'Wird im Frontend ignoriert, solange oben "Live NINA-Warnungen" gewählt ist.' : '',
							value: a.description,
							onChange: set( 'description' ),
						} ),
						el( AccentColorControl, { label: 'Akzentfarbe', mode: 'text', value: a.accent, onChange: set( 'accent' ) } )
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
