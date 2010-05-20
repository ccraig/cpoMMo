{include file="inc/admin.header.tpl"}

<h2><img src="{$url.theme.shared}images/icons/language.png" alt="language icon" class="navimage left" />{t}Language Settings{/t}</h2>

Please select the language for the poMMo mailing management software.

<p>
<div id="language" class="left">
<form method="POST" action="" id="language">
<select name="lang" onChange="this.form.submit();">
<option value="en">English (en)</option>
<option value="en-uk" {if $lang == 'en-uk'}SELECTED{/if}>british english (en-uk)</option>
<option value="bg" {if $lang == 'bg'}SELECTED{/if}>български (bg)</option>
<option value="da" {if $lang == 'da'}SELECTED{/if}>dansk (da)</option>
<option value="de" {if $lang == 'de'}SELECTED{/if}>deutsch (de)</option>
<option value="es" {if $lang == 'es'}SELECTED{/if}>español (es)</option>
<option value="fr" {if $lang == 'fr'}SELECTED{/if}>français (fr)</option>
<option value="it" {if $lang == 'it'}SELECTED{/if}>italiano (it)</option>
<option value="nl" {if $lang == 'nl'}SELECTED{/if}>nederlands (nl)</option>
<option value="pl" {if $lang == 'pl'}SELECTED{/if}>polski (pl)</option>
<option value="pt" {if $lang == 'pt'}SELECTED{/if}>português (pt)</option>
<option value="pt-br" {if $lang == 'pt-br'}SELECTED{/if}>brasil português (pt-br)</option>
<option value="ro" {if $lang == 'ro'}SELECTED{/if}>română (ro)</option>
<option value="ru" {if $lang == 'ru'}SELECTED{/if}>русский язык (ru)</option>
</select>
</form>
</div>
</p>

<br class="clear"/>

{include file="inc/messages.tpl"}

{include file="inc/admin.footer.tpl"}