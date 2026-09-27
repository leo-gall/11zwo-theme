( function () {
	function Accordion( el ) {
		this.el = el;
		this.summary = el.querySelector( 'summary' );
		this.content = el.querySelector( '.elfzwo-faq-answer' );
		this.animation = null;
		this.isClosing = false;
		this.isExpanding = false;
		this.summary.addEventListener( 'click', this.onClick.bind( this ) );
	}

	Accordion.prototype.onClick = function ( e ) {
		e.preventDefault();
		this.el.style.overflow = 'hidden';
		if ( this.isClosing || ! this.el.open ) {
			this.open();
		} else if ( this.isExpanding || this.el.open ) {
			this.shrink();
		}
	};

	Accordion.prototype.shrink = function () {
		this.isClosing = true;
		var startHeight = this.el.offsetHeight + 'px';
		var endHeight = this.summary.offsetHeight + 'px';
		if ( this.animation ) {
			this.animation.cancel();
		}
		this.animation = this.el.animate( { height: [ startHeight, endHeight ] }, { duration: 250, easing: 'ease-out' } );
		this.animation.onfinish = this.onAnimationFinish.bind( this, false );
		this.animation.oncancel = function () {
			this.isClosing = false;
		}.bind( this );
	};

	Accordion.prototype.open = function () {
		this.el.style.height = this.el.offsetHeight + 'px';
		this.el.open = true;
		window.requestAnimationFrame( this.expand.bind( this ) );
	};

	Accordion.prototype.expand = function () {
		this.isExpanding = true;
		var startHeight = this.el.offsetHeight + 'px';
		var endHeight = this.summary.offsetHeight + this.content.offsetHeight + 'px';
		if ( this.animation ) {
			this.animation.cancel();
		}
		this.animation = this.el.animate( { height: [ startHeight, endHeight ] }, { duration: 250, easing: 'ease-out' } );
		this.animation.onfinish = this.onAnimationFinish.bind( this, true );
		this.animation.oncancel = function () {
			this.isExpanding = false;
		}.bind( this );
	};

	Accordion.prototype.onAnimationFinish = function ( open ) {
		this.el.open = open;
		this.animation = null;
		this.isClosing = false;
		this.isExpanding = false;
		this.el.style.height = '';
		this.el.style.overflow = '';
	};

	document.querySelectorAll( '.elfzwo-faq-item' ).forEach( function ( el ) {
		new Accordion( el );
	} );
} )();
