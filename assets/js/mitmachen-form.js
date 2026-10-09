/**
 * Mach-mit-Formular: schickt im Hintergrund ab, ohne die Seite neu zu laden.
 * Bewusst ohne Bewegung: Während des Sendens wird der Button nur blasser,
 * danach ersetzt der Dank das ganze Formular in derselben Höhe; ein Fehler
 * ersetzt den Hinweistext unter dem Button. Ohne JavaScript oder bei einem
 * Netzwerkfehler läuft das normale Absenden mit Weiterleitung.
 */
( function () {
	document.querySelectorAll( '.elfzwo-mitmachen-form' ).forEach( function ( form ) {
		var hinweis = form.querySelector( '.elfzwo-mitmachen-hinweis' );

		form.addEventListener( 'submit', function ( e ) {
			var button = form.querySelector( 'button[type="submit"]' );
			if ( ! button || button.disabled || ! window.fetch ) {
				return;
			}
			e.preventDefault();
			button.disabled = true;

			var daten = new FormData( form );
			daten.append( 'ajax', '1' );
			// Nicht form.action: das versteckte Feld name="action" überdeckt die Eigenschaft.
			fetch( form.getAttribute( 'action' ), { method: 'POST', body: daten, credentials: 'same-origin' } )
				.then( function ( r ) { return r.json(); } )
				.then( function ( antwort ) {
					if ( 'success' !== antwort.status ) {
						button.disabled = false;
						if ( hinweis ) {
							hinweis.textContent = 'Das hat nicht geklappt. Bitte versuche es noch einmal.';
							hinweis.classList.add( 'font-semibold', 'text-white' );
						}
						return;
					}
					// Der Dank ersetzt das ganze Formular in genau dessen Höhe, damit darunter nichts springt.
					var vorlage = form.parentNode.querySelector( '.elfzwo-mitmachen-danke' );
					var danke = vorlage.content.firstElementChild.cloneNode( true );
					danke.classList.remove( 'min-h-[19rem]' );
					var bereich = form.parentNode;
					bereich.style.height = bereich.getBoundingClientRect().height + 'px';
					bereich.style.display = 'flex';
					form.replaceWith( danke );
				} )
				.catch( function () {
					form.submit();
				} );
		} );
	} );
} )();
