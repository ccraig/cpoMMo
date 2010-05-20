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

<div id="{if $dialogID}{$dialogID}{else}dialog{/if}" class="jqmDialog hidden{if $dialogClass} {$dialogClass}{/if}">
<div class="jqmdTL"><div class="jqmdTR"><div class="jqmdTC {if $dialogDrag}dragHandle{/if}">
{if $dialogTitle}
{$dialogTitle}
{else}
poMMo
{/if}
</div></div></div>
<div class="jqmdBL"><div class="jqmdBR"><div class="jqmdBC{if $dialogBodyClass} {$dialogBodyClass}{/if}">

<div class="jqmdMSG{if $dialogMsgClass} {$dialogMsgClass}{/if}">
{if $dialogContent}
{$dialogContent}
{else}
<img src="{$url.theme.shared}images/loader.gif" alt="Loading Icon" title="Please Wait" border="0" /></a>{t}Please Wait{/t}...
{/if}
</div>

</div></div></div>

<div>
<input type="image" src="{$url.theme.shared}images/dialog/close.gif" class="jqmdClose jqmClose" />
</div>

</div>

{literal}
<script type="text/javascript">
$().ready(function() {
	// Close Button Highlighting for *IE* (since IE does not support :hover selector...)
	$('input.jqmdClose')
	.hover(
		function(){ $(this).addClass('jqmdCloseFocus'); }, 
		function(){ $(this).removeClass('jqmdCloseFocus'); })
	.focus( 
		function(){ this.hideFocus=true; $(this).addClass('jqmdCloseFocus'); })
	.blur( 
		function(){ $(this).removeClass('jqmdCloseFocus'); });
});
</script>
{/literal}

{* Cache Dialog Images... *}
{if !$dialogCache}
{assign var='dialogCache' value=true}

<!-- optional: image cacheing. Any images contained in this div will be
	loaded offscreen, and thus cached -->
{literal}
<style type="text/css">
/* Caching CSS courtesf of;
	Klaus Hartl <klaus.hartl@stilbuero.de> */
@media projection, screen {
     div.imgCache { position: absolute; left: -8000px; top: -8000px; }
     div.imgCache img { display:block; }
}
@media print { div.imgCache { display: none; } }
</style>
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