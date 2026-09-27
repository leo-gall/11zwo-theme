/**
 * Öffnet/schließt das Modal mit allen NINA-Warnmeldungen (siehe
 * elfzwo_nina_render_compact() in inc/nina.php). No-op, wenn auf der Seite
 * kein Warnungs-Button vorhanden ist.
 */
( function () {
	function openModal( modal ) {
		modal.classList.remove( 'hidden' );
		modal.classList.add( 'flex' );
	}

	function closeModal( modal ) {
		modal.classList.add( 'hidden' );
		modal.classList.remove( 'flex' );
	}

	document.addEventListener( 'click', function ( e ) {
		var toggle = e.target.closest( '.elfzwo-nina-toggle' );
		if ( toggle ) {
			var modal = document.getElementById( toggle.getAttribute( 'aria-controls' ) );
			if ( modal ) {
				openModal( modal );
			}
			return;
		}
		if ( e.target.closest( '[data-nina-close]' ) ) {
			var openModals = document.querySelectorAll( '.elfzwo-nina-modal:not(.hidden)' );
			openModals.forEach( closeModal );
		}
	} );

	document.addEventListener( 'keydown', function ( e ) {
		if ( 'Escape' !== e.key ) {
			return;
		}
		document.querySelectorAll( '.elfzwo-nina-modal:not(.hidden)' ).forEach( closeModal );
	} );
} )();
