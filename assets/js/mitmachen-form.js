/**
 * Mach-mit-Formular: schickt im Hintergrund ab, ohne die Seite neu zu laden.
 * Bewusst ohne Bewegung: Während des Sendens wird der Button nur blasser,
 * danach steht an seiner Stelle der Dank in derselben Höhe; ein Fehler
 * ersetzt den Hinweistext daneben. Ohne JavaScript oder bei einem
 * Netzwerkfehler läuft das normale Absenden mit Weiterleitung.
 */
( function () {
	document.querySelectorAll( '.elfzwo-mitmachen-form' ).forEach( function ( form ) {
		var hinweis = form.querySelector( '.elfzwo-mitmachen-hinweis' );
		var hinweisHtml = hinweis ? hinweis.innerHTML : '';

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
					var danke = document.createElement( 'p' );
					danke.className = 'flex items-center font-semibold';
					danke.style.minHeight = button.offsetHeight + 'px';
					danke.setAttribute( 'role', 'status' );
					danke.textContent = 'Danke! Wir melden uns bei dir.';
					button.replaceWith( danke );
					if ( hinweis ) {
						hinweis.innerHTML = hinweisHtml;
						hinweis.classList.remove( 'font-semibold', 'text-white' );
					}
				} )
				.catch( function () {
					form.submit();
				} );
		} );
	} );
} )();
