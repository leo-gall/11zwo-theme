( function () {
	var el = wp.element.createElement;
	var ServerSideRender = window.elfzwoBlocks.ServerSideRender;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;
	var RangeControl = wp.components.RangeControl;
	var SelectControl = wp.components.SelectControl;

	wp.blocks.registerBlockType( 'elfzwo/aktuelles-uebersicht', {
		edit: function ( props ) {
			var a = props.attributes;
			return el(
				'div', {},
				el( InspectorControls, {}, el( PanelBody, { title: 'Aktuelles', initialOpen: true },
					el( SelectControl, { label: 'Inhalt', value: a.teil, options: [ { label: 'Neuigkeiten und Einsätze', value: 'beide' }, { label: 'Nur Neuigkeiten (Kartenreihe bis zum Rand)', value: 'neuigkeiten' }, { label: 'Nur letzte Einsätze (dunkles Band)', value: 'einsaetze' } ], onChange: function ( v ) { props.setAttributes( { teil: v } ); } } ),
					el( RangeControl, { label: 'Anzahl Einsätze in der Liste', min: 3, max: 10, value: a.anzahlEinsaetze, onChange: function ( v ) { props.setAttributes( { anzahlEinsaetze: v } ); } } ),
					el( TextControl, { label: 'Link „Alle anzeigen“', value: a.linkUrl, onChange: function ( v ) { props.setAttributes( { linkUrl: v } ); } } )
				) ),
				el( ServerSideRender, { block: 'elfzwo/aktuelles-uebersicht', attributes: a } )
			);
		},
		save: function () {
			return null;
		},
	} );
} )();
