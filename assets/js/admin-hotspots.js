/**
 * Klickpunkt-Editor für die Gerätefächer eines Fahrzeugs (Feldtyp "hotspots",
 * inc/meta-boxes.php): Klick ins Beitragsbild setzt einen neuen Punkt, Punkte
 * lassen sich per Drag & Drop verschieben. Die Position wird in Prozent der
 * Bildbreite/-höhe gespeichert, damit sie auf jeder Bildgröße passt.
 */
( function () {
	function clamp( v ) {
		return Math.round( Math.min( 100, Math.max( 0, v ) ) * 100 ) / 100;
	}

	function initHotspots( wrap ) {
		var stage    = wrap.querySelector( '.elfzwo-hotspots-stage' );
		var img      = stage.querySelector( 'img' );
		var rows     = wrap.querySelector( '.elfzwo-hotspots-rows' );
		var template = wrap.querySelector( '.elfzwo-hotspots-template' );
		var dragging = null;

		function rowList() {
			return Array.prototype.slice.call( rows.querySelectorAll( '.elfzwo-hotspot-row' ) );
		}

		function position( e ) {
			var rect = img.getBoundingClientRect();
			return {
				x: clamp( ( e.clientX - rect.left ) / rect.width * 100 ),
				y: clamp( ( e.clientY - rect.top ) / rect.height * 100 ),
			};
		}

		function setActive( row, active ) {
			row.classList.toggle( 'is-active', active );
			if ( row._marker ) {
				row._marker.classList.toggle( 'is-active', active );
			}
		}

		/** Zeichnet alle Marker neu und nummeriert Zeilen und Punkte gleich durch. */
		function render() {
			stage.querySelectorAll( '.elfzwo-hotspot-marker' ).forEach( function ( m ) {
				m.remove();
			} );
			rowList().forEach( function ( row, i ) {
				var marker = document.createElement( 'button' );
				marker.type        = 'button';
				marker.className   = 'elfzwo-hotspot-marker';
				marker.textContent = String( i + 1 );
				marker.style.left  = row.querySelector( '.elfzwo-hotspot-x' ).value + '%';
				marker.style.top   = row.querySelector( '.elfzwo-hotspot-y' ).value + '%';
				marker._row        = row;
				row._marker        = marker;
				row.dataset.nummer = String( i + 1 );
				stage.appendChild( marker );
			} );
		}

		function addRow( pos ) {
			var html = template.innerHTML.split( '__INDEX__' ).join( Date.now() );
			var tmp  = document.createElement( 'div' );
			tmp.innerHTML = html.trim();
			var row = tmp.firstElementChild;
			row.querySelector( '.elfzwo-hotspot-x' ).value = pos.x;
			row.querySelector( '.elfzwo-hotspot-y' ).value = pos.y;
			rows.appendChild( row );
			render();
			row.querySelector( '.elfzwo-hotspot-titel' ).focus();
		}

		stage.addEventListener( 'click', function ( e ) {
			var marker = e.target.closest( '.elfzwo-hotspot-marker' );
			if ( marker ) {
				marker._row.querySelector( '.elfzwo-hotspot-titel' ).focus();
				return;
			}
			addRow( position( e ) );
		} );

		stage.addEventListener( 'pointerdown', function ( e ) {
			var marker = e.target.closest( '.elfzwo-hotspot-marker' );
			if ( ! marker ) {
				return;
			}
			dragging = marker;
			marker.setPointerCapture( e.pointerId );
			marker.classList.add( 'is-dragging' );
		} );

		stage.addEventListener( 'pointermove', function ( e ) {
			if ( ! dragging ) {
				return;
			}
			var pos = position( e );
			dragging.style.left = pos.x + '%';
			dragging.style.top  = pos.y + '%';
			dragging._row.querySelector( '.elfzwo-hotspot-x' ).value = pos.x;
			dragging._row.querySelector( '.elfzwo-hotspot-y' ).value = pos.y;
		} );

		function endDrag() {
			if ( dragging ) {
				dragging.classList.remove( 'is-dragging' );
				dragging = null;
			}
		}
		stage.addEventListener( 'pointerup', endDrag );
		stage.addEventListener( 'pointercancel', endDrag );

		stage.addEventListener( 'mouseover', function ( e ) {
			var marker = e.target.closest( '.elfzwo-hotspot-marker' );
			rowList().forEach( function ( row ) {
				setActive( row, !! marker && marker._row === row );
			} );
		} );
		stage.addEventListener( 'mouseleave', function () {
			rowList().forEach( function ( row ) {
				setActive( row, false );
			} );
		} );

		rows.addEventListener( 'mouseover', function ( e ) {
			var current = e.target.closest( '.elfzwo-hotspot-row' );
			rowList().forEach( function ( row ) {
				setActive( row, row === current );
			} );
		} );
		rows.addEventListener( 'mouseleave', function () {
			rowList().forEach( function ( row ) {
				setActive( row, false );
			} );
		} );

		rows.addEventListener( 'click', function ( e ) {
			var remove = e.target.closest( '.elfzwo-hotspot-remove' );
			if ( ! remove ) {
				return;
			}
			e.preventDefault();
			remove.closest( '.elfzwo-hotspot-row' ).remove();
			render();
		} );

		watchThumbnail( wrap, img );
		render();
	}

	/**
	 * Hält das Bild im Editor aktuell, wenn das Beitragsbild in der
	 * Seitenleiste gesetzt, getauscht oder entfernt wird (ohne Neuladen).
	 */
	function watchThumbnail( wrap, img ) {
		var box = document.getElementById( 'postimagediv' );
		if ( ! box || ! window.MutationObserver ) {
			return;
		}
		var lastId = null;

		function update() {
			var input = document.getElementById( '_thumbnail_id' );
			var id    = input ? parseInt( input.value, 10 ) : 0;
			if ( id === lastId ) {
				return;
			}
			lastId = id;
			var has = id > 0;
			wrap.querySelector( '.elfzwo-hotspots-empty' ).style.display = has ? 'none' : '';
			wrap.querySelector( '.elfzwo-hotspots-help' ).style.display  = has ? '' : 'none';
			wrap.querySelector( '.elfzwo-hotspots-stage' ).style.display = has ? '' : 'none';
			if ( ! has || ! window.wp || ! wp.media || ! wp.media.attachment ) {
				return;
			}
			var att = wp.media.attachment( id );
			att.fetch().then( function () {
				var sizes = att.get( 'sizes' ) || {};
				img.src = sizes.large ? sizes.large.url : att.get( 'url' );
			} );
		}

		var input = document.getElementById( '_thumbnail_id' );
		lastId = input ? parseInt( input.value, 10 ) : 0;
		new MutationObserver( update ).observe( box, { childList: true, subtree: true, attributes: true } );
	}

	document.addEventListener( 'DOMContentLoaded', function () {
		document.querySelectorAll( '.elfzwo-hotspots' ).forEach( initHotspots );
	} );
} )();
