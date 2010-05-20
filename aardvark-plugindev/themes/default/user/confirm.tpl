{include file="inc/user.header.tpl"}

<h2>{t}Subscriber Confirmation{/t}</h2>

<p><a href="{$config.site_url}"><img src="{$url.theme.shared}images/icons/back.png" alt="back icon" class="navimage" /> {t website=$config.site_name}Return to %1{/t}</a></p>

{include file="inc/messages.tpl"}

{t}If you have received your verification email, enter the activation code below;{/t}

<form method="get" action="">

<fieldset>
<legend>{t}Activation Code{/t}</legend>

<div>
<input type="text" name="code" />
</div>

<div class="buttons">
<input type="submit" name="codeTry" value="{t}Submit{/t}" />
</div>

</form>


{include file="inc/user.footer.tpl"}