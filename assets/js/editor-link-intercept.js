/**
 * Der Block-Editor-Canvas läuft seit WP 5.9 in einem eigenen iframe. Unsere
 * elfzwo/*-Blöcke rendern über render.php echte <a href>-Links (Buttons, Nav,
 * CTAs) -- ohne diese Datei würde ein Klick darauf nur das iframe selbst
 * navigieren (die Zielseite erscheint verquer im winzigen Editor-Canvas),
 * während die eigentliche Backend-URL unverändert auf der ursprünglich
 * bearbeiteten Seite stehen bleibt.
 *
 * Diese Datei fängt Klicks auf interne Links im Editor-Canvas ab, löst die
 * Ziel-URL server-seitig auf einen Beitrag/eine Seite auf und schickt das
 * GESAMTE Backend-Fenster (window.top, nicht nur das iframe) zur
 * Bearbeiten-Ansicht dieser Seite.
 */
( function () {
	if ( ! window.elfzwoEditorLinkNav ) { return; }

	document.addEventListener(
		'click',
		function ( e ) {
			var a = e.target.closest && e.target.closest( 'a[href]' );
			if ( ! a ) { return; }

			// Datei-Downloads (z. B. im Termin-Block) ganz normal herunterladen.
			if ( a.hasAttribute( 'download' ) ) { return; }

			var href = a.getAttribute( 'href' );
			if ( ! href || '#' === href.charAt( 0 ) || href.indexOf( 'mailto:' ) === 0 || href.indexOf( 'tel:' ) === 0 ) {
				return;
			}

			var url;
			try {
				url = new URL( href, window.location.href );
			} catch ( err ) {
				return;
			}

			// Nur interne Links abfangen -- externe Links und wp-admin/wp-login
			// normal weiterklicken lassen (bzw. im iframe navigieren, was dort
			// unschädlich ist, da es nicht unsere gerenderten Seiten betrifft).
			if ( url.origin !== window.location.origin ) { return; }
			if ( url.pathname.indexOf( '/wp-admin/' ) !== -1 || url.pathname.indexOf( '/wp-login.php' ) !== -1 ) { return; }

			e.preventDefault();
			e.stopPropagation();

			fetch( elfzwoEditorLinkNav.restUrl + '?url=' + encodeURIComponent( url.href ), {
				headers: { 'X-WP-Nonce': elfzwoEditorLinkNav.nonce },
				credentials: 'same-origin',
			} )
				.then( function ( r ) { return r.ok ? r.json() : null; } )
				.then( function ( data ) {
					if ( data && data.editUrl ) {
						window.top.location.href = data.editUrl;
					}
				} )
				.catch( function () {} );
		},
		true
	);
} )();
