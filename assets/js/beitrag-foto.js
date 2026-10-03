/**
 * TinyMCE-Button "Foto einfügen" für Beiträge (siehe inc/beitrag-editor.php).
 * Öffnet die Mediathek; Bilder, die nicht 16:9 sind, werden anschließend im
 * WordPress-Zuschneide-Dialog auf 16:9 zugeschnitten (neue Datei über die
 * Core-Ajax-Aktion "crop-image") und dann in den Text eingefügt.
 */
( function () {
	var RATIO = 16 / 9;
	var TOLERANCE = 0.01;

	function isSixteenNine( width, height ) {
		return width && height && Math.abs( width / height - RATIO ) / RATIO <= TOLERANCE;
	}

	// Startauswahl: größtmöglicher, mittig liegender 16:9-Ausschnitt.
	function imgSelectOptions( attachment ) {
		var realWidth = attachment.get( 'width' );
		var realHeight = attachment.get( 'height' );
		var width = realWidth;
		var height = Math.round( realWidth / RATIO );
		if ( height > realHeight ) {
			height = realHeight;
			width = Math.round( realHeight * RATIO );
		}
		var x1 = Math.round( ( realWidth - width ) / 2 );
		var y1 = Math.round( ( realHeight - height ) / 2 );
		return {
			handles: true,
			keys: true,
			instance: true,
			persistent: true,
			aspectRatio: '16:9',
			imageWidth: realWidth,
			imageHeight: realHeight,
			x1: x1,
			y1: y1,
			x2: x1 + width,
			y2: y1 + height,
		};
	}

	var FotoCropper = wp.media.controller.Cropper.extend( {
		doCrop: function ( attachment ) {
			var crop = attachment.get( 'cropDetails' );
			crop.dst_width = crop.width;
			crop.dst_height = crop.height;
			return wp.ajax.post( 'crop-image', {
				nonce: attachment.get( 'nonces' ).edit,
				id: attachment.get( 'id' ),
				context: 'elfzwo-foto-16x9',
				cropDetails: crop,
			} );
		},
	} );

	function imageHtml( attachment ) {
		var size = ( attachment.sizes && ( attachment.sizes.large || attachment.sizes.full ) ) || attachment;
		var sizeName = attachment.sizes && attachment.sizes.large ? 'large' : 'full';
		var img = document.createElement( 'img' );
		img.className = 'size-' + sizeName + ' wp-image-' + attachment.id;
		img.setAttribute( 'src', size.url );
		img.setAttribute( 'alt', attachment.alt || '' );
		img.setAttribute( 'width', size.width );
		img.setAttribute( 'height', size.height );
		return '<p>' + img.outerHTML + '</p>';
	}

	function openFrame( editor ) {
		var frame = wp.media( {
			button: { text: 'Auswählen', close: false },
			states: [
				new wp.media.controller.Library( {
					title: 'Foto einfügen (16:9)',
					library: wp.media.query( { type: 'image' } ),
					multiple: false,
					date: false,
					priority: 20,
				} ),
				new FotoCropper( { imgSelectOptions: imgSelectOptions } ),
			],
		} );

		function insert( attachment ) {
			editor.focus();
			editor.insertContent( imageHtml( attachment ) );
		}

		frame.on( 'select', function () {
			var attachment = frame.state().get( 'selection' ).first().toJSON();
			if ( isSixteenNine( attachment.width, attachment.height ) ) {
				insert( attachment );
				frame.close();
			} else {
				frame.setState( 'cropper' );
			}
		} );
		frame.on( 'cropped', insert );
		frame.open();
	}

	tinymce.PluginManager.add( 'elfzwo_foto', function ( editor ) {
		editor.addButton( 'elfzwo_foto', {
			icon: 'dashicon dashicons-format-image',
			tooltip: 'Foto einfügen (16:9)',
			onclick: function () {
				openFrame( editor );
			},
		} );
	} );
} )();
