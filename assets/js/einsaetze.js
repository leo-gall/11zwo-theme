/**
 * Einsätze-Seite als Single-Page-App: Jahreswechsel lädt nur die Ansicht neu
 * (AJAX, Adresse per History-API), die Legende filtert die Tabelle nach
 * Einsatzart und ein Klick auf eine Zeile klappt die Details darunter auf.
 */
( function () {
	var bereich = document.querySelector( '[data-einsaetze]' );
	if ( ! bereich || ! window.elfzwoEinsaetze ) {
		return;
	}
	var inhalt = bereich.querySelector( '[data-einsaetze-inhalt]' );
	var seite  = window.location.href.split( '?' )[ 0 ].split( '#' )[ 0 ];

	function filtere( art ) {
		inhalt.querySelectorAll( '[data-zeile]' ).forEach( function ( zeile ) {
			zeile.hidden = !! art && zeile.dataset.art !== art;
			if ( zeile.hidden ) {
				aufklappen( zeile, false );
			}
			zeile.nextElementSibling.hidden = zeile.hidden;
		} );
		inhalt.querySelectorAll( '[data-filter]' ).forEach( function ( knopf ) {
			knopf.setAttribute( 'aria-pressed', knopf.dataset.filter === art ? 'true' : 'false' );
		} );
		inhalt.querySelectorAll( 'svg [data-art]' ).forEach( function ( teil ) {
			teil.style.opacity = ! art || teil.dataset.art === art ? '1' : '.2';
		} );
	}

	function aufklappen( zeile, offen ) {
		var knopf = zeile.querySelector( '[data-details]' );
		var details = document.getElementById( knopf.getAttribute( 'aria-controls' ) );
		if ( offen && 'true' !== knopf.getAttribute( 'aria-expanded' ) && window.elfzwoTrack ) {
			window.elfzwoTrack( 'Einsatz angesehen', {
				nummer: zeile.dataset.einsatzNummer,
				einsatz: zeile.dataset.einsatzTitel,
				datum: zeile.dataset.einsatzDatum,
				ort: zeile.dataset.einsatzOrt,
			} );
		}
		knopf.setAttribute( 'aria-expanded', offen ? 'true' : 'false' );
		zeile.toggleAttribute( 'data-offen', offen );
		details.toggleAttribute( 'data-offen', offen );
	}

	function breiteMerken() {
		var tabelle = inhalt.querySelector( '[data-einsaetze-tabelle]' );
		if ( tabelle ) {
			tabelle.style.setProperty( '--einsatz-breite', tabelle.clientWidth + 'px' );
		}
	}

	function ausAdresse() {
		var zeile = window.location.hash && inhalt.querySelector( window.location.hash + '[data-zeile]' );
		if ( zeile ) {
			aufklappen( zeile, true );
			// Erst nach dem Laden scrollen, sonst verschieben nachladende Bilder die Zeile wieder.
			window.addEventListener( 'load', function () { zeile.scrollIntoView( { block: 'start' } ); } );
		}
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
				breiteMerken();
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
			return;
		}
		var zeile = e.target.closest( '[data-zeile]' );
		if ( zeile ) {
			aufklappen( zeile, 'true' !== zeile.querySelector( '[data-details]' ).getAttribute( 'aria-expanded' ) );
		}
	} );

	breiteMerken();
	window.addEventListener( 'resize', breiteMerken );
	ausAdresse();

	window.addEventListener( 'popstate', function () {
		var jahr = new URL( window.location.href ).searchParams.get( 'einsatz_jahr' ) || '';
		ladeJahr( jahr, false );
	} );
} )();
