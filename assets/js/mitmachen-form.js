/**
 * Mach-mit-Formular: Nach dem Absenden den Button sperren und einen Loader
 * zeigen, bis die Seite mit der Bestätigung neu lädt — verhindert auch
 * doppeltes Absenden.
 */
( function () {
	document.querySelectorAll( '.elfzwo-mitmachen-form' ).forEach( function ( form ) {
		form.addEventListener( 'submit', function () {
			var button = form.querySelector( 'button[type="submit"]' );
			if ( ! button || button.disabled ) {
				return;
			}
			button.disabled = true;
			button.setAttribute( 'aria-busy', 'true' );
			var idle = button.querySelector( '.elfzwo-submit-idle' );
			var busy = button.querySelector( '.elfzwo-submit-busy' );
			if ( idle ) { idle.classList.add( 'hidden' ); }
			if ( busy ) { busy.classList.remove( 'hidden' ); busy.classList.add( 'inline-flex' ); }
		} );
	} );

	// Zurück-Navigation aus dem Cache (bfcache): Button wieder freigeben.
	window.addEventListener( 'pageshow', function ( e ) {
		if ( ! e.persisted ) { return; }
		document.querySelectorAll( '.elfzwo-mitmachen-form button[type="submit"]' ).forEach( function ( button ) {
			button.disabled = false;
			button.removeAttribute( 'aria-busy' );
			var idle = button.querySelector( '.elfzwo-submit-idle' );
			var busy = button.querySelector( '.elfzwo-submit-busy' );
			if ( idle ) { idle.classList.remove( 'hidden' ); }
			if ( busy ) { busy.classList.add( 'hidden' ); busy.classList.remove( 'inline-flex' ); }
		} );
	} );
} )();
