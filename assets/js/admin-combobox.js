( function () {
	function normalize( s ) {
		return ( s || '' ).toLowerCase();
	}

	function renderResults( wrap, items, limit ) {
		var results = wrap.querySelector( '.elfzwo-combobox-results' );
		results.innerHTML = '';

		if ( ! items.length ) {
			results.innerHTML = '<p class="elfzwo-combobox-empty">Keine Treffer.</p>';
			results.style.display = 'block';
			return;
		}

		var shown = items.slice( 0, limit );
		var lastGroup = null;

		shown.forEach( function ( item ) {
			if ( item.group !== lastGroup ) {
				var heading = document.createElement( 'div' );
				heading.className = 'elfzwo-combobox-group';
				heading.textContent = item.group;
				results.appendChild( heading );
				lastGroup = item.group;
			}
			var row = document.createElement( 'div' );
			row.className = 'elfzwo-combobox-option';
			row.textContent = item.name;
			row.dataset.id = item.id;
			row.dataset.name = item.name;
			results.appendChild( row );
		} );

		if ( items.length > limit ) {
			var more = document.createElement( 'p' );
			more.className = 'elfzwo-combobox-empty';
			more.textContent = ( items.length - limit ) + ' weitere Treffer — weiter tippen zum Eingrenzen …';
			results.appendChild( more );
		}

		results.style.display = 'block';
	}

	function initCombobox( wrap ) {
		var dataEl = wrap.querySelector( '.elfzwo-combobox-data' );
		var items = JSON.parse( dataEl.textContent || '[]' );
		var hidden = wrap.querySelector( 'input[type="hidden"]' );
		var input = wrap.querySelector( '.elfzwo-combobox-input' );
		var results = wrap.querySelector( '.elfzwo-combobox-results' );

		function search( term ) {
			term = normalize( term );
			if ( ! term ) {
				return items;
			}
			return items.filter( function ( item ) {
				return normalize( item.group + ' ' + item.name ).indexOf( term ) !== -1;
			} );
		}

		input.addEventListener( 'focus', function () {
			renderResults( wrap, search( input.value ), 80 );
		} );

		input.addEventListener( 'input', function () {
			hidden.value = '';
			renderResults( wrap, search( input.value ), 80 );
		} );

		input.addEventListener( 'blur', function () {
			// delay so a click on a result registers before we hide the list
			setTimeout( function () {
				results.style.display = 'none';
			}, 150 );
		} );

		results.addEventListener( 'mousedown', function ( e ) {
			var option = e.target.closest( '.elfzwo-combobox-option' );
			if ( ! option ) {
				return;
			}
			hidden.value = option.dataset.id;
			input.value = option.dataset.name;
			results.style.display = 'none';
		} );
	}

	document.addEventListener( 'DOMContentLoaded', function () {
		document.querySelectorAll( '.elfzwo-combobox' ).forEach( initCombobox );
	} );
} )();
