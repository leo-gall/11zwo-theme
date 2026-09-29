( function () {
	var DURATION = 250;
	var EASING   = 'ease-out';

	var reduceMotion = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	/**
	 * Animiert die Höhe eines <details>-Eintrags per Web-Animations-API.
	 * Start- und Zielhöhe werden immer echt gemessen (inkl. Padding, Rahmen und
	 * Abständen der Antwort) -- sonst endet die Animation auf der falschen Höhe
	 * und springt am Schluss. Ein Klick während einer laufenden Animation dreht
	 * sie ab der aktuellen Höhe um.
	 */
	function Accordion( el ) {
		this.el        = el;
		this.summary   = el.querySelector( 'summary' );
		this.animation = null;
		this.el.classList.toggle( 'is-open', el.open );
		this.summary.addEventListener( 'click', this.onClick.bind( this ) );
	}

	Accordion.prototype.currentHeight = function () {
		return this.el.getBoundingClientRect().height;
	};

	/** Höhe im geschlossenen Zustand: nur die Frage plus Padding und Rahmen des Eintrags. */
	Accordion.prototype.closedHeight = function () {
		var cs = window.getComputedStyle( this.el );
		return this.summary.getBoundingClientRect().height
			+ parseFloat( cs.paddingTop ) + parseFloat( cs.paddingBottom )
			+ parseFloat( cs.borderTopWidth ) + parseFloat( cs.borderBottomWidth );
	};

	/** Höhe im geöffneten Zustand, gemessen ohne laufende Animation und ohne feste Höhe. */
	Accordion.prototype.openHeight = function () {
		this.el.style.height = '';
		return this.currentHeight();
	};

	Accordion.prototype.onClick = function ( e ) {
		e.preventDefault();
		var opening = ! this.el.classList.contains( 'is-open' );
		this.el.classList.toggle( 'is-open', opening );

		var start = this.currentHeight();
		if ( this.animation ) {
			this.animation.cancel();
			this.animation = null;
		}

		// Während der Animation bleibt <details> offen, damit die Antwort beim
		// Zuklappen sichtbar mitschrumpft; geschlossen wird erst am Ende.
		this.el.open = true;
		var end = opening ? this.openHeight() : this.closedHeight();

		if ( reduceMotion || Math.abs( end - start ) < 1 ) {
			this.finish( opening );
			return;
		}

		this.el.style.overflow = 'hidden';
		this.el.style.height   = start + 'px';
		this.animation = this.el.animate(
			{ height: [ start + 'px', end + 'px' ] },
			{ duration: DURATION, easing: EASING }
		);
		this.animation.onfinish = this.finish.bind( this, opening );
	};

	Accordion.prototype.finish = function ( open ) {
		this.el.open           = open;
		this.animation         = null;
		this.el.style.height   = '';
		this.el.style.overflow = '';
	};

	document.querySelectorAll( '.elfzwo-faq-item' ).forEach( function ( el ) {
		new Accordion( el );
	} );
} )();
