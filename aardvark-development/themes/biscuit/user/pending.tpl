{include file="inc/user.header.tpl"}

<h2>{t}Pending Changes{/t}</h2>

<p><a href="{$config.site_url}"><img src="{$url.theme.shared}images/icons/back.png" alt="back icon" class="navimage" />{t website=$config.site_name}Return to %1{/t}</a></p>

{include file="inc/messages.tpl"}

{if !$nodisplay}
<form method="post" action="">
<fieldset>
<h3>Pending User</h3>

<div class="buttons">
<button type="submit" name="reconfirm" value="{t}SEND another confirmation email{/t}" class="positive"><img src="{$url.theme.shared}/images/icons/tick.png" alt="Send"/>{t}SEND another confirmation email{/t}</button>
<button type="submit" name="cancel" value="{t}CANCEL your pending request{/t}" class="negative"><img src="{$url.theme.shared}/images/icons/cross.png" alt="Cancel"/>{t}CANCEL your pending request{/t}</button>
</div>

</fieldset>

</form>
{/if}

{include file="inc/user.footer.tpl"}