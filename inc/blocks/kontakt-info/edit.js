( function () {
	var el = wp.element.createElement;
	var ServerSideRender = window.elfzwoBlocks.ServerSideRender;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;
	var TextareaControl = wp.components.TextareaControl;
	var ToggleControl = wp.components.ToggleControl;

	wp.blocks.registerBlockType( 'elfzwo/kontakt-info', {
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
				el( InspectorControls, {},
					el( PanelBody, { title: 'Überschrift', initialOpen: true },
						el( TextControl, { label: 'Vorspann (Handschrift)', value: a.kicker, onChange: set( 'kicker' ) } ),
						el( TextControl, { label: 'Titel (H1)', value: a.title, onChange: set( 'title' ) } ),
						el( TextControl, { label: 'Titel-Zusatz (rot, optional)', value: a.titleHand, onChange: set( 'titleHand' ) } ),
						el( TextareaControl, { label: 'Beschreibung', value: a.description, onChange: set( 'description' ) } )
					),
					el( PanelBody, { title: 'Inhalte', initialOpen: true },
						el( 'p', { className: 'components-base-control__help' }, 'Adresse, Zeiten, E-Mail und Ansprechpartner kommen aus den Footer-Einstellungen unter „Design → Menüs“.' ),
						el( ToggleControl, { label: 'Gerätehaus anzeigen', checked: !! a.showGeraetehaus, onChange: set( 'showGeraetehaus' ) } ),
						el( ToggleControl, { label: 'Ansprechpartner anzeigen', checked: !! a.showPersonen, onChange: set( 'showPersonen' ) } ),
						el( ToggleControl, { label: 'Notruf-Hinweis (112) anzeigen', checked: !! a.showNotruf, onChange: set( 'showNotruf' ) } )
					)
				),
				el( ServerSideRender, { block: 'elfzwo/kontakt-info', attributes: a } )
			);
		},
		save: function () {
			return null;
		},
	} );
} )();
