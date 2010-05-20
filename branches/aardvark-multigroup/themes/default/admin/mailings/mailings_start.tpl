{capture name=head}{* used to inject content into the HTML <head> *}
<script type="text/javascript" src="{$url.theme.shared}js/tinymce/tiny_mce.js"></script>
<script type="text/javascript" src="{$url.theme.shared}js/jq/jq11.js"></script>
<script type="text/javascript" src="{$url.theme.shared}js/jq/history.js" ></script>
<script type="text/javascript" src="{$url.theme.shared}js/jq/tabs.js"></script>
<script type="text/javascript" src="{$url.theme.shared}js/jq/form.js"></script>
<script type="text/javascript" src="{$url.theme.shared}js/jq/jqModal.js"></script>

<link type="text/css" rel="stylesheet" href="{$url.theme.shared}css/modal.css" />
<link type="text/css" rel="stylesheet" href="{$url.theme.shared}css/default.mailings.css" />

<link type="text/css" rel="stylesheet" href="{$url.theme.shared}css/tabs.css" />
<!--[if lte IE 7]>
{literal}
<style type="text/css" media="projection, screen">
.anchors { /* auto clear */
    display: inline-block; /* @ IE 7 */
    _height: 1%; /* @ IE 6 */
}
.anchors a {
    float: left;
}
.anchors .tabs-disabled a {
    filter: alpha(opacity=40);
    zoom: 1; /* trigger filter */
}
{/literal}
</style>
<![endif]-->
{/capture}
{include file="inc/admin.header.tpl" sidebar='off'}

<ul class="inpage_menu">
<li><a href="admin_mailings.php" title="{t}Return to Subscribers Page{/t}">{t}Return to Mailings Page{/t}</a></li>
</ul>

{include file="inc/messages.tpl"}

<hr />

<div id="mailing">
	<ul class="anchors">
	    <li><a href="mailing/setup.php">{t}Setup{/t}</a></li>
	    <li><a href="mailing/templates.php">{t}Templates{/t}</a></li>
	    <li><a href="mailing/compose.php">{t}Compose{/t}</a></li>
	    <li><a href="mailing/preview.php">{t}Preview{/t}</a></li>
	</ul>
</div>

{capture name=personalize}
<div class="helpToggle">
<img src="{$url.theme.shared}images/icons/help.png" alt="help icon" style="float: left; margin: 0 10px; 0 5px;" />
<p>
{t escape=no 1="<a href=\"`$url.base`admin/setup/setup_fields.php\">" 2="</a>"}Any %1subscriber field%2 may be used to personalize the message. If the subscriber is missing a value for a personalization, a default substitution may be supplied. Values are injected into mailings to personalize them per individual.{/t}
<br /><br />
{t escape=no 1='<tt>' 2='</tt>'}For instance, you may begin a mailing with "Dear %1[[first_name|Subscriber]]%2, Happy New Year!". In this example 'first_name' is the name of a subscriber field, and 'Subscriber' is the default substitution.{/t}
</p>
</div>

<div class="alert">
<strong>{t}Syntax{/t}</strong> --&gt; <br />
{t}[[Field_Name]] or [[Field_Name|Default]]{/t}
</div>

<hr />

<p>
<label for="field">{t}Personalization{/t}:</label>
<select name="field">
<option value="">{t}choose field{/t}</option>
<option value="email">{t}Email{/t}</option>
<option value="ip">{t}IP Address{/t}</option>
<option value="registered">{t}Registered{/t}</option>
<option value="">-----------</option>
{foreach from=$fields key=id item=field}
<option value="{$field.name}">{$field.name}</option>
{/foreach}
</select>
</p>

<p>
<label for="default">{t}Default{/t}:</label>
<input type="text" name="default" />
</p>

<div class="buttons">
<input type="submit" class="tinyInject" value="{t}Insert{/t}" />
<input type="submit" class="jqmClose" value="{t}Cancel{/t}" />
</div>		
{/capture}

{capture name=specialLink}
<div class="alert">
<p>
<strong>{t}Unsubscribe{/t}</strong>: {t}Personalized URL of subscriber update/unsubscribe page.{/t}
</p>

<p>
<strong>{t}Web Link{/t}</strong>: {t escape=no  1="<a href='`$url.base`admin/setup/setup_configure.php#mailings'>" 2='</a>'}URL to view this mailing on your website (note: %1public mailings%2 must be enabled){/t}
</p>

<p>
<strong>{t}Subscriber ID{/t}</strong>: {t}Outputs the subscriber's unique ID.{/t} {t}This is useful for tracking "open rates" and such.{/t}
</p>

<p>
<strong>{t}Mailing ID{/t}</strong>: {t}Outputs the mailing ID.{/t} {t}This is useful for tracking "open rates" and such.{/t}
</p>

</div>

<hr />

<p>
<label for="field">{t}Special Link{/t}:</label>
<select name="field">
<option value="!unsubscribe">{t}Unsubscribe{/t}</option>
<option value="!weblink">{t}Web Link{/t}</option>
<option value="!subscriber_id">{t}Subscriber ID{/t}</option>
<option value="!mailing_id">{t}Mailing ID{/t}</option>
</select>
</p>

<div class="buttons">
<input type="submit" class="tinyInject" value="{t}Insert{/t}" />
<input type="submit" class="jqmClose" value="{t}Cancel{/t}" />
</div>	
{/capture}

{literal}
<script type="text/javascript">
/* TabWizzard JS (c) 2007 Brice Burgess, <bhb@iceburg.net>
	Licensed under the GPL */
	
var pommo = {
	clickedTab: false,
	isTiny: false,
	isForm: false,
	ajaxQueue: [], // for synchronous ajax
	
	// prepares forms of class ajax to be submitted via ajax
	//	returns jQuery object of affected elements
	
	assignForm: function(scope, callback) {
		
		return this.isForm = $('form.ajax',scope).ajaxForm( { 
			target: scope,
			beforeSubmit: function() {
				$('input[@type=submit]', scope).hide();
				$('img[@name=loading]', scope).show();
			},

			success: function() {

				pommo.assignForm(this);
				$('div.output',this).fadeTo(5000,0.35);
				
				if($.isFunction(callback)) {
					return callback();
				}
				
				if($('#success',scope).length > 0) { // form passed server side validation
					pommo.switchTab();
				}
			
			}
		});
	},
	
	// prepares a textarea of class wysiwyg as a wysiwyg editor
	//	returns jQuery object of affected elements
	
	makeTiny: function(scope) {
		return this.isTiny = $('textarea.wysiwyg',scope).each(function(){
			var id = this.attributes.getNamedItem("name").value;
			tinyMCE.execCommand('mceAddControl', false, id);
		});
	},
	
	// removes wysiwyg functionality on textareas
	
	brakeTiny: function(scope) {
		$('textarea.wysiwyg',scope).each(function(){
			var id = this.attributes.getNamedItem("name").value;
			tinyMCE.execCommand('mceFocus', false, id);                    
			tinyMCE.execCommand('mceRemoveControl', false, id);
		});
		return this.isTiny = $('.__NO_CLASS');
	},
	
	// Special submit function for COMPOSE tab
	
	bodySubmit: function(scope, callback) {
			
		var bodies = {
			body: (this.isTiny.length > 0) ? 
				tinyMCE.getContent() : $('textarea[@name=body]',scope).val(),
			altbody:
				$('textarea[@name=altbody]',scope).val()
		};

		$('#wait').jqmShow();
				
		$.ajax({
			type: "POST",
			url: "mailing/ajax.savebody.php",
			data: bodies,
			dataType: 'json',
			success: function(json){
				if(!json.success)
					return;
				
				// if callback is passed as a function, execute on success vs. switching tab.
				if($.isFunction(callback))
					callback();
				else {
					pommo.brakeTiny($('form'));
					pommo.switchTab();
				}
				$('#wait').jqmHide();
			}
		});
	},
	
	// triggers the clicked tab, or proceed to "next" tab (extraced from #success value)
	//	optionally, the tabIndex can be passed
		
	switchTab: function(tabIndex) {
		if (typeof tabIndex != 'undefined') {
			$('#mailing').triggerTab(tabIndex);
			return;
		}
		
		if(this.clickedTab) $(this.clickedTab).trigger('triggerTab'); // load clicked tab
		else $('#mailing').triggerTab($('#success').val()); // load "next" tab
	},
	
	// sends ajax requests in synchronous order
	syncAjax: function() {
		var url = this.ajaxQueue.pop();
		if (url)
			$.ajax({
				url: url,
				success: function() {
					pommo.syncAjax();
				}
			});
		return;
	},
	
	sendAjax: function(url) {
		this.ajaxQueue.push(url);
		this.syncAjax();
		return;
	}
	
}

$().ready(function(){ 
	
	$('#mailing').tabs({
		remote: true,
		onClick: function(tab, loading, current){
				
			pommo.clickedTab = tab;
			
			// if a form is present, prevent clicked tab from activiating.
			//	tab will activate if form is valid, via the ajaxForm onSuccess function
			
			if(pommo.isForm.length > 0) {
				pommo.isForm.submit();
				return false; 
			}
			
			if($('textarea[@name=body]',current).length > 0) {
				pommo.bodySubmit(current);
				return false;
			}
			
			return true;
			}, 
		onShow: function(tab, loading, current){
			
			// assign wysiwyg and ajax form functionality
			
			pommo.makeTiny(loading);
			pommo.assignForm(loading);
			pommo.clickedTab = false;
			}	
		});
		
	// initialize wait dialog
	
	$('#wait').jqm({
		trigger: false, 
		modal: true,
		overlay: 0
	});
	
	// initialize template dialog
	
	$('#addTemplate').jqm({
		trigger: false,
		ajax: 'mailing/ajax.addtemplate.php',
		target: 'div.jqmdMSG',
		onHide: function(h) {
			// reset the dialog html to loading state
			$('div.jqmdMSG',h.w).html('{/literal}<img src="{$url.theme.shared}images/loader.gif" alt="Loading Icon" title="Please Wait" border="0" />{t}Please Wait{/t}...{literal}');	
			
			h.o.remove();
			h.w.fadeOut(800);
			
			// set pommo.isForm to false (empty array) to allow tab switching (it was set via assignForm() )
			pommo.isForm = [];
		},
		onLoad: function(h) {
			var scope = $('div.jqmdMSG',h.w);
			pommo.assignForm(scope,function() {
				$('#addTemplate').jqmAddClose('.jqmClose',scope);
			});
		}
	}).jqDrag('div.jqmdTC');
	
	// initialize test mailing dialog
	
	$('#testMailing').jqm({
		trigger: false,
		ajax: 'ajax/mailing_test.php',
		target: 'div.jqmdMSG',
		onHide: function(h) {
			// reset the dialog html to loading state
			$('div.jqmdMSG',h.w).html('{/literal}<img src="{$url.theme.shared}images/loader.gif" alt="Loading Icon" title="Please Wait" border="0" />{t}Please Wait{/t}...{literal}');	
			
			h.o.remove();
			h.w.fadeOut(800);
			
			// set pommo.isForm to false (empty array) to allow tab switching (it was set via assignForm() )
			pommo.isForm = [];
		},
		onLoad: function(h) {
			var scope = $('div.jqmdMSG',h.w);
			pommo.assignForm(scope,function() {
				$('#testMailing').jqmAddClose('.jqmClose',scope);
			});
		}
	}).jqDrag('div.jqmdTC');
	
	// initialize personalization, special link dialogs 
	var dialogs = $('#personalize,#specialLink').jqm({trigger: false}).jqDrag('div.jqmdTC');
	
	// initialize help buttons
	
	$('div.helpToggle img:first').click(function() {
		$(this).siblings('p').toggle();
		return false;
	});
	
	// initialize personalization ++ special link injection buttons
	$('input.tinyInject',dialogs).click(function() {
		
		var 
			parent=$(this).parents('div.jqmDialog:first'),
			v=$('select',parent);
			d=$('input[@name=default]'),
			out='',
			_value=v.val(),
			_default=(d[0] && d.val() != '') ? '|'+d.val() : '';
		
		if (_value == '') {
			alert ('{/literal}{t}You must choose a field{/t}{literal}');
			return false;
		}
		
		out='[['+_value+_default+']]';
		
		// reset select, default
		var o = v[0].options; o[0].selected = true;
		d.val('');
		
		if (pommo.isTiny.length > 0) {
			$(tinyMCE.getInstanceById('body').getBody()).append(out);
			tinyMCE.updateContent(out); }
		else
			$('textarea[@name=body]')[0].value += out;
		
		parent.jqmHide();
		return false;
	});
});

// initialize wysiwyg namespace

var lang="{/literal}{$lang}{literal}";
var s=',separator,';

// poMMo languages not supported by TinyMCE:
switch (lang) {
	case 'bg':
	case 'en-uk':
		lang='en';
		break;
}

tinyMCE.init({
	mode : 'none',
	theme : 'advanced',
	plugins : 'style',
	language: lang,
	entity_encoding: 'raw',
	theme_advanced_buttons1 : 
		'bold,italic,underline,strikethrough'+s+
		'bullist,numlist'+s+
		'link,unlike,image'+s+
		'hr,sub,sup,charmap'+s+
		'forecolor,backcolor,styleprops'+s+
		'undo,redo'
		,
	theme_advanced_buttons2 : 
		'justifyleft,justifycenter,justifyright,justifyfull'+s+
		'outdent,indent'+s+
		'formatselect,fontselect,fontsizeselect'+s+
		'removeformat'
		,
	theme_advanced_buttons3 : "",
	extended_valid_elements : "style[dir<ltr?rtl|lang|media|title|type]", // can add more entities, see tinymce page!
	remove_linebreaks : false
});

</script>
{/literal}

{capture name=dialogs}
{include file="inc/dialog.tpl" dialogID="wait" dialogNoClose=true dialogBodyClass="jqmdShort"}
{include file="inc/dialog.tpl" dialogID="personalize" dialogContent=$smarty.capture.personalize dialogDrag=true dialogClass="jqmdWide" dialogBodyClass="jqmdTall"}
{include file="inc/dialog.tpl" dialogID="specialLink" dialogContent=$smarty.capture.specialLink dialogDrag=true dialogClass="jqmdWide" dialogBodyClass="jqmdTall"}
{include file="inc/dialog.tpl" dialogID="addTemplate" dialogTitle=$t_saveTemplate dialogDrag=true dialogClass="jqmdWide" dialogBodyClass="jqmdTall"}
{include file="inc/dialog.tpl" dialogID="testMailing" dialogTitle=$t_testMailing dialogDrag=true dialogClass="jqmdWide" dialogBodyClass="jqmdTall"}
{/capture}

{include file="inc/admin.footer.tpl"}