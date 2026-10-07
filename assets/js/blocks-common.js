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
	};
} )();
