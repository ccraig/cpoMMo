{include file="inc/user.header.tpl"}

<h2>{t}Pending Changes{/t}</h2>

<p><a href="{$config.site_url}"><img src="{$url.theme.shared}images/icons/back.png" alt="back icon" class="navimage" />{t website=$config.site_name}Return to %1{/t}</a></p>

{include file="inc/messages.tpl"}

{if !$nodisplay}
<form method="post" action="">
<fieldset>
<legend>Pending user</legend>

<div class="buttons">

<input type="submit" name="reconfirm" value="{t}SEND another confirmation email{/t}" />

<input type="submit" name="cancel" value="{t}CANCEL your pending request{/t}" />

</div>

</fieldset>

</form>
{/if}

{include file="inc/user.footer.tpl"}