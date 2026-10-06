/**
 * Suchfeld mit Vorschlägen: ab zwei Zeichen fragt das Feld die WordPress-
 * Suche ab (elfzwo/v1/suche) und zeigt bis zu sechs Treffer. Pfeiltasten wählen
 * aus, Enter öffnet den gewählten Treffer, Escape schließt die Liste.
 */
( function () {
	document.querySelectorAll( '[data-suche]' ).forEach( function ( form ) {
		var feld = form.querySelector( 'input[type=search]' );
		var liste = form.querySelector( '[data-vorschlaege]' );
		var treffer = [];
		var aktiv = -1;
		var timer = null;
		var anfrage = 0;

		function schliessen() {
			liste.classList.add( 'hidden' );
			feld.setAttribute( 'aria-expanded', 'false' );
			feld.removeAttribute( 'aria-activedescendant' );
			aktiv = -1;
		}

		function markieren( index ) {
			aktiv = index;
			liste.querySelectorAll( '[role=option]' ).forEach( function ( eintrag, i ) {
				var an = i === aktiv;
				eintrag.setAttribute( 'aria-selected', an ? 'true' : 'false' );
				eintrag.classList.toggle( 'bg-cream', an );
				if ( an ) {
					feld.setAttribute( 'aria-activedescendant', eintrag.id );
				}
			} );
		}

		function zeigen( ergebnisse, begriff ) {
			treffer = ergebnisse;
			liste.innerHTML = '';
			if ( ! ergebnisse.length ) {
				var leer = document.createElement( 'li' );
				leer.className = 'px-4 py-3 text-smoke';
				leer.textContent = 'Keine Treffer für „' + begriff + '“.';
				liste.appendChild( leer );
			}
			ergebnisse.forEach( function ( eintrag, i ) {
				var li = document.createElement( 'li' );
				var a = document.createElement( 'a' );
				a.id = 'elfzwo-suche-option-' + i;
				a.href = eintrag.url;
				a.setAttribute( 'role', 'option' );
				a.className = 'flex items-baseline justify-between gap-4 border-t border-border px-4 py-3 first:border-t-0 hover:bg-cream';
				var titel = document.createElement( 'span' );
				titel.className = 'font-semibold';
				titel.textContent = eintrag.titel;
				var typ = document.createElement( 'span' );
				typ.className = 'shrink-0 text-sm text-smoke';
				typ.textContent = eintrag.typ;
				a.appendChild( titel );
				a.appendChild( typ );
				li.appendChild( a );
				liste.appendChild( li );
			} );
			liste.classList.remove( 'hidden' );
			feld.setAttribute( 'aria-expanded', 'true' );
			aktiv = -1;
		}

		function suchen( begriff ) {
			var nummer = ++anfrage;
			fetch( elfzwoSuche.restUrl + '?q=' + encodeURIComponent( begriff ) )
				.then( function ( antwort ) { return antwort.json(); } )
				.then( function ( ergebnisse ) {
					if ( nummer === anfrage && feld.value.trim() === begriff ) {
						zeigen( Array.isArray( ergebnisse ) ? ergebnisse : [], begriff );
					}
				} )
				.catch( schliessen );
		}

		feld.addEventListener( 'input', function () {
			clearTimeout( timer );
			var begriff = feld.value.trim();
			if ( begriff.length < 2 ) {
				schliessen();
				return;
			}
			timer = setTimeout( function () { suchen( begriff ); }, 200 );
		} );

		feld.addEventListener( 'keydown', function ( e ) {
			if ( liste.classList.contains( 'hidden' ) || ! treffer.length ) {
				return;
			}
			if ( 'ArrowDown' === e.key ) {
				e.preventDefault();
				markieren( ( aktiv + 1 ) % treffer.length );
			} else if ( 'ArrowUp' === e.key ) {
				e.preventDefault();
				markieren( aktiv <= 0 ? treffer.length - 1 : aktiv - 1 );
			} else if ( 'Enter' === e.key && aktiv >= 0 ) {
				e.preventDefault();
				window.location.href = treffer[ aktiv ].url;
			} else if ( 'Escape' === e.key ) {
				schliessen();
			}
		} );

		document.addEventListener( 'click', function ( e ) {
			if ( ! form.contains( e.target ) ) {
				schliessen();
			}
		} );
	} );
} )();
