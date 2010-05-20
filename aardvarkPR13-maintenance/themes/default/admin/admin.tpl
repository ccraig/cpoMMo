{include file="admin/inc.header.tpl"}
{include file="admin/inc.sidebar.tpl"}

<p class="introduction">
{t escape=no url=$config.app.weblink}Hello and welcome to the poMMo "Aardvark" release. The "A" in Aardvark stands for "alpha" and designates this as an early version of poMMo. Much development is underway, so please report your findings and suggestions to the %1.{/t}
</p>

<div id="mainbar">

<h1>{t}Admin Menu{/t}</h1>
		<p>
			<a href="{$url.base}admin/setup/admin_setup.php">
			<img src="{$url.theme.shared}images/icons/settings.png" class="navimage" />
			{t}Setup{/t}</a> - 
			{t}This area allows you to configure poMMo and its default behavior. Set mailing list parameters, choose the information you'd like to collect from subscribers, and generate subscription forms from here.{/t}
		</p>					
		
		<p>
			<a href="{$url.base}admin/subscribers/admin_subscribers.php">
			<img src="{$url.theme.shared}images/icons/subscribers.png" class="navimage" />
			{t}Subscribers{/t}</a> - 
			{t}Here you can list, add, delete, import, export, and update your subscribers. You can also create groups (subsets) of your subsribers from here.{/t}
		</p>

						
		<p>
			<a href="{$url.base}admin/mailings/admin_mailings.php">
			<img src="{$url.theme.shared}images/icons/mailing.png" class="navimage" />
			{t}Mailings{/t}</a> - 
			{t}Send mailings to the entire list or to a subset of subscribers. Mailing status and history can also be viewed from here.{/t}
		</p>
 
 </div>
<!-- end mainbar -->

{include file="admin/inc.footer.tpl"}