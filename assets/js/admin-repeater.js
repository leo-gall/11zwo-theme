( function () {
	function initRepeater( wrap ) {
		var rows     = wrap.querySelector( '.elfzwo-repeater-rows' );
		var addBtn   = wrap.querySelector( '.elfzwo-repeater-add' );
		var template = wrap.querySelector( '.elfzwo-repeater-template' );

		addBtn.addEventListener( 'click', function () {
			var index = Date.now();
			var html  = template.innerHTML.split( '__INDEX__' ).join( index );
			var tmp   = document.createElement( 'div' );
			tmp.innerHTML = html;
			rows.appendChild( tmp.firstElementChild );
		} );

		wrap.addEventListener( 'click', function ( e ) {
			var remove = e.target.closest( '.elfzwo-repeater-remove' );
			if ( ! remove ) {
				return;
			}
			e.preventDefault();
			remove.closest( '.elfzwo-repeater-row' ).remove();
		} );

		initPartnerCombobox( wrap );
	}

	/**
	 * Such-Combobox für das erste Repeater-Unterfeld (z. B. "Name" bei den
	 * Einsatzkräften): schlägt bereits einmal gespeicherte Einträge vor und
	 * füllt beim Auswählen auch benachbarte Felder (z. B. den Link) mit.
	 * Arbeitet per Event-Delegation, damit sie auch für per JS neu
	 * hinzugefügte Zeilen ohne erneute Initialisierung funktioniert.
	 */
	function initPartnerCombobox( wrap ) {
		var dataEl = wrap.querySelector( '.elfzwo-repeater-suggestions' );
		if ( ! dataEl ) {
			return;
		}
		var items = JSON.parse( dataEl.textContent || '[]' );

		function normalize( s ) {
			return ( s || '' ).toLowerCase();
		}

		function search( term ) {
			term = normalize( term );
			if ( ! term ) {
				return items;
			}
			return items.filter( function ( item ) {
				return normalize( item.name ).indexOf( term ) !== -1;
			} );
		}

		function renderResults( results, matches ) {
			results.innerHTML = '';
			if ( ! matches.length ) {
				results.innerHTML = '<p class="elfzwo-combobox-empty">Keine Treffer &ndash; wird als neuer Eintrag gespeichert.</p>';
				results.style.display = 'block';
				return;
			}
			matches.slice( 0, 20 ).forEach( function ( item ) {
				var option = document.createElement( 'div' );
				option.className = 'elfzwo-combobox-option';
				option.textContent = item.name;
				option.dataset.name = item.name;
				option.dataset.url = item.url || '';
				results.appendChild( option );
			} );
			results.style.display = 'block';
		}

		wrap.addEventListener(
			'focus',
			function ( e ) {
				var input = e.target.closest( '.elfzwo-partner-combobox-input' );
				if ( ! input ) {
					return;
				}
				var results = input.closest( '.elfzwo-partner-combobox' ).querySelector( '.elfzwo-combobox-results' );
				renderResults( results, search( input.value ) );
			},
			true
		);

		wrap.addEventListener( 'input', function ( e ) {
			var input = e.target.closest( '.elfzwo-partner-combobox-input' );
			if ( ! input ) {
				return;
			}
			var results = input.closest( '.elfzwo-partner-combobox' ).querySelector( '.elfzwo-combobox-results' );
			renderResults( results, search( input.value ) );
		} );

		wrap.addEventListener(
			'blur',
			function ( e ) {
				var input = e.target.closest( '.elfzwo-partner-combobox-input' );
				if ( ! input ) {
					return;
				}
				var results = input.closest( '.elfzwo-partner-combobox' ).querySelector( '.elfzwo-combobox-results' );
				setTimeout( function () {
					results.style.display = 'none';
				}, 150 );
			},
			true
		);

		wrap.addEventListener( 'mousedown', function ( e ) {
			var option = e.target.closest( '.elfzwo-combobox-option' );
			if ( ! option ) {
				return;
			}
			var comboWrap = option.closest( '.elfzwo-partner-combobox' );
			if ( ! comboWrap ) {
				return;
			}
			comboWrap.querySelector( '.elfzwo-partner-combobox-input' ).value = option.dataset.name;

			var row      = comboWrap.closest( '.elfzwo-repeater-row' );
			var urlInput = row ? row.querySelector( '[data-subfield="url"]' ) : null;
			if ( urlInput ) {
				urlInput.value = option.dataset.url;
			}
			comboWrap.querySelector( '.elfzwo-combobox-results' ).style.display = 'none';
		} );
	}

	document.addEventListener( 'DOMContentLoaded', function () {
		document.querySelectorAll( '.elfzwo-repeater' ).forEach( initRepeater );
	} );
} )();
