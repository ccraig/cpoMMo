{include file="inc/user.header.tpl"}

<h2>{t}Update Activation{/t}</h2>

{include file="inc/messages.tpl"}

{t}Hello!{/t} {t}Before you can update your records on unsubscribe, we must first verify your email address.{/t}

<form method="get" action="">
<input type="hidden" name="Email" value="{$Email}" />

<div>
<input type="submit" name="send" value="{t}Send a verification email{/t}">
</div>

<fieldset>
<legend>{t}Activation Code{/t}</legend>

{t}If you have received your verification email, enter the activation code below;{/t}

<div>
<input type="text" name="code" />
</div>

<div class="buttons">
<input type="submit" name="codeTry" value="{t}Submit{/t}" />
</div>

</fieldset>

</form>


{include file="inc/user.footer.tpl"}