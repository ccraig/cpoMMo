{* Dialog Include -- 
	invoke via {include file="inc/dialog.tpl" param="value" ... }
	
	Valid parameters
	-------
	dialogID  ("dialog" by default)
	dialogClass (can pass multiple classes, e.g. {include file="inc/dialog.tpl" dialogClass="classA classB" ... }
	dialogBodyClass
	dialogMsgClass
	dialogContent
*}

<div id="{if $dialogID}{$dialogID}{else}dialog{/if}" class="jqmDialog{if $dialogClass} {$dialogClass}{/if}">
<div class="jqmdTL"><div class="jqmdTR"><div class="jqmdTC {if $dialogDrag}jqDrag{/if}">
{if $dialogTitle}{$dialogTitle}{else}poMMo{/if}
</div></div></div>
<div class="jqmdBL"><div class="jqmdBR"><div class="jqmdBC{if $dialogBodyClass} {$dialogBodyClass}{/if}">

<div class="jqmdMSG{if $dialogMsgClass} {$dialogMsgClass}{/if}">
{if $dialogContent}{$dialogContent}{else}<img src="{$url.theme.shared}images/loader.gif" alt="Loading Icon" title="Please Wait" border="0" />{t}Please Wait{/t}...{/if}
</div>

</div></div></div>
{if !$dialogNoClose}
<input type="image" src="{$url.theme.shared}images/dialog/close.gif" class="jqmdX jqmClose" />
{/if}
</div>


{* Cache Dialog Images... *}
{if !$smarty.capture.dialogCache}
{capture name=dialogCache}1{/capture}
{literal}
<script type="text/javascript">
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
});
</script>
{/literal}
<div class="imgCache">
	<img src="{$url.theme.shared}images/loader.gif" />
	<img src="{$url.theme.shared}images/dialog/close.gif" />
	<img src="{$url.theme.shared}images/dialog/close_hover.gif" />
	<img src="{$url.theme.shared}images/dialog/sprite.gif" />
	<img src="{$url.theme.shared}images/dialog/bl.gif" />
	<img src="{$url.theme.shared}images/dialog/br.gif" />
	<img src="{$url.theme.shared}images/dialog/bc.gif" />
</div>
{/if}