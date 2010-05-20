{include file="admin/inc.header.tpl"}
{include file="admin/inc.sidebar.tpl"}

<div id="mainbar">

<h1>{t}Setup Page{/t}</h1>

		<p>
			<a href="{$url.base}admin/setup/setup_configure.php">
			<img src="{$url.theme.shared}images/icons/settings.png" class="navimage" />
			{t}Configure{/t}</a> - 
			{t}poMMo. Set your mailing list name, its default behavior, and the administrator's information.{/t}
			<br>&nbsp;

		</p>		
						
		<p>
			<a href="{$url.base}admin/setup/setup_fields.php">
			<img src="{$url.theme.shared}images/icons/fields.png" class="navimage" />							
			{t}Subscriber Fields{/t}</a> - 
			{t}Choose the information you'd like to collect from your subscribers.{/t}
			<br>&nbsp;
		</p>
			
		<p>
			<a href="{$url.base}admin/setup/setup_form.php">
			<img src="{$url.theme.shared}images/icons/form.png" class="navimage" />
			{t}Subscription Form{/t}</a> - 
			{t}Preview and Generate the subscription form for your website.{/t}
			<br>&nbsp;
		</p>

 <p class="clearer"></p>
 
 </div>
<!-- end mainbar -->

{include file="admin/inc.footer.tpl"}