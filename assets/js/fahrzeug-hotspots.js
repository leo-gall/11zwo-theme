/**
 * Klickpunkte auf den Fahrzeugbildern (Block "Fahrzeuge-Liste"): ein Klick
 * öffnet das Foto des Gerätefachs mit Beschreibung in einem <dialog>. Der
 * Inhalt kommt aus dem <template> direkt hinter dem jeweiligen Punkt; mit
 * Pfeiltasten bzw. den Buttons blättert man durch die Fächer desselben
 * Fahrzeugs.
 */
( function () {
	var dialog = document.querySelector( '.fahrzeug-modal' );
	if ( ! dialog || typeof dialog.showModal !== 'function' ) {
		return;
	}

	var body    = dialog.querySelector( '.fahrzeug-modal-body' );
	var prev    = dialog.querySelector( '.fahrzeug-modal-prev' );
	var next    = dialog.querySelector( '.fahrzeug-modal-next' );
	var zaehler = dialog.querySelector( '.fahrzeug-modal-zaehler' );
	var spots   = [];
	var index   = 0;

	function show( i ) {
		index = i;
		var spot = spots[ i ];
		body.innerHTML = '';
		body.appendChild( spot.nextElementSibling.content.cloneNode( true ) );

		var titel = body.querySelector( '.fahrzeug-modal-titel' );
		if ( titel ) {
			titel.id = 'fahrzeug-modal-titel';
			dialog.setAttribute( 'aria-labelledby', titel.id );
		} else {
			dialog.removeAttribute( 'aria-labelledby' );
		}

		prev.disabled = i === 0;
		next.disabled = i === spots.length - 1;
		zaehler.textContent = spots.length > 1 ? ( i + 1 ) + ' / ' + spots.length : '';
		dialog.scrollTop = 0;

		if ( window.umami && typeof window.umami.track === 'function' ) {
			var article = spot.closest( '[data-fahrzeug]' );
			window.umami.track( 'Gerätefach geöffnet', {
				fahrzeug: article ? article.getAttribute( 'data-fahrzeug' ) : '',
				fach: titel ? titel.textContent.trim() : String( i + 1 ),
			} );
		}
	}

	document.addEventListener( 'click', function ( e ) {
		var spot = e.target.closest( '.fahrzeug-hotspot' );
		if ( ! spot ) {
			return;
		}
		var article = spot.closest( '[data-fahrzeug]' );
		spots = Array.prototype.slice.call( article.querySelectorAll( '.fahrzeug-hotspot' ) );
		show( spots.indexOf( spot ) );
		dialog.showModal();
	} );

	prev.addEventListener( 'click', function () {
		if ( index > 0 ) {
			show( index - 1 );
		}
	} );
	next.addEventListener( 'click', function () {
		if ( index < spots.length - 1 ) {
			show( index + 1 );
		}
	} );

	dialog.querySelector( '.fahrzeug-modal-close' ).addEventListener( 'click', function () {
		dialog.close();
	} );

	// Klick auf den abgedunkelten Hintergrund schließt das Modal.
	dialog.addEventListener( 'click', function ( e ) {
		if ( e.target === dialog ) {
			dialog.close();
		}
	} );

	dialog.addEventListener( 'keydown', function ( e ) {
		if ( e.key === 'ArrowLeft' && index > 0 ) {
			show( index - 1 );
		} else if ( e.key === 'ArrowRight' && index < spots.length - 1 ) {
			show( index + 1 );
		}
	} );
} )();
