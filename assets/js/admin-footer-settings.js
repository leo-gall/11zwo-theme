( function () {
	/**
	 * Speichert die Footer-Boxen unter "Design → Menüs" per AJAX: Der
	 * Menü-Editor verhindert das normale Absenden der linken Spalte.
	 */
	function save( button ) {
		var box     = button.closest( '.elfzwo-footer-box' );
		var spinner = box.querySelector( '.spinner' );
		var status  = box.querySelector( '.elfzwo-footer-status' );
		var data    = new FormData();

		data.append( 'action', 'elfzwo_save_footer' );
		data.append( 'nonce', window.elfzwoFooterSettings.nonce );
		data.append( 'box', button.dataset.box );
		box.querySelectorAll( 'input[name], textarea[name]' ).forEach( function ( field ) {
			if ( ! field.closest( 'template' ) ) {
				data.append( field.name, field.value );
			}
		} );

		button.disabled = true;
		spinner.classList.add( 'is-active' );
		status.textContent = '';

		fetch( window.elfzwoFooterSettings.ajaxUrl, { method: 'POST', body: data, credentials: 'same-origin' } )
			.then( function ( res ) {
				return res.json();
			} )
			.then( function ( json ) {
				status.textContent = json && json.success ? 'Gespeichert' : 'Fehler beim Speichern';
			} )
			.catch( function () {
				status.textContent = 'Fehler beim Speichern';
			} )
			.finally( function () {
				button.disabled = false;
				spinner.classList.remove( 'is-active' );
			} );
	}

	document.addEventListener( 'click', function ( e ) {
		var button = e.target.closest( '.elfzwo-footer-save' );
		if ( button ) {
			e.preventDefault();
			save( button );
		}
	} );
} )();
