<script type="text/javascript" src="{$url.theme.shared}js/jq/jqModal.js"></script>
<link type="text/css" rel="stylesheet" href="{$url.theme.shared}css/ui.dialog.css" />

{literal}
<script type="text/javascript">

PommoDialog = {
	init: function(dialogs,params,overloadParams) {
		dialogs = dialogs || 'div.jqmDialog[id!=wait]';
		params = params || {};
		if(!overloadParams)
			params = $.extend(this.params,params);
		
		$(dialogs).jqm(this.params);
	},
	params: {
		modal: false,
		ajax: '@href',
		target: '.jqmdMSG',
		trigger: false,
		onLoad: function(hash){
			// Automatically prepare forms in ajax loaded content
			if(poMMo.form && $.isFunction(poMMo.form.assign))
				poMMo.form.assign(hash.w);
		}
	}
};

$().ready(function() {
	// Close Button Highlighting. IE doesn't support :hover. Surprise?
	$('input.jqmdX')
	.hover(
		function(){ $(this).addClass('jqmdXFocus'); }, 
		function(){ $(this).removeClass('jqmdXFocus'); })
	.focus( 
		function(){ this.hideFocus=true; $(this).addClass('jqmdXFocus'); })
	.blur( 
		function(){ $(this).removeClass('jqmdXFocus'); });
		
	// Work around for IE's lack of :focus CSS selector
	if($.browser.msie)
		$('div.jqmDialog :input:visible')
			.focus(function(){$(this).addClass('iefocus');})
			.blur(function(){$(this).removeClass('iefocus');});

	// Initialize default wait dialog
	$('#wait').jqm({modal: true});

});
</script>
{/literal}

{capture name=footer}
<div class="imgCache">
	<img src="{$url.theme.shared}images/loader.gif" />
	<img src="{$url.theme.shared}images/dialog/close.gif" />
	<img src="{$url.theme.shared}images/dialog/close_hover.gif" />
	<img src="{$url.theme.shared}images/dialog/sprite.gif" />
	<img src="{$url.theme.shared}images/dialog/bl.gif" />
	<img src="{$url.theme.shared}images/dialog/br.gif" />
	<img src="{$url.theme.shared}images/dialog/bc.gif" />
</div>
{include file="inc/dialog.tpl" id=wait wait=true}
{/capture}