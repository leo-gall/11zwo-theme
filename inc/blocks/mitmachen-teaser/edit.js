( function () {
	var el = wp.element.createElement;
	var ServerSideRender = window.elfzwoBlocks.ServerSideRender;
	var ImagePicker = window.elfzwoBlocks.ImagePicker;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;
	var TextareaControl = wp.components.TextareaControl;
	var SelectControl = wp.components.SelectControl;

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

	wp.blocks.registerBlockType( 'elfzwo/mitmachen-teaser', {
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
						el( SelectControl, { label: 'Anzeigen', value: a.teil, options: [ { label: 'Aktive und Jugendfeuerwehr', value: 'beide' }, { label: 'Nur aktive Mannschaft', value: 'aktive' }, { label: 'Nur Kinder- & Jugendfeuerwehr', value: 'jugend' } ], onChange: set( 'teil' ) } ),
						el( TextControl, { label: 'Titel', value: a.titel, onChange: set( 'titel' ) } ),
						el( TextareaControl, { label: 'Einleitung (optional)', value: a.intro, onChange: set( 'intro' ) } )
					),
					el( PanelBody, { title: 'Aktive Mannschaft', initialOpen: false },
						el( TextControl, { label: 'Titel', value: a.aktiveTitel, onChange: set( 'aktiveTitel' ) } ),
						el( TextareaControl, { label: 'Text', value: a.aktiveText, onChange: set( 'aktiveText' ) } ),
						el( TextareaControl, { label: 'Punkte', help: 'Eine Zeile pro Punkt, Format „Titel: Text“.', rows: 6, value: punkteAlsText( a.punkte ), onChange: function ( v ) { props.setAttributes( { punkte: textAlsPunkte( v ) } ); } } ),
						el( ImagePicker, { label: 'Foto (groß)', imageId: a.aktiveImageId, imageUrl: a.aktiveImageUrl, onSelect: function ( id, url ) { props.setAttributes( { aktiveImageId: id, aktiveImageUrl: url } ); }, onRemove: function () { props.setAttributes( { aktiveImageId: 0, aktiveImageUrl: '' } ); } } ),
						el( ImagePicker, { label: 'Zweites Foto (klein, versetzt)', imageId: a.aktiveImage2Id, imageUrl: a.aktiveImage2Url, onSelect: function ( id, url ) { props.setAttributes( { aktiveImage2Id: id, aktiveImage2Url: url } ); }, onRemove: function () { props.setAttributes( { aktiveImage2Id: 0, aktiveImage2Url: '' } ); } } ),
						el( TextControl, { label: 'Button – Text', value: a.aktiveButtonText, onChange: set( 'aktiveButtonText' ) } ),
						el( TextControl, { label: 'Button – URL', value: a.aktiveButtonUrl, onChange: set( 'aktiveButtonUrl' ) } )
					),
					el( PanelBody, { title: 'Kinder- & Jugendfeuerwehr', initialOpen: false },
						el( TextControl, { label: 'Titel', value: a.jugendTitel, onChange: set( 'jugendTitel' ) } ),
						el( TextareaControl, { label: 'Text', value: a.jugendText, onChange: set( 'jugendText' ) } ),
						el( TextareaControl, { label: 'Stichpunkte', help: 'Eine Zeile pro Stichpunkt.', rows: 4, value: ( a.jugendPunkte || [] ).join( '\n' ), onChange: function ( v ) { props.setAttributes( { jugendPunkte: v.split( '\n' ).filter( function ( z ) { return z.trim(); } ) } ); } } ),
						el( ImagePicker, { label: 'Foto', imageId: a.jugendImageId, imageUrl: a.jugendImageUrl, onSelect: function ( id, url ) { props.setAttributes( { jugendImageId: id, jugendImageUrl: url } ); }, onRemove: function () { props.setAttributes( { jugendImageId: 0, jugendImageUrl: '' } ); } } ),
						el( TextControl, { label: 'Button – Text', value: a.jugendButtonText, onChange: set( 'jugendButtonText' ) } ),
						el( TextControl, { label: 'Button – URL', value: a.jugendButtonUrl, onChange: set( 'jugendButtonUrl' ) } )
					)
				),
				el( ServerSideRender, { block: 'elfzwo/mitmachen-teaser', attributes: a } )
			);
		},
		save: function () {
			return null;
		},
	} );
} )();
