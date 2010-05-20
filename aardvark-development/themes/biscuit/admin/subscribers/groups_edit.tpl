{capture name=head}
<link type="text/css" rel="stylesheet" href="{$url.theme.shared}css/table.css" />
{include file="inc/ui.dialog.tpl"}
{include file="inc/ui.form.tpl"}
{/capture}
{include file="inc/admin.header.tpl"}

<h2><img src="{$url.theme.shared}images/icons/groups.png" alt="groups icon" class="navimage left" />{t}Edit Group{/t}&nbsp;&nbsp;&nbsp;<span style="font-size: 12px;"><a href="{$url.base}admin/subscribers/subscribers_groups.php">{t 1=$returnStr}(Return to %1){/t}</a></span></h2>


{t escape=no 1="<a href=\"`$url.base`admin/setup/setup_fields.php\">" 2="</a>"}To add subscribers to a group you must create matching rules. Subscribers are automatically added to a group if their %1subscriber field%2 values "match" a Group's rules. <a href="{$url.base}themes/default/admin/subscribers/examples.htm" onclick="return popitup('{$url.base}themes/default/admin/subscribers/examples.htm')"	>See examples</a>{/t}

<br class="clear"/>

{include file="inc/messages.tpl"}

<form class="json validate" action="ajax/group.rpc.php?call=renameGroup" method="post">
	
	<fieldset>
	<h3>{t}Change Name{/t}</h3>
    
	<label for="group_name">{t}Group Name: {/t}</label> <input class="pvEmpty" type="text" title="{t}type new group name{/t}" maxlength="60" size="20" name="group_name" id="group_name" value="{$group.name|escape}"  />
    
    <div class="buttons">
	<button type="submit" name="rename" value="{t}Rename{/t}" class="positive"><img src="{$url.theme.shared}/images/icons/tick.png" alt="rename"/>{t}Rename{/t}</button>
    </div>
    
    <br class="clear"/>
	<div class="output"></div>
    
	</fieldset>
</form>

<br class="clear"/>

<form action="" id="addRule" method="post">
<fieldset>
<h3>{t}Add Rule{/t}</h3>

<div>
<label for="field">{t escape=no 1="<strong><a href=\"`$url.base`admin/setup/setup_fields.php\">" 2="</a></strong>"}Select a %1 field %2 to filter: {/t}</label>
<select name="field">
<option value="">-- {t}Choose Subscriber Field{/t} --</option>
{foreach from=$legalFieldIDs key=id item=name}<option value="{$id}">{$fields[$id].name}</option>{/foreach}
</select>
</div>

<div>
<label for="group">{t escape=no 1="<strong><a href=\"`$url.base`admin/subscribers/subscribers_groups.php\">" 2="</a></strong>"}or, Select a %1 group %2 to include or exclude: {/t}</label>
<select name="group">
<option value="">-- {t}Choose Group{/t} --</option>
{foreach from=$legalGroups key=id item=name}<option value="{$id}">{$name}</option>{/foreach}
</select>
</div>

</fieldset>
</form>

<br class="clear"/>

{* **** DISPLAY GROUP RULES **** *}
{cycle reset=true print=false advance=false values="r1,r2,r3"}

<form id="rules" class="json" action="ajax/group.rpc.php?call=updateRule" method="post">
<input type="hidden" name="fieldID" value=''>
<input type="hidden" name="logic" value=''>
<input type="hidden" name="type" value=''>
<input type="hidden" name="request" value=''>

<fieldset>
<h3>{t}Group Rules{/t}</h3>

<table width="100%">
  <!--DWLayoutTable-->
<thead>
<tr>
<th width="75">{t}Delete{/t}</th>
<th width="75">{t}Edit{/t}</th>
<th width="350">{t}Field{/t}</th>
<th width="300">{t}Logic{/t}</th>
<th width="605">{t}Value{/t}</th>
<th width="100">{t}Type{/t}</th>
</tr>
</thead>
<tbody>
<tr class="alert">{* **** "AND" GROUP RULES **** *}
<td colspan="6" style="padding: 5px;">
	<center>
	{t escape=no 1='<strong>' 2='</strong>'}"AND" match %1all%2 of the following{/t}
	</center></td>
</tr>

{foreach from=$rules.and key=field_id item=rule}
{foreach from=$rule key=logic_id item=values}
<tr class="{cycle values="r1,r2,r3"}">
<td>
<a href="#"><img src="{$url.theme.shared}images/icons/delete.png" alt="{t}Delete{/t}" onClick="poMMo.callback.updateRule({ldelim}fieldID:'{$field_id|escape}',logic:'{$logic_id|escape}',request:'delete'{rdelim});" /></a></td>
<td>
{if $logic_id != 'true' && $logic_id != 'false'}{* DO NOT ALLOW EDITING OF CHECKBOXES *}
<img src="{$url.theme.shared}images/icons/edit.png" alt="{t}Edit{/t}" onClick="poMMo.callback.editRule({ldelim}fieldID:'{$field_id|escape}',logic:'{$logic_id|escape}', type: 'and'{rdelim});" />
{/if}</td>

<td>{$fields[$field_id].name}</td>

<td>{$logicNames[$logic_id]}</td>

<td>
<ul>
{foreach from=$values item=v name=vals}
{if $v}
	{if !$smarty.foreach.vals.first}<br />({t}or{/t}){/if}
	
	{if $fields[$field_id].type == 'date'}
	{$v|pommoDateFormat}
	{else}
	{$v}
	{/if}
	
{/if}
{/foreach}
</ul></td>

<td>
<select onChange="poMMo.callback.updateRule({ldelim}fieldID:'{$field_id|escape}',logic:'{$logic_id|escape}',type:'or',request:'update'{rdelim});">
<option selected>{t}AND{/t}</option>
<option>{t}OR{/t}</option>
</select></td>
</tr>
{/foreach}
{foreachelse}
<tr class="r1"><td height="18" colspan="6" valign="top"><center>{t}No rules have been added{/t}</center></td>
</tr>
{/foreach}

{cycle reset=true print=false advance=false}
<tr class="alert">{* **** "OR" GROUP RULES **** *}
<td colspan="6" style="padding: 5px; position: relative;">
	<center>
	{t escape=no 1='<strong>' 2='</strong>'}"OR", match %1any%2 of the following{/t}
	</center></td>
</tr>

{foreach from=$rules.or key=field_id item=rule}
{foreach from=$rule key=logic_id item=values}
<tr class="{cycle values="r1,r2,r3"}">

<td>
<a href="#"><img src="{$url.theme.shared}images/icons/delete.png" alt="{t}Delete{/t}" onClick="poMMo.callback.updateRule({ldelim}fieldID:'{$field_id|escape}',logic:'{$logic_id|escape}',request:'delete'{rdelim});" /></a></td>

<td>
{if $logic_id != 'true' && $logic_id != 'false'}{* DO NOT ALLOW EDITING OF CHECKBOXES *}
<img src="{$url.theme.shared}images/icons/edit.png" alt="{t}Edit{/t}" onClick="poMMo.callback.editRule({ldelim}fieldID:'{$field_id|escape}',logic:'{$logic_id|escape}', type: 'or'{rdelim});" />
{/if}</td>

<td>{$fields[$field_id].name}</td>

<td>{$logicNames[$logic_id]}</td>

<td>
<ul>
{foreach from=$values item=v name=vals}
{if $v}{if !$smarty.foreach.vals.first}<br />({t}or{/t}) {/if} {$v}{/if}
{/foreach}
</ul></td>

<td>
<select onChange="poMMo.callback.updateRule({ldelim}fieldID:'{$field_id|escape}',logic:'{$logic_id|escape}',type:'and',request:'update'{rdelim});">
<option>{t}AND{/t}</option>
<option selected=true>{t}OR{/t}</option>
</select></td>
</tr>
{/foreach}
{foreachelse}
<tr class="r1"><td height="18" colspan="6" valign="top"><center>{t}No rules have been added{/t}</center></td>
</tr>
{/foreach}

{cycle reset=true print=false advance=false}
<tr class="alert">{* **** GROUP IN/EX CLUSIONS **** *}
<td colspan="6" style="padding: 5px;">
	<center>
	{t escape=no 1=$t_include}"INCLUDE or EXCLUDE" the members in other groups{/t}
	</center></td>
</tr>

{foreach from=$rules.include key=field_id item=rule}
{foreach from=$rule key=logic_id item=values}
<tr class="{cycle values="r1,r2,r3"}">

<td colspan="2">
<a href="#"><img src="{$url.theme.shared}images/icons/delete.png" alt="{t}Delete{/t}" onClick="poMMo.callback.updateRule({ldelim}fieldID:'{$field_id|escape}',logic:'is_in',request:'delete'{rdelim});" /></a></td>

<td colspan="4">{t escape=no 1=<strong> 2=</strong> 3=$values}%1Include%2 members matching %3{/t}</td>
</tr>
{/foreach}
{foreachelse}
<tr class="r1"><td height="18" colspan="6" valign="top"><center>{t}No rules have been added{/t}</center></td>
</tr>
{/foreach}

{foreach from=$rules.exclude key=field_id item=rule}
{foreach from=$rule key=logic_id item=values}
<tr class="{cycle values="r1,r2,r3"}">

<td colspan="2">
<a href="#"><img src="{$url.theme.shared}images/icons/delete.png" alt="{t}Delete{/t}" onClick="poMMo.callback.updateRule({ldelim}fieldID:'{$field_id|escape}',logic:'not_in',request:'delete'{rdelim});" /></a></td>

<td colspan="4">{t escape=no 1=<strong> 2=</strong> 3=$values}%1Exclude%2 members matching %3{/t}</td>
</tr>
{/foreach}
{/foreach}
</table>

</fieldset>
</form>

<p>{t escape=no 1="<em>`$ruleCount`</em>" 2="<strong>`$tally`</strong>"}%1 rules match a total of %2 active subscribers{/t}</p>
<!--{t escape=no 1='<strong>' 2='</strong>'}If the Type column is set to &quot;AND&quot;, then %1all%2 criteria will be matched.{/t}<br />
{t escape=no 1='<strong>' 2='</strong>'}If the Type column is set to &quot;OR&quot;, then  = %1or%2, match %1any%2 of the following{/t}-->

{literal}
  <script type="text/javascript">
$().ready(function(){
	// assign ajax + json forms
	poMMo.form.assign();
	
	// Setup Modal Dialogs
	PommoDialog.init('#dialog',{modal: true});

	$('#addRule select').change(function(){
		var type = this.name, fieldID = $(this).val();
		if($.trim(fieldID) != '')
			$('#dialog')
				.jqm({ajax: 'ajax/group.rpc.php?call=displayRule&ruleType='+type+'&fieldID='+fieldID})
				.jqmShow();
	});
	
});

poMMo.callback.updateRule = function(p) {
	$('#rules input[@name=fieldID]').val(p.fieldID);
	$('#rules input[@name=logic]').val(p.logic);
	$('#rules input[@name=type]').val(p.type);
	$('#rules input[@name=request]').val(p.request);
	
	poMMo.callback.pause();
	$('#rules').submit();
	return false;
};

poMMo.callback.editRule = function(p) {
	console.log(p.logic);
	$('#dialog')
		.jqm({ajax: 'ajax/group.rpc.php?call=displayRule&ruleType=field&fieldID='+p.fieldID+'&logic='+p.logic+'&type='+p.type})
		.jqmShow();	
	return false;
};


  </script>

  <script language="javascript" type="text/javascript">
<!--
function popitup(url) {
	newwindow=window.open(url,'name','height=500,width=500');
	if (window.focus) {newwindow.focus()}
	return false;
}

// -->
  </script>
  {/literal}

{capture name=dialogs}
{include file="inc/dialog.tpl" id=dialog wide=true}
{/capture}

{include file="inc/admin.footer.tpl"}
