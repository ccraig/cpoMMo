{capture name=head}{* used to inject content into the HTML <head> *}
<script src="{$url.theme.shared}js/scriptaculous/prototype.js" type="text/javascript"></script>
<script src="{$url.theme.shared}js/scriptaculous/effects.js" type="text/javascript"></script>
{/capture}{include file="admin/inc.header.tpl"}
{include file="admin/inc.sidebar.tpl"}

{literal}
<style>
	/* NECESSARY TO SET STYLING HERE FOR IE6 TO PICKUP ON IT.... */
	/* BACK BUTTON STYLING FOR FILTER CRITERIA */
	.goback { 
		font-size: 120%; 
		font-weight: bold; 
		text-decoration: underline;
		padding: 4px;
		display: inline;
		cursor:pointer;
		cursor:hand;
	}
</style>
{/literal}


<div id="mainbar">

<h1>{t}Edit Group{/t}</h1>

<img src="{$url.theme.shared}images/icons/groups.png" class="articleimg">

<p>
{t}They are made up of "filters" that match field values or other groups. For instance, if you collect  "age" and "country", you can match subscribers 21 and older living in Japan by creating two filtering critiera; one which matches "age" to a value GREATER THAN 20, and another which matches "country" EQUAL TO "Japan"{/t} 
<p>

<a href="{$url.base}admin/subscribers/subscribers_groups.php">
		<img src="{$url.theme.shared}images/icons/back.png" align="middle" class="navimage" border='0'>
		{t 1=$returnStr}Return to %1{/t}</a>

<h2>{$group_name} &raquo;</h2>
  
{if $messages}
    <div class="msgdisplay">
    {foreach from=$messages item=msg}
   	 <div>* {$msg}</div>
    {/foreach}
    </div>
 {/if}
 
<form id="nameForm" name="nameForm" action="" method="POST">
	<div class="field">
		{t}Group Name:{/t}
		<input type="text" class="text"  title="{t}type new group name{/t}" maxlength="60" size="30" 
		name="group_name" id="group_name"  value="{$group_name}" />
		<input class="button" type="submit" name="rename" value="{t}Rename{/t}" />
	</div>
</form>


<form id="filterForm" name="filterForm" action="" method="POST">

<div id="newFilter">

	<div id="field" style="margin-bottom: 10px;" >
		{t escape=no 1="<strong>" 2="</strong>"}Select a %1 field %2 to filter{/t}
		
		<select id="field_id" name="field_id" onChange="updateLogic()">
			<option value="">{t}Choose subscriber field{/t}</option>
			{foreach from=$demos key=id item=demo}
				<option value="{$id}">{$demo.name}</option>
			{/foreach}
		</select>
	</div>
	
	<div id="group">
	
	{if count($groups) > 1}
			{t escape=no 1="<strong>" 2="</strong>"}or, Select a %1 group %2 to{/t} 
			<select name="group_logic" id="group_logic" onChange="updateGroup({$group_id})">
				<option value="">Choose to Include or Exclude</option>	
				<option value="is_in">{t}Include{/t}</option>
				<option value="not_in">{t}Exclude{/t}</option>
			</select>
	{else}
		{* PLACEHOLDER FOR group_logic *}<span id="group_logic" style="visibility: hidden;"></span>
	{/if}
	
	</div>	
</div>
	<div id="critLogic" style="float: right; background-color: #e6eaff; padding: 7px; border: 1px solid; margin: 2px;"></div>
</form>

{t escape=no 1="<em>`$filterCount`</em>" 2="<strong>`$tally`</strong>"}%1 filters match a total of %2 subscribers{/t}
<hr>

<div id="filters">

<span>{t}Delete{/t}</span>
<span style="margin-left: 20px;">{t}Edit{/t}</span>
<span style="text-align:left; margin-left: 20px;">{t}Filter Details{/t}</span>
	
{foreach from=$filters key=filter_id item=filter}
<div style="border-top: 1px dotted; padding: 5px;">
	<a href="{$smarty.server.PHP_SELF}?filter_id={$filter_id}&delete=TRUE&group_id={$group_id}">
 	 		<img src="{$url.theme.shared}images/icons/delete.png" border="0" align="absmiddle"></a>
	<span style="margin-left: 25px; cursor:pointer; cursor:hand;" onClick="filterUpdate('{$filter_id}','{$group_id}')" >
			<img src="{$url.theme.shared}images/icons/edit.png" border="0" align="absmiddle">
	</span>
	<span style="text-align:left; margin-left: 12px;">
		{if $filter.logic == 'is_in'}
			{t}Include subscribers belonging to{/t} <strong>{$groups[$filter.field_id]}</strong>
		{elseif $filter.logic == 'not_in'}
			{t}Exclude subscribers belonging to{/t} <strong>{$groups[$filter.field_id]}</strong>
		{elseif $filter.logic == 'is_equal'}
			{t escape=no 1="<strong>`$demos[$filter.field_id].name`</strong>" 2="<em>`$filter.value`</em>}Include subscribers who have %1 equal to %2{/t}
		{elseif $filter.logic == 'not_equal'}
			{t escape=no 1="<strong>`$demos[$filter.field_id].name`</strong>" 2="<em>`$filter.value`</em>}Exclude subscribers who have %1 equal to %2{/t}
		{elseif $filter.logic == 'is_more'}
			{t escape=no 1="<strong>`$demos[$filter.field_id].name`</strong>" 2="<em>`$filter.value`</em>}Include subscribers who have %1 greater than %2{/t}
		{elseif $filter.logic == 'is_less'}
			{t escape=no 1="<strong>`$demos[$filter.field_id].name`</strong>" 2="<em>`$filter.value`</em>}Include subscribers who have %1 less than %2{/t}
		{elseif $filter.logic == 'not_true'}
			{t}Exclude subscribers that checked{/t} <strong>{$demos[$filter.field_id].name}</strong>
		{elseif $filter.logic == 'is_true'}
			{t}Include subscribers that checked{/t} <strong>{$demos[$filter.field_id].name}</strong>
		{/if}
	</span>
</div>
{foreachelse}
 	<div><br><strong>{t}No filters have been assigned.{/t}</strong></div>
{/foreach}

</div>

<div>
<input type="checkbox" id="focusStealer" name="focusStealer">
</div>

{literal}
<script type="text/javascript">
// <![CDATA[

/* TODO - CREATE AN OBJECT OF THIS MESS */

function updateLogic() {
	poll = new Ajax.Updater(
		'critLogic',
		'ajax_filters.php',
		{
			asynchronous:true,
			parameters: "field_id="+$('field_id').value
		}
	);
	hide();
}

function updateGroup(curGroup) {
	poll = new Ajax.Updater(
		'critLogic',
		'ajax_filters.php',
		{
			asynchronous:true,
			parameters: "group_id="+curGroup+"&group_logic="+$('group_logic').value
		}
	);
	hide();
}

function filterUpdate(filter_id,group_id) {
	poll = new Ajax.Updater(
		'critLogic',
		'ajax_filters.php',
		{
			asynchronous:true,
			parameters: "update=TRUE&filter_id="+filter_id+"&group_id="+group_id
		}
	);
	hide();
}

function hide() {
	Effect.BlindUp('group');
	Effect.BlindUp('field');
	Effect.BlindUp('filters');
	Effect.Appear('critLogic');
	$('field_id').blur();
	$('group_logic').blur();
	$('focusStealer').focus();
}

function reset(val) {
	$('critLogic').innerHTML = "";
	$('group_logic').value = "";
	$('field_id').value = "";
	Effect.BlindDown('field');
	Effect.BlindDown('group');
	Effect.BlindDown('filters');
}

Effect.Fade('critLogic');
// ]]>
</script>
{/literal}


   
</div>
<!-- end mainbar -->

{include file="admin/inc.footer.tpl"}