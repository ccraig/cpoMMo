{include file="admin/inc.header.tpl"}
{include file="admin/inc.sidebar.tpl"}

<div id="mainbar">

<h1>{t}Groups Page{/t}</h1>

<img src="{$url.theme.shared}images/icons/groups.png" class="articleimg">

<p>
{t}Create groups of subscribers based off the values of subscriber fields. You can then mail subscribers belonging to a group instead your entire list.{/t}
</p>


<h2>{t}Groups{/t} &raquo;</h2>
  
{if $messages}
    <div class="msgdisplay">
    {foreach from=$messages item=msg}
   	 <div>* {$msg}</div>
    {/foreach}
    </div>
 {/if}
 
 <form action="" method="POST">
	<div class="field">
		<b>{t}Make New{/t} &raquo;</b>
		<input type="text" class="text"  title="{t}type new group name{/t}" maxlength="60" size="30" 
		name="group_name" id="group_name"  value="{t}type new group name{/t}" />
		<input class="button" type="submit" value="{t}Add{/t}" />
	</div>
</form>

<div width="100%">

	<span>{t}Delete{/t}</span>
	<span style="margin-left: 20px;">{t}Edit{/t}</span>
	<span style="text-align:left; margin-left: 20px;">{t}Group Name{/t}</span>
	
	{foreach from=$groups key=id item=name}
	<div style="border-top: 1px dotted; padding: 5px;">
		<a href="{$smarty.server.PHP_SELF}?group_id={$id}&delete=TRUE&group_name={$name}">
	 	 		<img src="{$url.theme.shared}images/icons/delete.png" border="0" align="absmiddle"></a>
		<span style="margin-left: 25px;">
		<a href="groups_edit.php?group_id={$id}">
				<img src="{$url.theme.shared}images/icons/edit.png" border="0" align="absmiddle"></a>
		</span>
		<span style="text-align:left; margin-left: 25px;">
			<strong>{$name}</strong>
		</span>
	</div>
	{foreachelse}
	 	<div><br><strong>{t}No groups have been assigned.{/t}</strong></div>
	{/foreach}

</div>

   
</div>
<!-- end mainbar -->

{include file="admin/inc.footer.tpl"}