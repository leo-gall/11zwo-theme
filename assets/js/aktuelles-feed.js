( function () {
	function initFeed( section ) {
		var grid     = section.querySelector( '.elfzwo-aktuelles-feed-grid' );
		var moreWrap = section.querySelector( '.elfzwo-aktuelles-feed-more' );
		if ( ! grid || ! moreWrap ) {
			return;
		}
		var button = moreWrap.querySelector( 'button' );

		button.addEventListener( 'click', function () {
			var page       = button.dataset.nextPage;
			var origLabel  = button.textContent;
			button.disabled = true;
			button.textContent = 'Lädt …';

			var url = elfzwoAktuellesFeed.ajaxUrl + '?action=elfzwo_aktuelles_feed&seite=' + encodeURIComponent( page );
			fetch( url, { credentials: 'same-origin' } )
				.then( function ( r ) {
					return r.json();
				} )
				.then( function ( res ) {
					if ( ! res.success ) {
						button.disabled = false;
						button.textContent = origLabel;
						return;
					}
					grid.insertAdjacentHTML( 'beforeend', res.data.html );
					if ( res.data.hasMore ) {
						button.dataset.nextPage = parseInt( page, 10 ) + 1;
						button.disabled = false;
						button.textContent = origLabel;
					} else {
						moreWrap.remove();
					}
				} )
				.catch( function () {
					button.disabled = false;
					button.textContent = origLabel;
				} );
		} );
	}

	document.addEventListener( 'DOMContentLoaded', function () {
		document.querySelectorAll( '.elfzwo-aktuelles-feed-grid' ).forEach( function ( grid ) {
			initFeed( grid.closest( 'section' ) );
		} );
	} );
} )();
