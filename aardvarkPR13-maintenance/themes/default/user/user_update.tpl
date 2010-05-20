{include file="user/inc.header.tpl"}
<div id="header"><h1>{t}Subscriber Update{/t}</h1></div>

<a href="{$config.site_url}">
		<img src="{$url.theme.shared}images/icons/back.png" align="middle" border='0'>
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
 	

{include file="subscribe/form.update.tpl"}

<div style="margin-top: 20px; margin-left: 30px;">
	<form action="" method="post">
	<input type="hidden" name="original_email" value="{$original_email}">
	<img src="{$url.theme.shared}images/icons/nok.png" align="bottom" border='0'>
	<input type="hidden" name="bm_email" value="{$bm_email}">
	<input type="submit" name="unsubscribe" value="{t}Click to Unsubscribe{/t}">
	</form>
</div>

{include file="user/inc.footer.tpl"}