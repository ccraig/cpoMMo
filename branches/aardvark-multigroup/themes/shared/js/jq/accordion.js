/*
 * Accordion 1.1 - jQuery menu widget
 *
 * Copyright (c) 2006 Jörn Zaefferer, Frank Marcia
 *
 * Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 *
 * Revision: $Id$
 *
 */
// nextUntil is necessary, would be nice to have this in jQuery core
jQuery.fn.nextUntil = function(expr) {
    var match = [];

    // We need to figure out which elements to push onto the array
    this.each(function(){
        // Traverse through the sibling nodes
        for( var i = this.nextSibling; i; i = i.nextSibling ) {
            // Make sure that we're only dealing with elements
            if ( i.nodeType != 1 ) continue;

            // If we find a match then we need to stop
            if ( jQuery.filter( expr, [i] ).r.length ) break;

            // Otherwise, add it on to the stack
            match.push( i );
        }
    });

    return this.pushStack( match, arguments );
};

// create private scope with $ alias for jQuery
(function($) {
	// save reference to plugin method
	var plugin = $.fn.Accordion = function(settings) {

		// setup configuration
		settings = $.extend({}, plugin.defaults, {
			// define context defaults
			header: $(':first-child', this)[0].tagName // take first childs tagName as header
		}, settings);

		// calculate active if not specified, using the first header
		var container = this,
			active = settings.active ? $(settings.active, this) : settings.active === false ? $("<div>") : $(settings.header, this).eq(0),
			running = 0;

		container.find(settings.header)
			.not(active && active[0] || "")
			.nextUntil(settings.header)
			.hide();
		active.addClass(settings.selectedClass);

		var clickHandler = function(event) {
			// get the click target
			var clicked = $(event.target);
			
			var clickedActive = clicked[0] == active[0];
			
			// if animations are still active, or the active header is the target, ignore click
			if(running || (settings.alwaysOpen && clickedActive) || !clicked.is(settings.header))
				return;

			// switch classes
			active.removeClass(settings.selectedClass);
			clicked.addClass(settings.selectedClass);

			// find elements to show and hide
			var toShow = clicked.nextUntil(settings.header),
				toHide = active.nextUntil(settings.header),
				data = [clicked, active, toShow, toHide];
			active = clicked;
			// count elements to animate
			running = toHide.size() + toShow.size();
			var finished = function() {
				if(--running)
					return;

				// trigger custom change event
				container.trigger("change", data);
			};
			// TODO if hideSpeed is set to zero, animations are crappy
			// workaround: use hide instead
			// solution: animate should check for speed of 0 and do something about it
			if(!settings.alwaysOpen && clickedActive) {
				toShow.slideToggle(settings.showSpeed, finished);
				finished();
			} else {
				toHide.filter(":hidden").each(finished).end().filter(":visible").slideUp(settings.hideSpeed, finished);
				toShow.slideDown(settings.showSpeed, finished);
			}

			return false;
		};
		var activateHandlder = function(event, index) {
			// call clickHandler with custom event
			clickHandler({
				target: $(settings.header, this)[index]
			});
		};

		return container
			.click(clickHandler)
			.bind("activate", activateHandlder);
	};
	// define static defaults
	plugin.defaults = {
		selectedClass: "selected",
		showSpeed: 'slow',
		hideSpeed: 'fast',
		alwaysOpen: true
	};

	// shortcut for trigger, nicer API and easily to document
	$.fn.activate = function(index) {
		return this.trigger('activate', [index || 0]);
	};

})(jQuery);