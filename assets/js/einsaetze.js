/**
 * Einsätze-Seite als Single-Page-App: Jahreswechsel lädt nur die Ansicht neu
 * (AJAX, Adresse per History-API), die Legende filtert die Tabelle nach
 * Einsatzart, ohne die Seite zu verlassen.
 */
( function () {
	var bereich = document.querySelector( '[data-einsaetze]' );
	if ( ! bereich || ! window.elfzwoEinsaetze ) {
		return;
	}
	var inhalt = bereich.querySelector( '[data-einsaetze-inhalt]' );
	var seite  = window.location.href.split( '?' )[ 0 ].split( '#' )[ 0 ];

	function filtere( art ) {
		var zeilen  = inhalt.querySelectorAll( 'tbody tr' );
		zeilen.forEach( function ( zeile ) {
			var passt = ! art || zeile.dataset.art === art;
			zeile.hidden = ! passt;
		} );
		inhalt.querySelectorAll( '[data-filter]' ).forEach( function ( knopf ) {
			knopf.setAttribute( 'aria-pressed', knopf.dataset.filter === art ? 'true' : 'false' );
		} );
		inhalt.querySelectorAll( 'circle[data-art]' ).forEach( function ( teil ) {
			teil.style.opacity = ! art || teil.dataset.art === art ? '1' : '.2';
		} );
	}

	function ladeJahr( jahr, verlauf ) {
		inhalt.style.opacity = '.4';
		var url = elfzwoEinsaetze.ajaxUrl + '?action=elfzwo_einsaetze&jahr=' + encodeURIComponent( jahr ) + '&seite=' + encodeURIComponent( seite );
		fetch( url, { credentials: 'same-origin' } )
			.then( function ( r ) { return r.json(); } )
			.then( function ( antwort ) {
				if ( ! antwort.success ) {
					throw new Error();
				}
				inhalt.innerHTML = antwort.data.html;
				inhalt.style.opacity = '';
				if ( verlauf ) {
					history.pushState( { jahr: jahr }, '', seite + '?einsatz_jahr=' + jahr );
				}
				var titel = inhalt.querySelector( '[data-einsaetze-titel]' );
				if ( titel ) {
					titel.focus( { preventScroll: true } );
				}
				if ( bereich.getBoundingClientRect().top < 0 ) {
					bereich.scrollIntoView( { behavior: 'smooth' } );
				}
			} )
			.catch( function () {
				window.location.href = seite + '?einsatz_jahr=' + jahr;
			} );
	}

	inhalt.addEventListener( 'click', function ( e ) {
		var jahr = e.target.closest( '[data-jahr]' );
		if ( jahr ) {
			e.preventDefault();
			ladeJahr( jahr.dataset.jahr, true );
			return;
		}
		var knopf = e.target.closest( '[data-filter]' );
		if ( knopf ) {
			filtere( 'true' === knopf.getAttribute( 'aria-pressed' ) ? '' : knopf.dataset.filter );
		}
	} );

	window.addEventListener( 'popstate', function () {
		var jahr = new URL( window.location.href ).searchParams.get( 'einsatz_jahr' ) || '';
		ladeJahr( jahr, false );
	} );
} )();
