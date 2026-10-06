/**
 * Bilder öffnen: Jedes Inhaltsbild, das nicht schon verlinkt ist, öffnet per
 * Klick groß in einem Overlay. Pfeiltasten bzw. Wischen blättern durch alle
 * Bilder der Seite, Escape oder ein Klick daneben schließt.
 */
( function () {
	var MIN = 120;
	var bilder = [];
	var aktuell = 0;
	var dialog, bild, text, zaehler, startX = null;

	function grosseQuelle( img ) {
		var beste = img.currentSrc || img.src;
		var breite = 0;
		( img.getAttribute( 'srcset' ) || '' ).split( ',' ).forEach( function ( teil ) {
			var t = teil.trim().split( /\s+/ );
			var w = parseInt( t[ 1 ], 10 ) || 0;
			if ( t[ 0 ] && w > breite ) {
				breite = w;
				beste = t[ 0 ];
			}
		} );
		var link = img.closest( 'a' );
		if ( link && /\.(jpe?g|png|webp|gif)(\?|$)/i.test( link.href ) ) {
			beste = link.href;
		}
		return beste;
	}

	function knopf( klasse, label, zeichen ) {
		var b = document.createElement( 'button' );
		b.type = 'button';
		b.className = klasse;
		b.setAttribute( 'aria-label', label );
		b.innerHTML = zeichen;
		return b;
	}

	function bauen() {
		dialog = document.createElement( 'dialog' );
		dialog.className = 'elfzwo-lightbox';
		dialog.setAttribute( 'aria-label', 'Bild' );
		bild = document.createElement( 'img' );
		bild.alt = '';
		text = document.createElement( 'p' );
		text.className = 'elfzwo-lightbox-text';
		zaehler = document.createElement( 'span' );
		zaehler.className = 'elfzwo-lightbox-zaehler';
		var zu = knopf( 'elfzwo-lightbox-zu', 'Schließen', '&times;' );
		var vor = knopf( 'elfzwo-lightbox-vor', 'Vorheriges Bild', '&#8249;' );
		var nach = knopf( 'elfzwo-lightbox-nach', 'Nächstes Bild', '&#8250;' );
		dialog.append( bild, text, zaehler, zu, vor, nach );
		document.body.appendChild( dialog );

		zu.addEventListener( 'click', function () { dialog.close(); } );
		vor.addEventListener( 'click', function () { zeigen( aktuell - 1 ); } );
		nach.addEventListener( 'click', function () { zeigen( aktuell + 1 ); } );
		dialog.addEventListener( 'click', function ( e ) {
			if ( e.target === dialog ) {
				dialog.close();
			}
		} );
		dialog.addEventListener( 'keydown', function ( e ) {
			if ( 'ArrowLeft' === e.key ) {
				zeigen( aktuell - 1 );
			} else if ( 'ArrowRight' === e.key ) {
				zeigen( aktuell + 1 );
			}
		} );
		dialog.addEventListener( 'touchstart', function ( e ) { startX = e.touches[ 0 ].clientX; }, { passive: true } );
		dialog.addEventListener( 'touchend', function ( e ) {
			if ( null === startX ) {
				return;
			}
			var dx = e.changedTouches[ 0 ].clientX - startX;
			startX = null;
			if ( Math.abs( dx ) > 50 ) {
				zeigen( aktuell + ( dx < 0 ? 1 : -1 ) );
			}
		} );
		dialog.addEventListener( 'close', function () { document.documentElement.style.overflow = ''; } );
	}

	function zeigen( index ) {
		aktuell = ( index + bilder.length ) % bilder.length;
		var img = bilder[ aktuell ];
		bild.src = grosseQuelle( img );
		var figur = img.closest( 'figure' );
		var unterschrift = figur && figur.querySelector( 'figcaption' );
		text.textContent = ( unterschrift && unterschrift.textContent.trim() ) || img.alt || '';
		zaehler.textContent = bilder.length > 1 ? ( aktuell + 1 ) + ' / ' + bilder.length : '';
		dialog.classList.toggle( 'is-einzeln', bilder.length < 2 );
	}

	function erfassen() {
		document.querySelectorAll( 'main img' ).forEach( function ( img ) {
			if ( img.dataset.lightbox || img.closest( 'a:not([href$=".jpg"]):not([href$=".jpeg"]):not([href$=".png"]):not([href$=".webp"]), button, dialog, header, [data-keine-lightbox]' ) ) {
				return;
			}
			if ( Math.max( img.naturalWidth, img.width ) < MIN && img.complete ) {
				return;
			}
			// Hintergrundfotos der Kopfbereiche sind Gestaltung, kein Inhalt.
			if ( img.classList.contains( '-z-20' ) ) {
				return;
			}
			// Liegt Text über dem Foto (z. B. Quereinsteiger-Karte), reagiert die ganze Fläche.
			var ziel = img.classList.contains( 'absolute' ) && img.parentElement ? img.parentElement : img;
			img.dataset.lightbox = '1';
			ziel.classList.add( 'elfzwo-zoombar' );
			ziel.addEventListener( 'click', function ( e ) {
				if ( ziel !== img && e.target.closest( 'a, button' ) ) {
					return;
				}
				e.preventDefault();
				bilder = Array.prototype.filter.call( document.querySelectorAll( 'img[data-lightbox]' ), function ( b ) { return b.offsetParent !== null; } );
				if ( ! dialog ) {
					bauen();
				}
				zeigen( Math.max( 0, bilder.indexOf( img ) ) );
				document.documentElement.style.overflow = 'hidden';
				dialog.showModal();
			} );
		} );
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', erfassen );
	} else {
		erfassen();
	}
	window.addEventListener( 'load', erfassen );
	var main = document.querySelector( 'main' );
	if ( main ) {
		new MutationObserver( erfassen ).observe( main, { childList: true, subtree: true } );
	}
} )();
