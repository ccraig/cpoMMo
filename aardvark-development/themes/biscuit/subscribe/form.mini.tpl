<div id="subscribeForm">

<form method="post" action="{$url.base}user/subscribe.php">
<fieldset>
<h3>Join newsletter</h3>

{if $referer}
<input type="hidden" name="bmReferer" value="{$referer}" />
{/if}

<div>
<label for="email">{t}Your Email:{/t}</label>
<input type="text" size="20" maxlength="60" name="Email" id="email" />
</div>

</fieldset>

<div class="buttons">
<button type="submit" value="{t}Subscribe{/t}" class="positive"><img src="{$url.theme.shared}/images/icons/tick.png" alt="Subscribe"/>{t}Subscribe{/t}</button>
</div>

</form>

<br class="clear" />

</div>