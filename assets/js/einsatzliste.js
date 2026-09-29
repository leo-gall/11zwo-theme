( function () {
	var SELECTED_CLASSES   = [ 'bg-signal/20', 'text-signal' ];
	var UNSELECTED_CLASSES = [ 'bg-secondary', 'text-muted-foreground', 'hover:bg-border' ];

	var reduceMotion = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	function stripInlineHandlers( root ) {
		root.querySelectorAll( '[onchange]' ).forEach( function ( el ) {
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

	function updateYearScrollEdges( aside ) {
		var track = aside.querySelector( '.elfzwo-einsatzliste-years' );
		var prev  = aside.querySelector( '.elfzwo-einsatzliste-years-edge--left' );
		var next  = aside.querySelector( '.elfzwo-einsatzliste-years-edge--right' );
		if ( ! track || ! prev || ! next ) {
			return;
		}
		var max       = track.scrollWidth - track.clientWidth;
		var overflows = max > 1;
		prev.classList.toggle( 'is-active', overflows && track.scrollLeft > 1 );
		next.classList.toggle( 'is-active', overflows && track.scrollLeft < max - 1 );
	}

	/**
	 * Scrollt die Jahresleiste mit der nativen Smooth-Scroll-Animation.
	 * Die Leiste wird beim Jahreswechsel nicht mehr neu eingesetzt, daher
	 * startet jede Animation zuverlässig an der tatsächlichen Position.
	 */
	function scrollTrack( track, left, animate ) {
		var max = track.scrollWidth - track.clientWidth;
		left    = Math.max( 0, Math.min( left, max ) );
		if ( Math.abs( left - track.scrollLeft ) < 1 ) {
			return;
		}
		track.scrollTo( { left: left, behavior: animate && ! reduceMotion ? 'smooth' : 'auto' } );
	}

	/** Zentriert die ausgewählte Jahres-Badge (am Anfang/Ende auf den Rand begrenzt). */
	function centerSelectedYearChip( aside, animate ) {
		var track = aside.querySelector( '.elfzwo-einsatzliste-years' );
		if ( ! track ) {
			return;
		}
		var selected = track.querySelector( '.elfzwo-einsatzliste-year-chip[data-year="' + track.dataset.selectedYear + '"]' );
		if ( ! selected ) {
			return;
		}
		var chipRect  = selected.getBoundingClientRect();
		var trackRect = track.getBoundingClientRect();
		var target    = track.scrollLeft + ( chipRect.left - trackRect.left ) - ( track.clientWidth - chipRect.width ) / 2;
		scrollTrack( track, target, animate );
	}

	/** Markiert ein Jahr als ausgewählt, ohne die Leiste neu aufzubauen. */
	function selectYear( aside, year ) {
		var track = aside.querySelector( '.elfzwo-einsatzliste-years' );
		if ( ! track ) {
			return;
		}
		track.dataset.selectedYear = year;
		track.querySelectorAll( '.elfzwo-einsatzliste-year-chip' ).forEach( function ( chip ) {
			var isSelected = chip.dataset.year === String( year );
			SELECTED_CLASSES.forEach( function ( c ) {
				chip.classList.toggle( c, isSelected );
			} );
			UNSELECTED_CLASSES.forEach( function ( c ) {
				chip.classList.toggle( c, ! isSelected );
			} );
			if ( isSelected ) {
				chip.setAttribute( 'aria-current', 'true' );
			} else {
				chip.removeAttribute( 'aria-current' );
			}
		} );
	}

	function initEinsatzliste( aside ) {
		var postId       = aside.dataset.postId;
		var track        = aside.querySelector( '.elfzwo-einsatzliste-years' );
		var requestToken = 0;

		function load( jahr, seite, pushState ) {
			var yearChanged = track && track.dataset.selectedYear !== String( jahr );

			// Auswahl und Zentrierung sofort beim Klick, nicht erst nach der
			// Server-Antwort -- so läuft die Animation immer gleich.
			if ( yearChanged ) {
				selectYear( aside, jahr );
				centerSelectedYearChip( aside, true );
			}

			// Bei schnell aufeinanderfolgenden Klicks darf nur die zuletzt
			// ausgelöste Anfrage noch etwas anwenden.
			var myToken = ++requestToken;
			var body    = aside.querySelector( '.elfzwo-einsatzliste-body' );
			if ( body ) {
				body.classList.add( 'opacity-50' );
			}

			var ajaxUrl = elfzwoEinsatzliste.ajaxUrl + '?action=elfzwo_einsatzliste&post_id=' + encodeURIComponent( postId ) + '&jahr=' + encodeURIComponent( jahr ) + '&seite=' + encodeURIComponent( seite );

			fetch( ajaxUrl, { credentials: 'same-origin' } )
				.then( function ( r ) {
					return r.json();
				} )
				.then( function ( res ) {
					if ( myToken !== requestToken ) {
						return;
					}
					var currentBody = aside.querySelector( '.elfzwo-einsatzliste-body' );
					if ( ! res.success ) {
						if ( currentBody ) {
							currentBody.classList.remove( 'opacity-50' );
						}
						return;
					}

					// Nur Tabelle + Pagination austauschen; die Jahresleiste bleibt stehen.
					var tmp = document.createElement( 'div' );
					tmp.innerHTML = res.data.html;
					var newBody = tmp.querySelector( '.elfzwo-einsatzliste-body' );
					if ( currentBody && newBody ) {
						stripInlineHandlers( newBody );
						currentBody.replaceWith( newBody );
					}
					if ( String( res.data.jahr ) !== track.dataset.selectedYear ) {
						selectYear( aside, res.data.jahr );
						centerSelectedYearChip( aside, true );
					}

					if ( pushState ) {
						var url = new URL( window.location.href );
						url.searchParams.set( 'einsatz_jahr', res.data.jahr );
						url.searchParams.set( 'einsatz_seite', res.data.seite );
						window.history.pushState( {}, '', url );
					}
				} )
				.catch( function () {
					var currentBody = aside.querySelector( '.elfzwo-einsatzliste-body' );
					if ( myToken === requestToken && currentBody ) {
						currentBody.classList.remove( 'opacity-50' );
					}
				} );
		}

		stripInlineHandlers( aside );
		if ( track ) {
			track.addEventListener( 'scroll', function () {
				updateYearScrollEdges( aside );
			}, { passive: true } );
			centerSelectedYearChip( aside, false );
			updateYearScrollEdges( aside );
		}

		aside.addEventListener( 'click', function ( e ) {
			var edge = e.target.closest( '.elfzwo-einsatzliste-years-edge' );
			if ( edge && track ) {
				scrollTrack( track, track.scrollLeft + parseInt( edge.dataset.dir, 10 ) * track.clientWidth * 0.7, true );
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
				load( currentParams( aside ).jahr, pageLink.dataset.elfzwoPage, true );
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
