{* Dialog Include -- 
invoke via {include file="inc/dialog.tpl" param="value" ... }

Valid parameters
-------
id: (str) - DOM ID; "dialog" by default
wide: (bool) - Wide Dialog; false
tall: (bool) - Tall Dialog; false
short: (bool) - Short Dialog; false
content: (str) - Initial Contents of dialog; null
title: (str) window title ; "poMMo",
wait: (bool) - wait dialog [no close button] ; false
*}


<div id="{if $id}{$id}{else}dialog{/if}" class="jqmDialog {if $wide}jqmdWide{/if}">
	<div class="jqmdTL"><div class="jqmdTR"><div class="jqmdTC">
		{if $title}{$title}{else}poMMo{/if}
	</div></div></div>
	<div class="jqmdBL"><div class="jqmdBR"><div class="jqmdBC {if $tall}jqmdTall{/if} {if $short}jqmdShort{/if}">
	<div class="jqmdMSG{if $dialogMsgClass} {$dialogMsgClass}{/if}">
		{if $content}{$content}{else}<img src="{$url.theme.shared}images/loader.gif" alt="Loading Icon" title="Please Wait" border="0" />{t}Please Wait{/t}...{/if}
	</div>
	</div></div></div>
	{if !$wait}<input type="image" src="{$url.theme.shared}images/dialog/close.gif" class="jqmdX jqmClose" />{/if}

</div>