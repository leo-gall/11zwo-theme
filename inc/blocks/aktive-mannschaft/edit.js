( function () {
	var el = wp.element.createElement;
	var ServerSideRender = window.elfzwoBlocks.ServerSideRender;
	var ImagePicker = window.elfzwoBlocks.ImagePicker;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;
	var TextareaControl = wp.components.TextareaControl;

	/** Punkte als Text: eine Zeile pro Punkt, "Titel: Text". */
	function punkteAlsText( punkte ) {
		return ( punkte || [] ).map( function ( p ) {
			return p.body ? p.title + ': ' + p.body : p.title;
		} ).join( '\n' );
	}
	function textAlsPunkte( text ) {
		return text.split( '\n' ).filter( function ( z ) { return z.trim(); } ).map( function ( z ) {
			var i = z.indexOf( ':' );
			return i === -1 ? { title: z.trim(), body: '' } : { title: z.slice( 0, i ).trim(), body: z.slice( i + 1 ).trim() };
		} );
	}

	wp.blocks.registerBlockType( 'elfzwo/aktive-mannschaft', {
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
				el( InspectorControls, {}, el( PanelBody, { title: 'Aktive Mannschaft', initialOpen: true },
					el( TextControl, { label: 'Überschrift', value: a.titel, onChange: set( 'titel' ) } ),
					el( TextareaControl, { label: 'Text', value: a.text, onChange: set( 'text' ) } ),
					el( TextareaControl, { label: 'Zeitstrahl', help: 'Eine Zeile pro Punkt, Format „Titel: Text“.', rows: 6, value: punkteAlsText( a.punkte ), onChange: function ( v ) { props.setAttributes( { punkte: textAlsPunkte( v ) } ); } } ),
					el( ImagePicker, { label: 'Foto (groß)', imageId: a.bildId, imageUrl: a.bildUrl, onSelect: function ( id, url ) { props.setAttributes( { bildId: id, bildUrl: url } ); }, onRemove: function () { props.setAttributes( { bildId: 0, bildUrl: '' } ); } } ),
					el( ImagePicker, { label: 'Zweites Foto (klein, versetzt)', imageId: a.bild2Id, imageUrl: a.bild2Url, onSelect: function ( id, url ) { props.setAttributes( { bild2Id: id, bild2Url: url } ); }, onRemove: function () { props.setAttributes( { bild2Id: 0, bild2Url: '' } ); } } ),
					el( TextControl, { label: 'Button – Text', value: a.buttonText, onChange: set( 'buttonText' ) } ),
					el( TextControl, { label: 'Button – URL', value: a.buttonUrl, onChange: set( 'buttonUrl' ) } )
				) ),
				el( ServerSideRender, { block: 'elfzwo/aktive-mannschaft', attributes: a } )
			);
		},
		save: function () {
			return null;
		},
	} );
} )();
