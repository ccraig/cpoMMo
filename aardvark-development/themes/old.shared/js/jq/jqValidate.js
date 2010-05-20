/*
 * jqValidate - Simple client-side form validation with jQuery
 *
 * Copyright (c) 2006,2008 Brice Burgess <bhb@iceburg.net>, http://www.iceburg.net
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 * 
 * $Version: ??/??/???? +r1 beta
 * 
 */

(function($) {
$.fn.jqValidate=function(params){
var _params = {
debug: false,
submitElements: ':submit',	// form submital elements to be enabled/disabled based on validation state
validateElements: ':input',	// elements to validate
validateClasses: [			// validation elements possessing these classes will be checked against the corresponding validation rule [ see the $.jqv.rules -- note the "pv" is stripped ]
	'pvNumber',
	'pvDate',
	'pvEmpty',
	'pvEmail'
],
invalidClass: 'pvInvalid'	// assigned to invalid elements
};
return this.each(function(){
	// check if form has already been initialized
	if (this._jqV) 
		return $.jqv.init(this);

	// initialize this form
	s++; this._jqV=s;
	H[s] = {scope: this, valid: true, active: true, params: $.extend(_params,params)};
	$.jqv.init(this);
}).addClass('_jqValidate');};

// enables client side validation. Called on a form or an element within a form that has been initialized with $.jqValidate()
$.fn.jqvEnable=function(){return this.each(function(){
	var s = this._jqV || $.jqv.getSerial(this);
	H[s].active = true;
	$.jqv.validate(H[s]);
})};

// disables client side validation. Called on a form or an element within a form that has been initialized with $.jqValidate()
$.fn.jqvDisable=function(){return this.each(function(){
	var s = this._jqV || $.jqv.getSerial(this);
	H[s].active = false;
	// re-enable submit buttons (if disabled) and remove invalidClass from form elements
	H[s].inputs.removeClass(H[s].params.invalidClass);
	$.jqv.setState(true,H[s]); 
})};

// globals
$.jqv = {
hash: {},
init: function(e) {
	var h = H[e._jqV];
	// assign submit elements
	h.submits = $(h.params.submitElements,h.scope);
	if(h.submits.size() == 0 && h.debug)
		alert('jqValidate: No Submit Elements found in Form');
		
	// assign validation elements
	h.inputs = $(h.params.validateElements,h.scope);
	if(h.inputs.size() == 0 && h.debug)
		alert('jqValidate: No Validation Elements found in Form');
	
	// assign validation event to inputs
	h.inputs.mouseup(function() { $.jqv.validate(h); });
	h.inputs.keyup(function() { $.jqv.validate(h); });
	
	// validate the form
	$.jqv.validate(h);
},
rules: function(value,rule) {
	// strip the "pv" from the rule
	rule = rule.toLowerCase().substr(2);
	
	switch(rule) {
		case 'number' :
			var regex = /^\d+$/;
			return (regex.test(value));
			break;
		case 'date' :
			var regex = /^\d\d(\d\d)?[\/-]\d\d[\/-]\d\d(\d\d)?$/;
			return (regex.test(value));
			break;
		case 'email' :
			var r1 = /@.*@|\.\.|\,|\;/;
			var r2 = /^.+\@(\[?)[a-zA-Z0-9\.\-]+\.([a-zA-Z]{2,6}|[0-9]{1,3})(\]?)$/
			return !(r1.test(value) || !r2.test(value));
			break;
		case 'empty' :
			return !(value == '')
			break;
		default:
			alert('jqValidate: Unknown rule encountered! ('+rule+')');
	}
},
validate: function(h) {
	if(!h.active) // skip validation if inactive
		return;
		
	$(h.inputs).each(function(){
		var r = new Array(), c = h.params.validateClasses;
		for(var i=0;i<c.length;i++)
			if($(this).is('.'+c[i]))
				r.push(c[i]);
				
		var valid = true, val = $.trim($(this).val());
		
		for (var i = 0; i < r.length; i++) {
			if (val == '' && r[i] != 'pvEmpty') 
				continue;
			if (!$.jqv.rules(val, r[i])) 
				valid = false;
		}
			
		(valid) ?
			$(this).removeClass(h.params.invalidClass) :
			$(this).addClass(h.params.invalidClass);
	});
		
	if(h.inputs.is('.'+h.params.invalidClass)) {
		// FORM IS NOT VALID
		if(!h.valid)
			return;
		$.jqv.setState(false,h);
	}
	else {
		// FORM IS VALID
		if(h.valid)
			return;
		$.jqv.setState(true,h);
	}
},
setState: function(valid, h) {
	h.valid = valid;
	h.submits.attr('disabled',!valid).css('opacity',(valid) ? 1 : 0.5);
},
getSerial: function(e) { return $(e).parents('._jqValidate')[0]._jqV; }
};

// shortcuts
var s=0,H=$.jqv.hash;

})(jQuery);