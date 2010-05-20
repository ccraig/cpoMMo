{include file="user/inc.header.tpl"}

<div id="header"><h1>{t}Subscription Review{/t}</h1></div>

{if $back}
<a href="{$referer}">
		<img src="{$url.theme.shared}images/icons/back.png" align="middle" class="navimage" border='0'>
		{t website=$config.site_name}Back to Subscription Form{/t}</a>
{else}
<a href="{$config.site_url}">
		<img src="{$url.theme.shared}images/icons/back.png" align="middle" class="navimage" border='0'>
		{t website=$config.site_name}Return to %1{/t}</a>
{/if}

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
 	
 	{if $dupe}
 		<div class="msgdisplay">
 		{t escape=no 1="<a href=\"login.php\">" 2='</a>'}If you would like to update your records click %1here%2{/t}
 		</div>
 	{/if}

{include file="user/inc.footer.tpl"}