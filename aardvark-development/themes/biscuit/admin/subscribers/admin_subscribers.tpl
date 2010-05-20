{include file="inc/admin.header.tpl"}

<h2>{t}Subscribers Page{/t}</h2>

<ul id="sec_nav">
	<li><a href="{$url.base}admin/subscribers/subscribers_manage.php"><img src="{$url.theme.shared}images/icons/examine.png" alt="manage icon" class="navimage" />
	<h3>{t}Manage Subscribers{/t}</h3>
	<span>{t}See an overview of your current and pending subscribers. You can add, delete, and edit subscribers from here.{/t}</span></a>
	</li>
    
	<li><a href="{$url.base}admin/subscribers/subscribers_import.php"><img src="{$url.theme.shared}images/icons/import.png" alt="user icon" class="navimage" />
	<h3>{t}Import Subscribers{/t}</h3>
	<span>{t}You can import large amounts of subscribers using files stored on your computer or by typing / pasting in multiple email addresses.{/t}</span></a>
	</li>
    
    <li><a href="{$url.base}admin/subscribers/subscribers_groups.php"><img src="{$url.theme.shared}images/icons/groups.png" alt="group icon" class="navimage" />
	<h3>{t}Mailing Groups{/t}</h3>
	<span>{t}Manage "mailing groups" from this area. Mailing groups allow you to mail subsets of your subscribers, rather than just the entire list.{/t}</span></a>
	</li>

	<li><a href="{$url.base}admin/setup/setup_fields.php"><img src="{$url.theme.shared}images/icons/fields.png" alt="subscriber icon" class="navimage" />
	<h3>{t}Subscriber Fields{/t}</h3>
    <span>{t}Choose the information you'd like to collect from your subscribers.{/t}</span></a>
	</li>
</ul>

{include file="inc/admin.footer.tpl"}