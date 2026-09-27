( function () {
	function initCarousel( root ) {
		var track = root.querySelector( '.elfzwo-carousel-track' );
		var slides = Array.prototype.slice.call( root.querySelectorAll( '.elfzwo-carousel-slide' ) );
		var prevBtn = root.querySelector( '.elfzwo-carousel-prev' );
		var nextBtn = root.querySelector( '.elfzwo-carousel-next' );
		var dots = Array.prototype.slice.call( root.querySelectorAll( '.elfzwo-carousel-dot' ) );
		if ( ! track || slides.length < 2 ) {
			return;
		}

		var index = 0;
		var width = root.clientWidth;
		var isDragging = false;
		var startX = 0;
		var pointerId = null;

		function setActiveDot() {
			dots.forEach( function ( dot, i ) {
				dot.classList.toggle( 'elfzwo-carousel-dot-active', i === index );
			} );
		}

		function goTo( i, animate ) {
			var count = slides.length;
			index = ( ( i % count ) + count ) % count; // umlaufend: nach dem letzten Bild kommt wieder das erste
			width = root.clientWidth;
			track.style.transition = false === animate ? 'none' : 'transform 0.35s ease';
			track.style.transform = 'translateX(-' + ( index * width ) + 'px)';
			setActiveDot();
		}

		window.addEventListener( 'resize', function () {
			goTo( index, false );
		} );

		if ( prevBtn ) {
			prevBtn.addEventListener( 'click', function () {
				goTo( index - 1 );
			} );
		}
		if ( nextBtn ) {
			nextBtn.addEventListener( 'click', function () {
				goTo( index + 1 );
			} );
		}
		dots.forEach( function ( dot, i ) {
			dot.addEventListener( 'click', function () {
				goTo( i );
			} );
		} );

		track.addEventListener( 'pointerdown', function ( e ) {
			isDragging = true;
			pointerId = e.pointerId;
			startX = e.clientX;
			width = root.clientWidth;
			track.style.transition = 'none';
			track.classList.add( 'elfzwo-carousel-dragging' );
			try {
				track.setPointerCapture( pointerId );
			} catch ( err ) {
				// Manche Browser (u. a. ältere Safari-Versionen) werfen hier gelegentlich —
				// das Ziehen funktioniert trotzdem, nur ohne garantierte Pointer-Capture.
			}
		} );

		track.addEventListener( 'pointermove', function ( e ) {
			if ( ! isDragging ) {
				return;
			}
			var dx = e.clientX - startX;
			track.style.transform = 'translateX(' + ( -( index * width ) + dx ) + 'px)';
		} );

		function endDrag( e ) {
			if ( ! isDragging ) {
				return;
			}
			isDragging = false;
			track.classList.remove( 'elfzwo-carousel-dragging' );
			var dx = e.clientX - startX;
			if ( Math.abs( dx ) > width * 0.18 ) {
				goTo( index + ( dx < 0 ? 1 : -1 ) );
			} else {
				goTo( index, true );
			}
		}
		track.addEventListener( 'pointerup', endDrag );
		track.addEventListener( 'pointercancel', endDrag );

		root.addEventListener( 'keydown', function ( e ) {
			if ( 'ArrowLeft' === e.key ) {
				goTo( index - 1 );
			} else if ( 'ArrowRight' === e.key ) {
				goTo( index + 1 );
			}
		} );

		goTo( 0, false );
	}

	document.querySelectorAll( '.elfzwo-carousel' ).forEach( initCarousel );
} )();
