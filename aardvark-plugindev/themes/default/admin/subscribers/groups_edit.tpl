{capture name=head}{* used to inject content into the HTML <head> *}
<script type="text/javascript" src="{$url.theme.shared}js/jq/jquery.js"></script>
<script type="text/javascript" src="{$url.theme.shared}js/jq/interface.js"></script>
<script type="text/javascript" src="{$url.theme.shared}js/validate.js"></script>
<script type="text/javascript" src="{$url.theme.shared}js/jq/interface.js"></script>
<link type="text/css" rel="stylesheet" href="{$url.theme.shared}css/modal.css" />
<link type="text/css" rel="stylesheet" href="{$url.theme.shared}css/table.css" />
{/capture}
{include file="inc/admin.header.tpl"}

<ul class="inpage_menu">
<li><a href="{$url.base}admin/subscribers/subscribers_groups.php">{t 1=$returnStr}Return to %1{/t}</a></li>
</ul>

<h2>{t}Edit Group{/t}</h2>

<p>
<img src="{$url.theme.shared}images/icons/groups.png" alt="groups icon" class="navimage right" />
{t escape=no 1="<a href=\"`$url.base`admin/setup/setup_fields.php\">" 2="</a>"}To add subscribers to a group you must create matching rules. Subscribers are automatically added to a group if their %1subscriber field%2 values "match" a Group's rules. For example, if you collect "AGE" and "COUNTRY" as %1subscriber fields%2, you can match those who are 21+ and living in Japan by creating two rules; one which matches "AGE" to greater than 20, and another which matches "Japan" to "COUNTRY". Including or excluding members of other groups is possible.{/t}
</p>

{include file="inc/messages.tpl"}

<form method="post" action="" id="nameForm" name="nameForm">
<fieldset>
<legend>{t}Change Name{/t}</legend>

<div>
<label for="group_name">{t}Group name:{/t}</label> <input type="text" title="{t}type new group name{/t}" maxlength="60" size="30" name="group_name" id="group_name"  value="{$group.name|escape}" />
<input type="submit" name="rename" value="{t}Rename{/t}" />
</div>

</fieldset>
</form>

<form method="post" action="" id="filterForm" name="filterForm">
<fieldset>
<legend>{t}Add Rule{/t}</legend>

<div id="newFilter">

{* filterWindow popup *}
<div id="filterWindow">
<a href="#" class="fwClose" alt="field_id">{t}Close window{/t}</a>
<div class="fwContent"></div>
</div>

<div>
<label for="field">{t escape=no 1="<strong><a href=\"`$url.base`admin/setup/setup_fields.php\">" 2="</a></strong>"}Select a %1 field %2 to filter{/t}</label>
<select name="field" id="field" alt="{$group.id}">
<option value="">-- {t}Choose Subscriber Field{/t} --</option>
{foreach from=$new key=id item=name}
<option value="{$id}">{$fields[$id].name}</option>
{/foreach}
</select>
</div>

<div>
<label for="group">{t escape=no 1="<strong><a href=\"`$url.base`admin/subscribers/subscribers_groups.php\">" 2="</a></strong>"}or, Select a %1 group %2 to include or exclude{/t}</label>
<select name="group" id="group" alt="{$group.id}">
<option value="">-- {t}Choose Group{/t} --</option>
{foreach from=$gnew key=id item=name}
<option value="{$id}">{$name}</option>
{/foreach}
</select>
</div>

</div>
</fieldset>

{* **** DISPLAY GROUP RULES **** *}

<fieldset>
<legend>{t}Group Rules{/t}</legend>

<table>
<thead>
<tr>
<th>{t}Delete{/t}</th>
<th>{t}Edit{/t}</th>
<th>{t}Field{/t}</th>
<th>{t}Logic{/t}</th>
<th>{t}Value{/t}</th>
<th>{t}Type{/t}</th>
</tr>
</thead>
<tbody>
<tr class="alert">{* **** "AND" GROUP RULES **** *}
<td colspan="6" style="padding: 5px;">
	<center>
	{t}"AND" RULES{/t} <br />
	{t escape=no 1='<strong>' 2='</strong>'}MATCH %1ALL%2 OF THE FOLLOWING{/t}
	</center>
</td>
</tr>

{foreach from=$rules.and key=field_id item=rule}
{foreach from=$rule key=logic_id item=values}
<tr class="{cycle values="r1,r2,r3"}">

<td><a href="{$getURL}&delete={$field_id|escape}&logic={$logic_id|escape}"><img src="{$url.theme.shared}images/icons/delete.png" alt="{t}Delete{/t}" /></a></td>

<td>
{if $logic_id != 'true' && $logic_id != 'false'}{* DO NOT ALLOW EDITING OF CHECKBOXES *}
<a href="#" onclick="fwAjaxCall({$field_id},'field',{$group.id},'{$logic_id}','and'); return false;"><img src="{$url.theme.shared}images/icons/edit.png" alt="{t}Edit{/t}" /></a>
{/if}
</td>

<td>{$fields[$field_id].name}</td>

<td>{$logicNames[$logic_id]}</td>

<td>
<ul>
{foreach from=$values item=v name=vals}
{if $v}{if !$smarty.foreach.vals.first}<br />({t}or{/t}) {/if} {$v}{/if}
{/foreach}
</ul>
</td>

<td>
<select name="type" onChange="_redirect('&toggle={$field_id|escape}&logic={$logic_id|escape}&type=or');">
<option selected>{t}AND{/t}</option>
<option>{t}OR{/t}</option>
</select>
</td>

</tr>
{/foreach}
{foreachelse}
<tr class="r1"><td colspan="5">{t}No rules have been added{/t}</td></tr>
{/foreach}

{cycle reset=true print=false advance=false}
<tr class="alert">{* **** "OR" GROUP RULES **** *}
<td colspan="6" style="padding: 5px; position: relative;">
	<center>
	{t}"OR" RULES{/t} <br />
	{t escape=no 1='<strong>' 2='</strong>'}OR, MATCH %1ANY%2 OF THE FOLLOWING{/t}
	</center>
</td>
</tr>

{foreach from=$rules.or key=field_id item=rule}
{foreach from=$rule key=logic_id item=values}
<tr class="{cycle values="r1,r2,r3"}">

<td><a href="{$getURL}&delete={$field_id|escape}&logic={$logic_id|escape}"><img src="{$url.theme.shared}images/icons/delete.png" alt="{t}Delete{/t}" /></a></td>

<td>
{if $logic_id != 'true' && $logic_id != 'false'}{* DO NOT ALLOW EDITING OF CHECKBOXES *}
<a href="#" onclick="fwAjaxCall({$field_id},'field',{$group.id},'{$logic_id}','or'); return false;"><img src="{$url.theme.shared}images/icons/edit.png" alt="{t}Edit{/t}" /></a>
{/if}
</td>

<td>{$fields[$field_id].name}</td>

<td>{$logicNames[$logic_id]}</td>

<td>
<ul>
{foreach from=$values item=v name=vals}
{if $v}{if !$smarty.foreach.vals.first}<br />({t}or{/t}) {/if} {$v}{/if}
{/foreach}
</ul>
</td>

<td>
<select name="type" onChange="_redirect('&toggle={$field_id|escape}&logic={$logic_id|escape}&type=and');">
<option>{t}AND{/t}</option>
<option selected=true>{t}OR{/t}</option>
</select>
</td>

</tr>
{/foreach}
{foreachelse}
<tr class="r1"><td colspan="5">{t}No rules have been added{/t}</td></tr>
{/foreach}

{cycle reset=true print=false advance=false}
<tr class="alert">{* **** GROUP IN/EX CLUSIONS **** *}
<td colspan="6" style="padding: 5px;">
	<center>
	{t 1=$t_include}AND, ADD OR SUBTRACT MEMBERS OF OTHER GROUPS{/t} <br />
	</center>
</td>
</tr>

{foreach from=$rules.include key=field_id item=rule}
{foreach from=$rule key=logic_id item=values}
<tr class="{cycle values="r1,r2,r3"}">

<td colspan="2"><a href="{$getURL}&delete={$field_id|escape}&logic=is_in"><img src="{$url.theme.shared}images/icons/delete.png" alt="{t}Delete{/t}" /></a></td>

<td colspan="4">{t escape=no 1=<strong> 2=</strong> 3=$values}%1Add%2 members matching %3{/t}</td>

</tr>
{/foreach}
{foreachelse}
{/foreach}

{foreach from=$rules.exclude key=field_id item=rule}
{foreach from=$rule key=logic_id item=values}
<tr class="{cycle values="r1,r2,r3"}">

{debug}

<td colspan="2"><a href="{$getURL}&delete={$field_id|escape}&logic=not_in"><img src="{$url.theme.shared}images/icons/delete.png" alt="{t}Delete{/t}" /></a></td>

<td colspan="4">{t escape=no 1=<strong> 2=</strong> 3=$values}%1Subtract%2 members matching %3{/t}</td>

</tr>
{/foreach}
{/foreach}

</table>

</fieldset>
</form>

<p>{t escape=no 1="<em>`$ruleCount`</em>" 2="<strong>`$tally`</strong>"}%1 rules match a total of %2 active subscribers{/t}</p>

{literal}
<script type="text/javascript">
function _redirect(url) {
	window.location.href = "{/literal}{$getURL}{literal}"+url;
}

function fwAjaxCall(id, name, gid, l, t) {
	$('#newFilter select').each(function() { $(this).hide(); });
	
	var _t = (typeof(t) == 'undefined') ? 'and' : t;
	
	var p = (typeof(l) == 'undefined') ?
		{ID: id, add: name, group: gid, type: _t} :
		{ID: id, add: name, group: gid, logic: l, type: _t};

	$('#filterWindow div.fwContent').load(
		'ajax/group_edit.php',
		p,
		function() {
			$('#filterWindow a.fwClose').attr("alt",name);
			$('#'+name).TransferTo({to:'filterWindow',className:'fwTransfer', duration: 300, complete:function(to){$(to).fadeIn(200)}});
			PommoValidate.reset();
			PommoValidate.init('#fwValue input[@name=v]', '#fwSubmit', false);
		}
	);
	return false; // don't follow href
}

$().ready(function(){ 

	$('#filterWindow a.fwClose').click(function() {
		var name = $(this).attr('alt');
		$('#newFilter select').each(function() { $(this).show().val(''); });
		$('#filterWindow').fadeOut(200, function(){$(this).TransferTo({to: name,className:'fwTransfer', duration: 300})});
		return false;
	});

	$('#newFilter select').change(function() {
		var name = $(this).name(); // "field" or "group"
		var id = $(this).val(); // group or field ID
		var gid = $(this).attr("alt"); // this group's ID
		if(id != '') 
			fwAjaxCall(id, name, gid);
	});
});
</script>
{/literal}

{include file="inc/admin.footer.tpl"}