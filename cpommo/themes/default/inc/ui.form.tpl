<script type="text/javascript" src="{$url.theme.shared}js/jq/form.js"></script>
<script type="text/javascript" src="{$url.theme.shared}js/jq/jqValidate.js"></script>

{literal}
<script type="text/javascript">
/**
  * Form Javascript Copyright 2008 by Brice Burgess <bhb@iceburg.net>, all rights reserved.
  */
poMMo.form = {
	currentForm: false, // (int) serial of the submitted form
	confirmForm: false,
	serial: 0,
	hash: [],
	init: function(e,p) {
		e = $(e);
		if(e.size() < 1) { alert('bad form passed to init'); return; }
	
		p = $.extend({
			type: 'ajax',  		// type can be 'ajax' or 'json'. Ajax type forms load their response into the DOM ("target"). JSON type forms evaluate/parse the response.
			onValid: null,		// (for JSON) executed if the form is determined 'valid' [success=false]
			onInvalid: null, 	// (for JSON) executed if the form is determined 'invalid' [success=false]
			target: null,
			beforeSubmit: poMMo.form.defaults.beforeSubmit,
			success: this.defaults.success
		},p);
		
		return e.each(function(){
		
			var s = (this.pfSerial) ? this.pfSerial : poMMo.form.serial++;
			this.pfSerial = s;
			
			p.form = this;
			p.scope = p.target || $(this).parent()[0];
			
			poMMo.form.hash[s] = p;
			
			$(this).ajaxForm({
				dataType: (p.type == 'ajax') ? null : 'json',
				target: (p.type == 'ajax' && p.target == null) ? $(this).parent() : p.target, // load the form response into the parent div if not specified
				beforeSubmit: function(formData,form,params) {
					var s = $(form)[0].pfSerial;
					var hash = poMMo.form.hash[s];
					if(poMMo.form.currentForm !== false) { 
						alert ('cannot submit form at this time [waiting for the return of another]');
						return false;
					}
					poMMo.form.currentForm = s;
					if($.isFunction(hash.beforeSubmit))
						return hash.beforeSubmit(formData,form,params);
				},
				success: function(response) {
					var s = poMMo.form.currentForm;
					poMMo.form.currentForm = false;
					var hash = poMMo.form.hash[s];
					if($.isFunction(hash.success))
						return hash.success(response, hash);
				}
			});
		});
	},
	defaults: {
		// Default beforeSubmit callback [if not overriden]
		beforeSubmit: function(formData,form,params) {	
			// reset errors
			$('label span.error',form).remove();
			$('div.output',form).html('');
			
			// toggle submit/loading state
			$('input[@type=submit],img[@name=loading]', form).toggle();
		},
		// Default success callback [if not overriden]
		success: function(response, hash) { 
			
			if($(hash.form).is('.confirm')) {
				var confirmed = $('input[@name=confirmed]',hash.form)[0];
				if (confirmed) {
					$(confirmed).remove();
					poMMo.resume();
				}
			}
			
			// if we're expecting a JSON return, execute the default JSON callback
			if(hash.type == 'json')
				return poMMo.form.defaults.jsonSuccess(response, hash);
			
			// reassign the form [designed to work in default setting, on forms with class ajax]
			poMMo.form.assign(hash.scope, hash.form.pfSerial);
		},
		jsonSuccess: function(json, hash) {
			// execute a callback function if passed (and exists).
			//   If the callbackParams exist, pass them to the callbackFunction. If not, pass the JSON return
			//   If the callback returns false, halt execution.
			if(json.callbackFunction && $.isFunction(poMMo.callback[json.callbackFunction])) {
				json.callbackParams = json.callbackParams || json;
				if(poMMo.callback[json.callbackFunction](json.callbackParams, hash.form) === false)
					return false;
			}
			
			// toggle submit/loading state
			$('input[@type=submit],img[@name=loading]', hash.form).toggle();
			
			// check for and execute onValid/onInvalid callbacks
			if(json.success && $.isFunction(hash.onValid))
				return hash.onValid(json,hash);
			else if(!json.success && $.isFunction(hash.onInvalid))
				return hash.onInvalid(json,hash);
		 	
		 	// output any message(s) or errors(s)
		 	if(json.messages.length > 0)
				$('div.output',hash.form).html(poMMo.implode(json.messages));
			if(json.errors.length > 0)
				$('div.output',hash.form).append('<div class="error">'+poMMo.implode(json.errors)+'</div>');
		
			// append error messages to form fields
			if(json.fieldErrors)
			for (var i=0;i<json.fieldErrors.length;i++) 
					$('label[@for='+json.fieldErrors[i].field+']',hash.form).append('<span class="error">'+json.fieldErrors[i].message+'</span>');
		}
	},
	assign: function(scope, reassign) { // prepares forms found in scope. Usually called on ajax loaded content.
		scope = scope || $('body')[0];
		$('form',scope).each(function(){
			if(reassign) { // conserve memory! reassign is passed as previous form's serial #
				this.pfSerial = reassign;
				poMMo.form.init(this,poMMo.form.hash[reassign]);
			}
			else if($(this).is('.ajax'))
				poMMo.form.init(this);
			else if($(this).is('.json'))
				poMMo.form.init(this,{type: 'json'});
		}).filter('.validate').jqValidate({submitElements: ':submit:not(.jqmClose,.pvSkip)'});
	}
};
</script>
{/literal}