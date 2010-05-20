{capture name=head}{* used to inject content into the HTML <head> *}
{include file="inc/ui.cssTable.tpl"}
{/capture}{include file="inc/admin.header.tpl"}

<h2><img src="{$url.theme.shared}images/icons/groups.png" class="navimage left" alt="groups icon" />{t}Groups Page{/t}</h2>

{t escape=no 1="<a href=\"`$url.base`admin/setup/setup_fields.php\">" 2="</a>"}Subscriber Groups allow you to mail subsets of subscribers instead of the entire list. Groups are defined by customizable matching rules, and members are automatically assigned based on their %1subscriber field%2 values.{/t}

<br class="clear"/>

<form method="post" action="">

{include file="inc/messages.tpl"}

<fieldset>
<h3>{t}New group{/t}</h3>

<div>
<label for="group_name">{t}Group Name: {/t}</label>
<input type="text" title="{t}type new group name{/t}" name="group_name" id="group_name" maxlength="60" size="20" />
</div>

<div class="buttons">
<button type="submit" value="{t}Add{/t}" class="positive"><img src="{$url.theme.shared}/images/icons/tick.png" alt="add group"/>{t}Add Group{/t}</button>
</div>

</fieldset>
</form>

<br class="clear"/>

<fieldset>
<h3>{t}Groups{/t}</h3>

<div id="grid">

<div class="header">
<span>{t}Delete{/t}</span>
<span>{t}Edit{/t}</span>
<span>{t}Group Name{/t}</span>	
</div>

{foreach from=$groups key=id item=name}

<div class="{cycle values="r1,r2,r3"} sortable" id="id{$id}">

<span>
<a href="{$smarty.server.PHP_SELF}?group_id={$id}&amp;delete=TRUE'; return false;" onclick="return confirm('Delete this group?');"><img src="{$url.theme.shared}images/icons/delete.png" alt="delete icon" /></a>
</span>

<span>
<a href='groups_edit.php?group={$id}'; return false;"><img src="{$url.theme.shared}images/icons/edit.png" alt="edit icon" /></a>
</span>

<span>
{$name}
</span>

</div>
{/foreach}

</div>

</fieldset>
{include file="inc/admin.footer.tpl"}