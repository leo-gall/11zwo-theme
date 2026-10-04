/**
 * Eigene Umami-Events (siehe inc/analytics.php). Seitenaufrufe zählt Umami
 * selbst; hier kommen per Event-Delegation die Aktionen dazu, die für die
 * Feuerwehr interessant sind: Kontaktaufnahmen, Downloads, Mitmachen,
 * Klicks auf Call-to-Action-Buttons und die Nutzung von Liste/FAQ/Menü.
 */
( function () {
	function track( name, data ) {
		if ( window.umami && typeof window.umami.track === 'function' ) {
			window.umami.track( name, data );
		}
	}

	function text( el ) {
		return ( el.textContent || '' ).replace( /\s+/g, ' ' ).trim().slice( 0, 80 );
	}

	function seite() {
		return window.location.pathname;
	}

	document.addEventListener(
		'click',
		function ( e ) {
			var target = e.target;
			if ( ! target || ! target.closest ) {
				return;
			}

			var a = target.closest( 'a[href]' );
			if ( a ) {
				var href = a.getAttribute( 'href' );
				var url;
				try {
					url = new URL( href, window.location.href );
				} catch ( err ) {
					return;
				}

				if ( url.protocol === 'tel:' ) {
					track( 'Anruf', { nummer: decodeURIComponent( url.pathname ), seite: seite() } );
				} else if ( url.protocol === 'mailto:' ) {
					track( 'E-Mail', { adresse: decodeURIComponent( url.pathname ), seite: seite() } );
				} else if ( a.hasAttribute( 'download' ) || url.pathname.indexOf( '/wp-content/uploads/' ) !== -1 ) {
					track( 'Download', { datei: url.pathname.split( '/' ).pop(), titel: text( a ), seite: seite() } );
				} else if ( url.origin !== window.location.origin ) {
					track( 'Externer Link', { ziel: url.hostname, url: url.href.slice( 0, 200 ), seite: seite() } );
				} else if ( a.classList.contains( 'elfzwo-btn-primary' ) ) {
					track( 'CTA', { text: text( a ), ziel: url.pathname, seite: seite() } );
				}
				return;
			}

			var more = target.closest( '[data-next-page]' );
			if ( more ) {
				track( 'Mehr Beiträge geladen', { seite: more.getAttribute( 'data-next-page' ) } );
				return;
			}

			var chip = target.closest( '.elfzwo-einsatzliste-year-chip' );
			if ( chip ) {
				track( 'Einsatzjahr gewählt', { jahr: chip.getAttribute( 'data-year' ) || text( chip ) } );
				return;
			}

			var summary = target.closest( '.elfzwo-faq-item > summary' );
			if ( summary && ! summary.parentNode.open ) {
				track( 'FAQ geöffnet', { frage: text( summary ), seite: seite() } );
				return;
			}

			var toggle = target.closest( '#mobile-toggle' );
			if ( toggle && toggle.getAttribute( 'aria-expanded' ) !== 'true' ) {
				track( 'Mobiles Menü geöffnet' );
			}
		},
		true
	);

	// Mach-mit-Formular: Absenden (mit gewähltem Interesse) und Erfolgsseite.
	document.addEventListener( 'submit', function ( e ) {
		var form = e.target;
		if ( ! form || ! form.querySelector || ! form.querySelector( 'input[name="action"][value="elfzwo_mitmachen"]' ) ) {
			return;
		}
		var interesse = form.querySelector( 'input[name="interesse"]:checked' );
		track( 'Mitmachen abgeschickt', { interesse: interesse ? interesse.value : '', seite: seite() } );
	} );

	var status = new URLSearchParams( window.location.search ).get( 'mitmachen' );
	if ( status === 'success' || status === 'error' ) {
		var report = function () {
			track( status === 'success' ? 'Mitmachen erfolgreich' : 'Mitmachen fehlerhaft', { seite: seite() } );
		};
		// Das Umami-Skript lädt "defer" — erst nach dem Laden ist umami.track verfügbar.
		if ( window.umami ) {
			report();
		} else {
			window.addEventListener( 'load', report );
		}
	}
} )();
