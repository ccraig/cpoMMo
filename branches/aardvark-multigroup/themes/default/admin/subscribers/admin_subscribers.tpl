{include file="inc/admin.header.tpl"}

<h2>{t}Subscribers Page{/t}</h2>

<div id="boxMenu">

<div><a href="{$url.base}admin/subscribers/subscribers_manage.php"><img src="{$url.theme.shared}images/icons/examine.png" alt="manage icon" class="navimage" />{t}Manage{/t}</a> -{t}subscribers. See an overview of your current and pending subscribers. You can add, delete, and edit subscribers from here.{/t}</div>

<div><a href="{$url.base}admin/subscribers/subscribers_import.php"><img src="{$url.theme.shared}images/icons/import.png" alt="user icon" class="navimage" />{t}Import{/t}</a> - {t}Subscribers. You can import large amounts of subscribers using files stored on your computer.{/t}</div>

<div><a href="{$url.base}admin/subscribers/subscribers_groups.php"><img src="{$url.theme.shared}images/icons/groups.png" alt="group icon" class="navimage" />{t}Groups{/t}</a> - {t}Manage "mailing groups" from this area. Mailing groups allow you to mail subsets of your subscribers, rather than just the entire list.{/t}</div>	

</div>

{include file="inc/admin.footer.tpl"}