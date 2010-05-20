{include file="inc/admin.header.tpl"}

<h2>{t}Admin Menu{/t}</h2>

<div id="language" class="right">
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

{include file="inc/messages.tpl"}

<div id="boxMenu">

<div><a href="{$url.base}admin/mailings/admin_mailings.php"><img src="{$url.theme.shared}images/icons/mailing.png" alt="envelope icon" class="navimage" /> {t}Mailings{/t}</a> - {t}Send mailings to the entire list or to a subset of subscribers. Mailing status and history can also be viewed from here.{/t}</div>

<div><a href="{$url.base}admin/subscribers/admin_subscribers.php"><img src="{$url.theme.shared}images/icons/subscribers.png" alt="people icon" class="navimage" /> {t}Subscribers{/t}</a> - {t}Here you can list, add, delete, import, export, and update your subscribers. You can also create groups (subsets) of your subsribers from here.{/t}</div>

<div><a href="{$url.base}admin/setup/admin_setup.php"><img src="{$url.theme.shared}images/icons/settings.png" alt="hammer and screw icon" class="navimage" /> {t}Setup{/t}</a> - {t}This area allows you to configure poMMo. Set mailing list parameters, choose the information you'd like to collect from subscribers, and generate subscription forms from here.{/t}</div>

<br />
<p>
{t}If you like poMMo, please consider a dontation to support its development{/t}: <br />
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="image" src="https://www.paypal.com/en_US/i/btn/x-click-butcc-donate.gif" border="0" name="submit" alt="Make payments with PayPal - it's fast, free and secure!">
<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
">
</form>
</p>

</div>


{include file="inc/admin.footer.tpl"}