{include file="admin/inc.header.tpl"}

</div>
<!-- end content -->

<div style="width:90%;">
	<span style="float: right;">
		{if $table == 'subscribers'}
			<a href="subscribers_manage.php?table=pending">{t}View Pending{/t}</a>
		{else}
			<a href="subscribers_manage.php?table=subscribers">{t}View Subscribed{/t}</a>
		{/if}
	</span>
	<span style="float: right; margin-right: 30px;">
		<a href="subscribers_export.php?table={$table}&group_id={$group_id}">{t}Export to CSV{/t}</a> 
	</span>
	<span style="float: right; margin-right: 30px;">
		<a href="admin_subscribers.php">{t 1=$returnStr}Return to %1{/t}</a>
	</span>
</div>

<p style="clear: both;"></p>
<hr>

<div style="text-align: center; width: 100%;" >
	
	<form name="bForm" id="bForm" method="POST" action="">
	{t}Subscribers per Page:{/t} 
		<SELECT name="limit" onChange="document.bForm.submit()">
			<option value="10"{if $limit == '10'} SELECTED{/if}>10</option>
			<option value="50"{if $limit == '50'} SELECTED{/if}>50</option>
			<option value="150"{if $limit == '150'} SELECTED{/if}>150</option>
			<option value="300"{if $limit == '300'} SELECTED{/if}>300</option>
			<option value="500"{if $limit == '500'} SELECTED{/if}>500</option>
		</SELECT>
	
	<span style="width: 30px;"></span>
	
	{t}Belonging to Group:{/t} 
		<SELECT name="group_id" onChange="document.bForm.submit()">
			<option value=all>{t}All Subscribers{/t}</option>
			{foreach from=$groups key=key item=item}
				<option value="{$key}"{if $group_id == $key} SELECTED{/if}>{$item}</option>
			{/foreach}
		</SELECT>
	
	<span style="width: 30px;"></span>
	
	{t}Order by:{/t}
		<SELECT name="order" onChange="document.bForm.submit()">
			<option value="email">{t}email{/t}</option>
			{foreach from=$fields key=key item=item}
				<option value="{$key}"{if $order == $key} SELECTED{/if}>{$item.name}</option>
			{/foreach}
		</SELECT>
	
	<span style="width: 15px;"></span>
	
	<SELECT name="orderType" onChange="document.bForm.submit()">
		<option value="ASC"{if $orderType == 'ASC'} SELECTED{/if}>{t}ascending{/t}</option>
		<option value="DESC"{if $orderType == 'DESC'} SELECTED{/if}>{t}descending{/t}</option>
	</SELECT>
	
	</form>

<br><br>

(<em>{t 1=$groupCount}%1 subscribers{/t}</em>)

<form name="oForm" id="oForm" method="POST" action="subscribers_mod.php">
	<input type="hidden" name="order" value="{$order}">
	<input type="hidden" name="orderType" value="{$orderType}">
	<input type="hidden" name="limit" value="{$limit}">
	<input type="hidden" name="table" value="{$table}">
	<input type="hidden" name="group_id" value="{$group_id}">

<table cellspacing="5" border="0" style="text-align:left;">
	<tr>
		<td nowrap>
			{t}select{/t}
		</td>
		<td nowrap>
			{if $table == 'subscribers'}
				{t}edit{/t}
			{else}
				{t}add{/t}
			{/if}
		</td>
		<td>
			{t}delete{/t}
		</td>
		<td nowrap>
			{t}email{/t}
		</td>
		
		{foreach from=$fields key=key item=item}
			<td nowrap>{$item.name}</td>
		{/foreach}
		
	</tr>
	
		
	{foreach name=sub from=$subscribers key=key item=item}
	<tr>
		<td nowrap><input type="checkbox" name="sid[]" value="{$item.email}"></td>
		<td nowrap>
			{if $table == 'subscribers'}
				<a href="subscribers_mod.php?sid={$item.email}&action=edit&table={$table}&limit={$limit}&order={$order}&orderType={$orderType}&group_id={$group_id}">{t}edit{/t}</a>
			{else}
				<a href="subscribers_mod.php?sid={$item.email}&action=add&table={$table}&limit={$limit}&order={$order}&orderType={$orderType}&group_id={$group_id}">{t}add{/t}</a>
			{/if}
				</td>
		<td nowrap><a href="subscribers_mod.php?sid={$item.email}&action=delete&table={$table}&limit={$limit}&order={$order}&orderType={$orderType}&group_id={$group_id}">{t}delete{/t}</a></td>
		<td nowrap><strong>{$item.email}</strong></td>
		{foreach name=demo from=$fields key=demo_id item=demo}
			<td nowrap>{$item.data.$demo_id}</td>
		{/foreach}
		<td nowrap>{$item.date}</td>
	</tr>
	{/foreach}
	
	<tr>
		<td colspan="4">
			<b><a href="javascript:SetChecked(1,'sid[]')">{t}Check All{/t}</a> 
			&nbsp;&nbsp; || &nbsp;&nbsp; 
			<a href="javascript:SetChecked(0,'sid[]')">{t}Clear All{/t}</a></b>
		</td>
	</tr>

</table>

<SELECT name="action">
	<option value="" SELECTED>{t}Ignore{/t} {t}checked subscribers{/t}</option>
	<option value="delete">{t}Delete{/t} {t}checked subscribers{/t}</option>
	{if $table == 'subscribers'}
		<option value="edit">{t}Edit{/t} {t}checked subscribers{/t}</option>
	{else}
		<option value="add">{t}Add{/t} {t}checked subscribers{/t}</option>
	{/if}
</SELECT>

&nbsp;&nbsp;&nbsp; 
<input type="submit" name="send" value="{t}go{/t}">

</form>

<br><br>

{$pagelist}

</div>

</table>


{literal}
<script type="text/javascript">
// <![CDATA[

/* The following code is to "check all/check none" NOTE: form name must properly be set */
var form='oForm' //Give the form name here
function SetChecked(val,chkName) {
	dml=document.forms[form];
	len = dml.elements.length;
	var i=0;
	for( i=0 ; i<len ; i++) {
		if (dml.elements[i].name==chkName) {
			dml.elements[i].checked=val;
		}
	}
}
// ]]>
</script>
{/literal}

{include file="admin/inc.footer.tpl"}