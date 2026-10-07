/**
 * Eigene Umami-Events (siehe inc/analytics.php): Mach-mit-Formular, Downloads,
 * angesehene Einsätze (assets/js/einsaetze.js) und Beiträge. Seitenaufrufe
 * zählt Umami selbst. Das Umami-Skript lädt "defer"; Events davor warten in
 * einer Schlange.
 */
( function () {
	var warteschlange = [];

	function bereit() {
		return window.umami && typeof window.umami.track === 'function';
	}

	function track( name, data ) {
		if ( bereit() ) {
			window.umami.track( name, data );
		} else {
			warteschlange.push( [ name, data ] );
		}
	}

	window.elfzwoTrack = track;

	window.addEventListener( 'load', function () {
		if ( bereit() ) {
			warteschlange.splice( 0 ).forEach( function ( e ) { window.umami.track( e[ 0 ], e[ 1 ] ); } );
		}
	} );

	document.addEventListener(
		'click',
		function ( e ) {
			var a = e.target && e.target.closest && e.target.closest( 'a[href]' );
			if ( ! a ) {
				return;
			}
			var url;
			try {
				url = new URL( a.getAttribute( 'href' ), window.location.href );
			} catch ( err ) {
				return;
			}
			if ( ! a.hasAttribute( 'download' ) && url.pathname.indexOf( '/wp-content/uploads/' ) === -1 ) {
				return;
			}
			var datei = decodeURIComponent( url.pathname.split( '/' ).pop() );
			track( 'Download', {
				titel: a.dataset.downloadTitel || ( a.textContent || '' ).replace( /\s+/g, ' ' ).trim().slice( 0, 120 ) || datei,
				kategorie: a.dataset.downloadKategorie || '',
				datei: datei,
				typ: ( datei.split( '.' ).pop() || '' ).toUpperCase(),
				url: url.href,
				seite: window.location.pathname,
			} );
		},
		true
	);

	document.addEventListener( 'submit', function ( e ) {
		var form = e.target;
		if ( ! form || ! form.elements || ! form.elements.action || 'elfzwo_mitmachen' !== form.elements.action.value ) {
			return;
		}
		track( 'Mach mit abgeschickt', {
			name: form.elements.name.value.trim(),
			email: form.elements.kontakt.value.trim(),
			interesse: form.elements.interesse ? form.elements.interesse.value : '',
		} );
	} );

	document.addEventListener( 'DOMContentLoaded', function () {
		var beitrag = document.querySelector( '[data-beitrag]' );
		if ( beitrag ) {
			track( 'Beitrag angesehen', {
				beitrag: beitrag.dataset.beitrag,
				datum: beitrag.dataset.beitragDatum,
				url: window.location.pathname,
			} );
		}
	} );
} )();
