{include file="inc/admin.header.tpl"}

<h2>{t}Admin Menu{/t}</h2>

<div id="language" class="right">
<form method="POST" action="" id="language">
<select name="lang" onChange="this.form.submit();">
<option value="en">English (en)</option>
<option value="en-uk" {if $lang == 'en-uk'}SELECTED{/if}>British English (en-uk)</option>
<option value="bg" {if $lang == 'bg'}SELECTED{/if}>българ�?ки (bg)</option>
<option value="br" {if $lang == 'br'}SELECTED{/if}>português (br)</option>
<option value="da" {if $lang == 'da'}SELECTED{/if}>dansk (da)</option>
<option value="de" {if $lang == 'de'}SELECTED{/if}>Deutsch (de)</option>
<option value="es" {if $lang == 'es'}SELECTED{/if}>español (es)</option>
<option value="fr" {if $lang == 'fr'}SELECTED{/if}>français (fr)</option>
<option value="it" {if $lang == 'it'}SELECTED{/if}>italiano (it)</option>
<option value="nl" {if $lang == 'nl'}SELECTED{/if}>Nederlands (nl)</option>
<option value="ro" {if $lang == 'ro'}SELECTED{/if}>română (ro)</option>
<option value="ru" {if $lang == 'ru'}SELECTED{/if}>ру�?�?кий �?зык (ru)</option>
</select>
</form>
</div>

{include file="inc/messages.tpl"}

<div id="boxMenu">

<div><a href="{$url.base}admin/mailings/admin_mailings.php"><img src="{$url.theme.shared}images/icons/mailing.png" alt="envelope icon" class="navimage" /> {t}Mailings{/t}</a> - {t}Send mailings to the entire list or to a subset of subscribers. Mailing status and history can also be viewed from here.{/t}</div>

<div><a href="{$url.base}admin/subscribers/admin_subscribers.php"><img src="{$url.theme.shared}images/icons/subscribers.png" alt="people icon" class="navimage" /> {t}Subscribers{/t}</a> - {t}Here you can list, add, delete, import, export, and update your subscribers. You can also create groups (subsets) of your subsribers from here.{/t}</div>

<div><a href="{$url.base}admin/setup/admin_setup.php"><img src="{$url.theme.shared}images/icons/settings.png" alt="hammer and screw icon" class="navimage" /> {t}Setup{/t}</a> - {t}This area allows you to configure poMMo. Set mailing list parameters, choose the information you'd like to collect from subscribers, and generate subscription forms from here.{/t}</div>


	{*corinna Display this only if plugins are activated in $pommo->_useplugins *}
	{if $showplugin}
		<div>
			<a href="{$url.base}plugins/adminplugins/plugins.php">
			<img src="" class="navimage" width="64" height="64" />
			{t}Additional Functionality{/t}</a> - 
			{t}PLUGINS LINK! Set up all the Plugins: Authentication methods, User Administration, and more...
				Define permissions, mailing lists, responsible persons, bounce settings and
				many more...
			{/t}
		</div>
	{/if}
	{*corinna End additional plugin functionality *}

</div>

{include file="inc/admin.footer.tpl"}