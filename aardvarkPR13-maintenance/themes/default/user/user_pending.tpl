{include file="user/inc.header.tpl"}

<div id="header"><h1>{t}Pending Changes{/t}</h1></div>

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

{if !$nodisplay}
<br>
<form action="" method="POST">
	<div style="text-align: center;">
		<input type="submit" name="reconfirm" value="{t}Click to *send* another confirmation email{/t}">
		<input style="margin-left: 40px;" type="submit" name="cancel" 
		value="{t}Click to *cancel* your pending request{/t}">
	</div>
</form>
{/if}

{include file="user/inc.footer.tpl"}