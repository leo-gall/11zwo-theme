/**
 * Gemeinsame Editor-Helfer für alle elfzwo/*-Blöcke. Kein Build-Step: reines
 * ES5/ES2015-JS gegen die globalen wp.*-Objekte, kein JSX.
 */
window.elfzwoBlocks = ( function () {
	var el = wp.element.createElement;
	var Fragment = wp.element.Fragment;
	var components = wp.components;
	var blockEditor = wp.blockEditor || wp.editor;
	var i18n = wp.i18n;

	function iconOptions() {
		var keys = ( window.elfzwoBlockData && window.elfzwoBlockData.iconKeys ) || [];
		var opts = [ { label: '— Kein Icon —', value: '' } ];
		keys.forEach( function ( k ) {
			opts.push( { label: k, value: k } );
		} );
		return opts;
	}

	function IconControl( props ) {
		return el( components.SelectControl, {
			label: props.label || 'Icon',
			value: props.value || '',
			options: iconOptions(),
			onChange: props.onChange,
		} );
	}

	// Kuratierte Akzentfarben-Palette: jede Option ist eine fertige Tailwind-
	// Klasse (bg-*/Opazität oder text-*), das Farbfeld zeigt die echte Farbe
	// als Kreis statt den Klassennamen als Freitext einzutippen.
	//
	// WICHTIG: color muss ein echter Hex-/rgb-Wert sein, KEIN CSS var(...)
	// und KEIN oklch(...) -- ColorPalette prüft/vergleicht Farben intern über
	// eine JS-Farbbibliothek (colord), die var() gar nicht auflösen kann
	// (kein DOM-Kontext) und oklch() nicht zuverlässig unterstützt. Damit
	// scheitert das Parsen lautlos und alle Kreise fallen auf die
	// WP-Admin-Standardfarbe (Blau) zurück, statt ihre echte Farbe zu zeigen.
	// Werte 1:1 aus assets/js/tailwind-config.js übernommen (oklch-Farben in
	// den entsprechenden Hex-Wert umgerechnet).
	var ACCENT_PALETTE = [
		{ key: 'signal', label: 'Signalrot', color: '#9d2626', bgOpacity: 30 },
		{ key: 'ember', label: 'Ember-Orange', color: '#ca363e', bgOpacity: 40 },
		{ key: 'wood', label: 'Holzbraun', color: '#923f3f', bgOpacity: 35 },
		{ key: 'ink', label: 'Tinte (Schwarz)', color: '#1a1a1a', bgOpacity: 20 },
		{ key: 'cream', label: 'Creme (fast Weiß)', color: '#eef2f9', bgOpacity: null },
		{ key: 'sky', label: 'Himmelblau', color: '#aec5ec', bgOpacity: 40 },
		{ key: 'leaf', label: 'Weinrot', color: '#832626', bgOpacity: 40 },
	];

	// "primary" ist im Theme exakt dieselbe Farbe wie "signal" (#9d2626) --
	// kein eigener Kreis, sondern ein Alias, damit bereits gespeicherte
	// bg-primary/text-primary-Werte trotzdem als "Signalrot" erkannt werden.
	var ACCENT_ALIASES = { primary: 'signal' };

	function accentEntry( key ) {
		for ( var i = 0; i < ACCENT_PALETTE.length; i++ ) {
			if ( ACCENT_PALETTE[ i ].key === key ) { return ACCENT_PALETTE[ i ]; }
		}
		return null;
	}

	function accentClassFromKey( key, mode ) {
		var entry = accentEntry( key );
		if ( ! entry ) { return ''; }
		if ( 'text' === mode ) { return 'text-' + entry.key; }
		return entry.bgOpacity ? ( 'bg-' + entry.key + '/' + entry.bgOpacity ) : ( 'bg-' + entry.key );
	}

	function accentKeyFromClass( value, mode ) {
		if ( ! value ) { return null; }
		var prefix = 'text' === mode ? 'text-' : 'bg-';
		if ( value.indexOf( prefix ) !== 0 ) { return null; }
		var key = value.slice( prefix.length ).split( '/' )[ 0 ];
		return ACCENT_ALIASES[ key ] || key;
	}

	/**
	 * Akzentfarben-Auswahl als farbige Kreise MIT fest sichtbarer Beschriftung
	 * (nicht nur als Hover-Tooltip wie bei wp.components.ColorPalette).
	 * props: label, mode ('bg'|'text'), value (aktuelle Tailwind-Klasse),
	 * onChange( neueKlasse ).
	 */
	function AccentColorControl( props ) {
		var mode = props.mode || 'bg';
		var currentKey = accentKeyFromClass( props.value, mode );

		function swatch( key, label, color, isSelected, onClick ) {
			return el(
				'button',
				{
					key: key,
					type: 'button',
					onClick: onClick,
					'aria-pressed': isSelected,
					style: {
						display: 'flex',
						flexDirection: 'column',
						alignItems: 'center',
						gap: '4px',
						width: '68px',
						padding: '4px',
						background: 'none',
						border: 'none',
						cursor: 'pointer',
					},
				},
				el( 'span', {
					style: {
						display: 'block',
						width: '28px',
						height: '28px',
						borderRadius: '50%',
						backgroundColor: color || '#fff',
						border: color ? '1px solid rgba(0,0,0,0.15)' : '1px dashed rgba(0,0,0,0.3)',
						boxShadow: isSelected ? '0 0 0 2px #fff, 0 0 0 4px #1e1e1e' : 'none',
					},
				} ),
				el( 'span', { style: { fontSize: '11px', lineHeight: '1.3', textAlign: 'center', color: '#1e1e1e' } }, label )
			);
		}

		return el(
			components.BaseControl,
			{ label: props.label || 'Akzentfarbe' },
			el(
				'div',
				{ style: { display: 'flex', flexWrap: 'wrap', gap: '6px', marginTop: '4px' } },
				ACCENT_PALETTE.map( function ( entry ) {
					return swatch( entry.key, entry.label, entry.color, entry.key === currentKey, function () {
						props.onChange( accentClassFromKey( entry.key, mode ) );
					} );
				} ),
				swatch( '__none', 'Kein Akzent', '', ! currentKey, function () {
					props.onChange( '' );
				} )
			)
		);
	}

	function ImagePicker( props ) {
		return el(
			blockEditor.MediaUploadCheck,
			{},
			el( blockEditor.MediaUpload, {
				onSelect: function ( media ) {
					props.onSelect( media.id, media.url, media.alt || '' );
				},
				allowedTypes: [ 'image' ],
				value: props.imageId,
				render: function ( obj ) {
					var open = obj.open;
					if ( props.imageUrl ) {
						return el(
							'div',
							{ style: { marginBottom: '8px' } },
							el( 'img', {
								src: props.imageUrl,
								style: { maxWidth: '100%', display: 'block', marginBottom: '4px', borderRadius: '8px' },
							} ),
							el( components.Button, { variant: 'secondary', onClick: open }, 'Bild ändern' ),
							props.onRemove
								? el(
										components.Button,
										{ variant: 'link', isDestructive: true, onClick: props.onRemove, style: { marginLeft: '8px' } },
										'Entfernen'
								  )
								: null
						);
					}
					return el( components.Button, { variant: 'primary', onClick: open }, props.label || 'Bild auswählen' );
				},
			} )
		);
	}

	return {
		el: el,
		Fragment: Fragment,
		components: components,
		blockEditor: blockEditor,
		i18n: i18n,
		ServerSideRender: wp.serverSideRender,
		IconControl: IconControl,
		ImagePicker: ImagePicker,
		AccentColorControl: AccentColorControl,
	};
} )();
