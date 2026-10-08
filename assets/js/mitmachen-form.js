/**
 * Mach-mit-Formular: schickt im Hintergrund ab, ohne die Seite neu zu laden.
 * Während des Sendens ist der Button gesperrt (kein doppeltes Absenden),
 * danach ersetzt die Bestätigung den Button (ohne Button lässt sich das
 * Formular auch per Enter nicht erneut abschicken). Ohne JavaScript oder bei einem
 * Netzwerkfehler läuft das normale Absenden mit Weiterleitung.
 */
( function () {
	var FEHLER = 'Das hat nicht geklappt. Bitte versuche es noch einmal.';

	function setzeBusy( button, busy ) {
		button.disabled = busy;
		button.toggleAttribute( 'aria-busy', busy );
		button.querySelector( '.elfzwo-submit-idle' ).classList.toggle( 'hidden', busy );
		button.querySelector( '.elfzwo-submit-busy' ).classList.toggle( 'hidden', ! busy );
	}

	function zeigeFehler( form ) {
		var hinweis = form.querySelector( '.elfzwo-mitmachen-fehler' );
		if ( ! hinweis ) {
			hinweis = document.createElement( 'p' );
			hinweis.className = 'elfzwo-mitmachen-fehler bg-white px-3 py-2 font-semibold text-signal';
			hinweis.setAttribute( 'role', 'alert' );
			hinweis.textContent = FEHLER;
			form.insertBefore( hinweis, form.querySelector( '.grid' ) );
		}
	}

	document.querySelectorAll( '.elfzwo-mitmachen-form' ).forEach( function ( form ) {
		form.addEventListener( 'submit', function ( e ) {
			var button = form.querySelector( 'button[type="submit"]' );
			if ( ! button || button.disabled || ! window.fetch ) {
				return;
			}
			e.preventDefault();
			setzeBusy( button, true );

			var daten = new FormData( form );
			daten.append( 'ajax', '1' );
			// Nicht form.action: das versteckte Feld name="action" überdeckt die Eigenschaft.
			fetch( form.getAttribute( 'action' ), { method: 'POST', body: daten, credentials: 'same-origin' } )
				.then( function ( r ) { return r.json(); } )
				.then( function ( antwort ) {
					if ( 'success' !== antwort.status ) {
						setzeBusy( button, false );
						zeigeFehler( form );
						return;
					}
					var alt = form.querySelector( '.elfzwo-mitmachen-fehler' );
					if ( alt ) {
						alt.remove();
					}
					var danke = document.createElement( 'p' );
					danke.className = 'border-l-4 border-white pl-4 text-lg font-semibold outline-none';
					danke.setAttribute( 'role', 'status' );
					danke.tabIndex = -1;
					danke.textContent = 'Danke! Wir melden uns in den nächsten Tagen bei dir.';
					button.replaceWith( danke );
					danke.focus( { preventScroll: true } );
				} )
				.catch( function () {
					// Im Zweifel klassisch absenden, damit keine Anfrage verloren geht.
					form.submit();
				} );
		} );
	} );
} )();
