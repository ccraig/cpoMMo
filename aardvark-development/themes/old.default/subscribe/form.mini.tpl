<div id="subscribeForm">

<form method="post" action="{$url.base}user/subscribe.php">
<fieldset>
<legend>Join newsletter</legend>

{if $referer}
<input type="hidden" name="bmReferer" value="{$referer}" />
{/if}

<div>
<label for="email">{t}Your Email:{/t}</label>
<input type="text" size="20" maxlength="60" name="Email" id="email" />
</div>

</fieldset>

<div class="buttons">

<input type="submit" value="{t}Subscribe{/t}" />

</div>

</form>
</div>