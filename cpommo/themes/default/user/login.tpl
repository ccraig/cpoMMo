{include file="inc/user.header.tpl"}

<h2>{t}Subscriber Login{/t}</h2>

<p>{t}In order to check your subscription status, update your information, or unsubscribe, you must enter your email address in the field below.{/t}</p>

{include file="inc/messages.tpl"}

<form method="post" action="">
<fieldset>
<legend>{t}Login{/t}</legend>

<div>
<label for="email"><strong class="required">{t}Your Email:{/t}</strong> <span class="error">{validate id="email" message=$formError.email}</span></label>
<input type="text"name="Email" id="email" value="{$Email|escape}" size="32" maxlength="60" />
</div>

</fieldset>

<div class="buttons">

<input type="submit" value="{t}Login{/t}" />

</div>

</form>

{include file="inc/user.footer.tpl"}