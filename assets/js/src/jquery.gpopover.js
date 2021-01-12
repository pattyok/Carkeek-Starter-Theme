/*
 * jquery-gpopover
 * http://github.com/markembling/jquery-gpopover
 *
 * A simple jQuery plugin for creating popover elements similar to Google's
 * new 'apps' launcher/switcher.
 *
 * Copyright (c) 2013 Mark Embling (markembling.info)
 * Licensed under the BSD (3 clause) license.
 */

( function( $ ) {
	const GPopover = function( element, options ) {
		this.options = null;
		this.$trigger = null;
		this.$popover = null;

		this.init( element, options );
	};

	GPopover.prototype.init = function( element, options ) {
		const that = this;

		this.options = $.extend( {}, $.fn.gpopover.defaults, options );

		this.$trigger = $( element );
		this.$popover = $( '#' + this.$trigger.data( 'popover' ) );

		this._addArrowElements();

		if ( this.options.preventHide ) {
			this._preventHideClickPropagation();
		}

		this.$trigger.click( function( e ) {
			if ( ! that.$popover.is( ':visible' ) ) {
				// Trigger a click on the parent element (that can bubble up)
				$( this )
					.parent()
					.click();

				that.show();

				e.stopPropagation();
			}

			e.preventDefault();
		} );
	};

	GPopover.prototype.show = function() {
		const that = this;

		// Set width before showing
		this.$popover.width( this.options.width );

		// Show the popover
		this.$popover.fadeIn( this.options.fadeInDuration );

		// Set up hiding
		$( document ).one( 'click.popoverHide', function() {
			// _hidePopover($popover, settings);
			that.hide();
		} );

		// Sort out the position (must be done after showing)
		const triggerPos = this.$trigger.offset();
		this.$popover.offset( {
			left:
				triggerPos.left +
				( this.$trigger.outerWidth() / 2 ) -
				( this.$popover.outerWidth() / 2 ),
			//top: triggerPos.top + this.$trigger.outerHeight() + 10,
			top: triggerPos.top - this.$popover.outerHeight() - 10,
			// the final 10 above allows room for the arrow below it
		} );

		// Check and reposition if out of the viewport
		const positionXCorrection = this._repositionForViewportSides();

		// Set the position of the arrow elements
		this._setArrowPosition( positionXCorrection );

		// Call the callback
		this.options.onShow.call( this.$trigger, this.$popover );
	};

	GPopover.prototype.hide = function() {
		// Hide the popover
		this.$popover.fadeOut( this.options.fadeOutDuration );

		// Call the callback
		this.options.onHide.call( this.$trigger, this.$popover );
	};

	GPopover.prototype._addArrowElements = function() {
		this.$arrow = $( '<div class="gpopover-arrow"></div>' );
		this.$arrowShadow = $( '<div class="gpopover-arrow-shadow"></div>' );

		this.$popover.append( this.$arrow );
		this.$popover.append( this.$arrowShadow );
	};

	GPopover.prototype._preventHideClickPropagation = function() {
		/* Prevent clicks within the popover from being propagated
           to the document (and thus stop the popover from being
           hidden) */
		this.$popover.click( function( e ) {
			e.stopPropagation();
		} );
	};

	GPopover.prototype._repositionForViewportSides = function() {
		let popoverOffsetLeft = this.$popover.offset().left,
			positionXCorrection = 0;

		const $window = $( window );

		// Right edge
		if (
			popoverOffsetLeft +
			this.$popover.outerWidth() +
			this.options.viewportSideMargin >
			$window.width()
		) {
			const rightEdgeCorrection = -(
				popoverOffsetLeft +
				this.$popover.outerWidth() +
				this.options.viewportSideMargin -
				$window.width()
			);
			popoverOffsetLeft = popoverOffsetLeft + rightEdgeCorrection;

			positionXCorrection = rightEdgeCorrection;
		}

		// Left edge
		if ( popoverOffsetLeft < this.options.viewportSideMargin ) {
			const leftEdgeCorrection =
				this.options.viewportSideMargin - popoverOffsetLeft;
			popoverOffsetLeft = popoverOffsetLeft + leftEdgeCorrection;

			positionXCorrection += leftEdgeCorrection;
		}

		// Reposition the popover element if necessary
		if ( positionXCorrection !== 0 ) {
			this.$popover.offset( { left: popoverOffsetLeft } );
		}

		return positionXCorrection;
	};

	GPopover.prototype._setArrowPosition = function( positionXCorrection ) {
		const leftposition =
			( this.$popover.outerWidth() / 2 ) -
			( this.$arrow.outerWidth() / 2 ) -
			positionXCorrection;

		this.$arrow.css( {
			bottom: -7,
			left: leftposition,
		} );
		this.$arrowShadow.css( {
			bottom: -8,
			left: leftposition,
		} );
	};

	$.fn.gpopover = function( option ) {
		return this.each( function() {
			const $this = $( this ),
				options = typeof option === 'object' && option;

			let data = $this.data( 'gpopover' );

			// Initialise if not already done
			if ( ! data ) {
				data = new GPopover( this, options );
				$this.data( 'gpopover', data );
			}

			// If the option parameter was a string, trigger the named function
			if ( typeof option === 'string' ) {
				data[ option ]();
			}
		} );
	};

	// Default settings
	$.fn.gpopover.defaults = {
		width: 250, // Width of the popover
		fadeInDuration: 65, // Duration of popover fade-in animation
		fadeOutDuration: 65, // Duration of popover fade-out animation
		viewportSideMargin: 10, // Space to leave the side if out the viewport
		preventHide: false, // Prevent hide when clicking within popover

		onShow() { }, // Called upon showing the popover
		onHide() { }, // Called upon hiding the popover
	};
}( jQuery ) );
