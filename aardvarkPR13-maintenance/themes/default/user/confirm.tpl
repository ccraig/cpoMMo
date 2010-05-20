{include file="user/inc.header.tpl"}

<div id="header"><h1>{t}Subscriber Confirmation{/t}</h1></div>

<a href="{$config.site_url}">
		<img src="{$url.theme.shared}images/icons/back.png" align="middle" class="navimage" border='0'>
		{t website=$config.site_name}Return to %1{/t}</a>
<br>
    {if $messages}
    	<div class="msgdisplay">
    	{foreach from=$messages item=msg}
    	<div>* {$msg}</div>
    	{/foreach}
    	</div>
 	{/if}
 	
 	{if $errors}
 		<br>
    	<div class="errdisplay">
    	{foreach from=$errors item=msg}
    	<div>* {$msg}</div>
    	{/foreach}
    	</div>
 	{/if}

{include file="user/inc.footer.tpl"}