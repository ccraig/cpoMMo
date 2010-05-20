
<div id="sidebar">
	<img src="{$url.theme.shared}images/pommo.png" alt="poMMo Logo" class="logo" />

	{if $section == "setup"}
	<!-- start section nav -->
	<h1>{t}poMMo Setup{/t}</h1>
	<div class="submenu">
		<a href="setup_configure.php">{t}Configure{/t}</a> 
		<a href="setup_fields.php">{t}Fields{/t}</a>
		<a href="setup_form.php">{t}Setup Form{/t}</a>
	</div>
	<!-- end section nav -->
	{elseif $section == "mailings"}
	<!-- start section nav -->
	<h1>{t}poMMo Setup{/t}</h1>
	<div class="submenu">
		<a href="mailings_send.php">{t}Send{/t}</a> 
		<a href="mailings_history.php">{t}History{/t}</a>
	</div>
	<!-- end section nav -->
	{elseif $section == "subscribers"}
	<!-- start section nav -->
	<h1>{t}poMMo Setup{/t}</h1>
	<div class="submenu">
		<a href="subscribers_manage.php">{t}Manage{/t}</a> 
		<a href="subscribers_import.php">{t}Import{/t}</a> 
		<a href="subscribers_groups.php">{t}Groups{/t}</a>
	</div>
	<!-- end section nav -->
	{/if}

	<!-- begin nav -->
	<h1>Sections</h1>
	<div class="submenu">
		<a href="{$url.base}admin/mailings/admin_mailings.php">{t}Mailings{/t}</a>
		<a href="{$url.base}admin/subscribers/admin_subscribers.php">{t}Subscribers{/t}</a>
		<a href="{$url.base}admin/setup/admin_setup.php">{t}Setup{/t}</a>	
	</div>
	<!-- end nav -->
	{if $config.demo_mode == "on"}
	<p><img src="{$url.theme.shared}images/icons/demo.png" class="sideimage">{t}Demonstration mode is ON.{/t}</p>	
	{else}
	<p><img src="{$url.theme.shared}images/icons/nodemo.png" class="sideimage">{t}Demonstration mode is OFF.{/t}</p>	
	{/if}

</div>
<!-- end sidebar -->