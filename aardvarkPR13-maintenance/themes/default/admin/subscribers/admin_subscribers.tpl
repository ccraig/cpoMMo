{include file="admin/inc.header.tpl"}
{include file="admin/inc.sidebar.tpl"}

<div id="mainbar">

	<h1>{t}Subscribers Page{/t}</h1>
			
	<p>
		<a href="{$url.base}admin/subscribers/subscribers_manage.php">
		<img src="{$url.theme.shared}images/icons/examine.png" class="navimage" />
		{t}Manage{/t}</a> -
		{t}subscribers. See an overview of your current and pending subscribers. You can add, delete, and edit subscribers from here.{/t}
		<br>&nbsp;
	</p>		
						
	<p>
		<a href="{$url.base}admin/subscribers/subscribers_import.php">
		<img src="{$url.theme.shared}images/icons/import.png" class="navimage" />							
		{t}Import{/t}</a> - 
		{t}Subscribers.  You can import large amounts of subscribers using files stored on your computer.{/t}
		<br>&nbsp;
	</p>

	<p>
		<a href="{$url.base}admin/subscribers/subscribers_groups.php">
		<img src="{$url.theme.shared}images/icons/groups.png" class="navimage" />
		{t}Groups{/t}</a> - 
		{t}Manage "mailing groups" from this area. Mailing groups allow you to mail subsets of your subscribers, rather than just the entire list.{/t}
	</p>	
 
</div>
<!-- end mainbar -->

{include file="admin/inc.footer.tpl"}