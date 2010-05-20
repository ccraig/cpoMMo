{include file="inc/user.header.tpl"}

<h2>{t}Subscriber Login{/t}</h2>

<p>{t}In order to check your subscription status, update your information, or unsubscribe, you must enter your email address in the field below.{/t}</p>

{include file="inc/messages.tpl"}

<form method="post" action="">
<fieldset>
<h3>{t}Login{/t}</h3>

<div>
<label for="email">{t}Your Email:{/t} <span class="error">{validate id="email" message=$formError.email}</span></label>
<input type="text"name="Email" id="email" value="{$Email|escape}" size="32" maxlength="60" />
</div>

</fieldset>

<div class="buttons">
<button type="submit" value="{t}Login{/t}" class="positive"><img src="{$url.theme.shared}/images/icons/tick.png" alt="Login"/>{t}Login{/t}</button>
</div>

</form>

<br class="clear" />

{include file="inc/user.footer.tpl"}