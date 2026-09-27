( function () {
	function stripInlineHandlers( aside ) {
		aside.querySelectorAll( '[onchange]' ).forEach( function ( el ) {
			el.removeAttribute( 'onchange' );
		} );
	}

	function currentParams( aside ) {
		var years = aside.querySelector( '.elfzwo-einsatzliste-years' );
		var url   = new URL( window.location.href );
		return {
			jahr:  url.searchParams.get( 'einsatz_jahr' ) || ( years ? years.dataset.selectedYear : '' ),
			seite: url.searchParams.get( 'einsatz_seite' ) || '1',
		};
	}

	/**
	 * Setzt scrollLeft nativ/Compositor-gestützt statt per JS-rAF-Schleife --
	 * eine manuelle Schleife, die scrollLeft jeden Frame direkt setzt, erzwingt
	 * pro Frame einen synchronen Reflow und kollidiert sichtbar (Ruckeln/
	 * Flackern) mit nativem Scroll-Momentum bzw. mit sich selbst bei schnell
	 * aufeinanderfolgenden Aufrufen. Die CSS-Klasse "is-animating" schaltet
	 * "scroll-behavior:smooth" NUR für den einen Aufruf ein, damit die
	 * Positions-Wiederherstellung bei reiner Pagination weiterhin instant
	 * bleibt (siehe bindYearScroll unten).
	 */
	function setScrollLeft( el, target, animate ) {
		if ( ! animate ) {
			el.classList.remove( 'is-animating' );
			el.scrollLeft = target;
			return;
		}

		el.classList.add( 'is-animating' );

		function cleanup() {
			el.classList.remove( 'is-animating' );
			el.removeEventListener( 'scrollend', cleanup );
		}
		if ( 'onscrollend' in window ) {
			el.addEventListener( 'scrollend', cleanup, { once: true } );
		} else {
			window.setTimeout( cleanup, 600 );
		}

		// Ohne rAF fällt der Sprung auf scrollLeft=0 (bei frisch eingefügtem
		// Element) und das Scrollen zum Ziel in denselben Frame -- der Browser
		// hat dann nie einen sichtbaren Ausgangszustand gemalt und überspringt
		// die Animation einfach.
		requestAnimationFrame( function () {
			el.scrollLeft = target;
		} );
	}

	function updateYearScrollEdges( aside ) {
		var track = aside.querySelector( '.elfzwo-einsatzliste-years' );
		var prev  = aside.querySelector( '.elfzwo-einsatzliste-years-edge--left' );
		var next  = aside.querySelector( '.elfzwo-einsatzliste-years-edge--right' );
		if ( ! track || ! prev || ! next ) {
			return;
		}
		var overflows = track.scrollWidth > track.clientWidth + 1;
		prev.classList.toggle( 'is-active', overflows && track.scrollLeft > 0 );
		next.classList.toggle( 'is-active', overflows && track.scrollLeft < track.scrollWidth - track.clientWidth - 1 );
	}

	/**
	 * Zentriert die ausgewählte Jahres-Badge in der Leiste -- aber nur, wenn
	 * links davon noch eine andere Badge steht (sonst gibt's nichts, wohin
	 * man "zentrieren" könnte, und die Leiste soll einfach am Anfang bleiben).
	 */
	function centerSelectedYearChip( aside, animate ) {
		var track = aside.querySelector( '.elfzwo-einsatzliste-years' );
		if ( ! track ) {
			return;
		}
		var chips    = Array.prototype.slice.call( track.querySelectorAll( '.elfzwo-einsatzliste-year-chip' ) );
		var selected = track.querySelector( '.elfzwo-einsatzliste-year-chip[data-year="' + track.dataset.selectedYear + '"]' );
		if ( ! selected ) {
			return;
		}
		var index  = chips.indexOf( selected );
		var target = index <= 0 ? 0 : selected.offsetLeft - ( track.clientWidth / 2 ) + ( selected.clientWidth / 2 );
		target     = Math.max( 0, Math.min( target, track.scrollWidth - track.clientWidth ) );

		setScrollLeft( track, target, animate );
	}

	/**
	 * options.seedScrollLeft: ein frisch per innerHTML eingefügtes Element
	 * startet IMMER bei scrollLeft=0, unabhängig davon, wo der Nutzer gerade
	 * war -- ohne das hier zuerst instant zu korrigieren, würde jede
	 * folgende Animation künstlich "von 0" starten statt vom echten
	 * Ausgangspunkt (mal zufällig keine sichtbare Bewegung, wenn das neue
	 * Ziel ebenfalls 0 ist; mal eine unnötig lange Strecke quer über die
	 * ganze Leiste statt einer kurzen Verschiebung).
	 * options.animateCenter: beim echten Jahreswechsel (AJAX) soll die neue
	 * Auswahl sichtbar smooth zentriert werden (ausgehend von der eben
	 * wiederhergestellten Position) -- bei reiner Seiten-Pagination
	 * (Jahr bleibt gleich) dagegen NICHTS weiter tun, nur die Position 1:1
	 * wiederherstellen, sonst "springt"/animiert die Leiste bei jedem
	 * Blättern, obwohl sich an ihrer Position gar nichts ändern soll.
	 */
	function bindYearScroll( aside, options ) {
		options = options || {};
		var track = aside.querySelector( '.elfzwo-einsatzliste-years' );
		if ( ! track ) {
			return;
		}
		track.addEventListener( 'scroll', function () {
			updateYearScrollEdges( aside );
		}, { passive: true } );

		if ( null != options.seedScrollLeft ) {
			setScrollLeft( track, options.seedScrollLeft, false );
		}

		if ( options.animateCenter ) {
			centerSelectedYearChip( aside, true );
		} else if ( null == options.seedScrollLeft ) {
			// Allererster Seitenaufbau: keine vorherige Position vorhanden.
			centerSelectedYearChip( aside, false );
		}

		updateYearScrollEdges( aside );
	}

	function initEinsatzliste( aside ) {
		var postId       = aside.dataset.postId;
		var requestToken = 0;

		function load( jahr, seite, pushState ) {
			var trackBefore     = aside.querySelector( '.elfzwo-einsatzliste-years' );
			var yearBefore      = trackBefore ? trackBefore.dataset.selectedYear : null;
			var scrollLeftBefore = trackBefore ? trackBefore.scrollLeft : 0;

			// Bei schnell aufeinanderfolgenden Klicks (z. B. zwei Jahre kurz
			// hintereinander) kann die AJAX-Antwort in beliebiger Reihenfolge
			// zurückkommen -- ohne diese Absicherung würde eine ältere Antwort
			// eine neuere überschreiben, was sich wie "Klick tut manchmal
			// nichts" anfühlt. Nur die zuletzt AUSGELÖSTE Anfrage darf noch
			// etwas anwenden.
			var myToken = ++requestToken;

			aside.classList.add( 'opacity-50' );
			var ajaxUrl = elfzwoEinsatzliste.ajaxUrl + '?action=elfzwo_einsatzliste&post_id=' + encodeURIComponent( postId ) + '&jahr=' + encodeURIComponent( jahr ) + '&seite=' + encodeURIComponent( seite );

			fetch( ajaxUrl, { credentials: 'same-origin' } )
				.then( function ( r ) {
					return r.json();
				} )
				.then( function ( res ) {
					if ( myToken !== requestToken ) {
						return;
					}
					if ( ! res.success ) {
						aside.classList.remove( 'opacity-50' );
						return;
					}
					var sameYear = null !== yearBefore && yearBefore === String( res.data.jahr );
					aside.innerHTML = res.data.html;
					stripInlineHandlers( aside );
					bindYearScroll( aside, {
						seedScrollLeft: scrollLeftBefore,
						animateCenter: ! sameYear,
					} );
					aside.classList.remove( 'opacity-50' );

					if ( pushState ) {
						var url = new URL( window.location.href );
						url.searchParams.set( 'einsatz_jahr', res.data.jahr );
						url.searchParams.set( 'einsatz_seite', res.data.seite );
						window.history.pushState( {}, '', url );
					}
				} )
				.catch( function () {
					if ( myToken === requestToken ) {
						aside.classList.remove( 'opacity-50' );
					}
				} );
		}

		stripInlineHandlers( aside );
		bindYearScroll( aside );

		aside.addEventListener( 'click', function ( e ) {
			var edge = e.target.closest( '.elfzwo-einsatzliste-years-edge' );
			if ( edge ) {
				var track = aside.querySelector( '.elfzwo-einsatzliste-years' );
				if ( track ) {
					var delta  = parseInt( edge.dataset.dir, 10 ) * track.clientWidth * 0.7;
					var target = Math.max( 0, Math.min( track.scrollLeft + delta, track.scrollWidth - track.clientWidth ) );
					setScrollLeft( track, target, true );
				}
				return;
			}

			var yearChip = e.target.closest( '.elfzwo-einsatzliste-year-chip' );
			if ( yearChip ) {
				e.preventDefault();
				load( yearChip.dataset.year, 1, true );
				return;
			}

			var pageLink = e.target.closest( '.elfzwo-einsatzliste-page' );
			if ( pageLink ) {
				e.preventDefault();
				var params = currentParams( aside );
				load( params.jahr, pageLink.dataset.elfzwoPage, true );
			}
		} );

		window.addEventListener( 'resize', function () {
			updateYearScrollEdges( aside );
		} );

		window.addEventListener( 'popstate', function () {
			var params = currentParams( aside );
			load( params.jahr, params.seite, false );
		} );
	}

	document.addEventListener( 'DOMContentLoaded', function () {
		document.querySelectorAll( '.elfzwo-einsatzliste' ).forEach( initEinsatzliste );
	} );
} )();
