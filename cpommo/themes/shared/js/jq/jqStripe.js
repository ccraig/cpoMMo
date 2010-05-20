/*
 * jqStripe - Simple alternating stripes with jQuery
 *
 * Copyright (c) 2008 Brice Burgess <bhb@iceburg.net>, http://www.iceburg.net
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 * 
 * $Version: ??/??/???? +r1 beta
 * 
 */
(function($) {
$.fn.jqStripe=function(params){
	var _params = {
		stripeClasses: ['r1', 'r2', 'r3'],
		rowSelector: 'tr:visible'
	};
	params = $.extend(_params,params);
	var stripes = params.stripeClasses.length;
	var classes = params.stripeClasses.join(' ');
	return this.each(function(){
		$(params.rowSelector,this).each(function(i){
			$(this).removeClass(classes);
			$(this).addClass(params.stripeClasses[i % stripes]);
		});
	});
};
})(jQuery);