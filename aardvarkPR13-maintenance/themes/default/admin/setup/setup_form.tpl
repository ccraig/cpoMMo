{include file="admin/inc.header.tpl"}
{include file="admin/inc.sidebar.tpl"}

<div id="mainbar">

	<h1>{t}Subscription Forms{/t}</h1>
	
	<p>
		<a href="{$url.base}user/subscribe.php">
		<img src="{$url.theme.shared}images/icons/form.png" class="navimage" />							
		{t}Default Subscription Form{/t}</a> - {t}Preview the default subscription form. Its look and feel can be adjusted through the theme template ([theme]/user/subscribe.tpl).{/t}
	</p>
	
	<p>
		<a href="{$url.base}admin/setup/form_embed.php">
		<img src="{$url.theme.shared}images/icons/embed.png" class="navimage" />							
		{t}Embedded Subscription From{/t}</a> - {t}Preview subscription forms that you can embed into an area of an existing webpage.{/t}
		<br>&nbsp;
	</p>
	
	<p>
		<a href="{$url.base}admin/setup/form_generate.php">
		<img src="{$url.theme.shared}images/icons/plain.png" class="navimage" />							
		{t}HTML Subscription Form{/t}</a> - {t}Generate a plain HTML subscription form that you can customize to fit your site..{/t}
		<br>&nbsp;
	</p>
	
	<b>{t}Important URLs{/t}</b>
	<ul>
		<li>{t}Default Form{/t} -> {$url.base}user/subscribe.php
		<li>{t}Embedded Form{/t} -> {$url.base}embed.form.php
		<li>{t}Embedded Mini Form{/t} -> {$url.base}embed.miniform.php
	</ul>
	
</div>
<!-- end mainbar -->

{include file="admin/inc.footer.tpl"}