{include file="admin/inc.header.tpl"}

</div>
<!-- end content -->

<div style="width:90%;">
	<span style="float: right; margin-right: 30px;">
		<a href="subscribers_manage.php">{t 1=$returnStr}Return to %1{/t}</a>
	</span>
</div>

<p style="clear: both;"></p>
<hr>


<form method="POST" action="">
	<input type="hidden" name="order" value="{$order}">
	<input type="hidden" name="orderType" value="{$orderType}">
	<input type="hidden" name="limit" value="{$limit}">
	<input type="hidden" name="table" value="{$table}">
	<input type="hidden" name="group_id" value="{$group_id}">
	<input type="hidden" name="action" value="{$action}">
	<input type="hidden" name="sid[]" value="{$sid}">

	{if $action == 'edit'}
		<table cellspacing="5" border="0" style="text-align:left;">
		<tr>
			<td nowrap>{t}email{/t}</td>
			
			{foreach from=$fields key=key item=item}
				<td nowrap>{$item.name}</td>
			{/foreach}
		
		</tr>
		
		
		{foreach name=sub from=$subscribers key=key item=item}
		<tr>
			
			<input type="hidden" name="editId[]" value="{$key}">
			<input type="hidden" name="date[{$key}]" value="{$item.date}">
			<input type="hidden" name="oldEmail[{$key}]" value="{$item.email}">
			
			<td nowrap>
				<input type="text" name="email[{$key}]" value="{$item.email}" maxlength="60">
			</td>
			
			{foreach name=demo from=$fields key=demo_id item=demo}
				<td nowrap>
				{if $demo.type == 'text'}
					<input type="text" name="d[{$key}][{$demo_id}]" maxlength="60" value="{$item.data.$demo_id}">
				{elseif $demo.type == 'checkbox'}
					<input type="checkbox" name="d[{$key}][{$demo_id}]"{if $item.data.$demo_id == 'on'} checked{/if}>
				{elseif $demo.type == 'multiple'}
					<select name="d[{$key}][{$demo_id}]">
						{foreach name=option from=$demo.options item=option}
						<option{if $item.data.$demo_id == $option} SELECTED{/if}>{$option}</option>
						{/foreach}
				{else}
	   				{t}Unsupported Field Type.{/t}
	   			{/if}
				</td>
			{/foreach}
		</tr>
		{/foreach}
		</table>
		
		<br>
		<input type="submit" name="submit" value="{t}Update{/t}">
		<br>
	
	{elseif $action == 'delete'}
	
		{t}The following will be deleted{/t}
		
		<div style="float: right; width: 50%;">
			<input type="submit" name="submit" value="{t}Click to Delete{/t}">
		</div>
		<ul>
		{foreach from=$emails item=email}
			<input type="hidden" name="deleteEmails[]" value="{$email}">
			<li>{$email}</li>	
		{/foreach}
		</ul>
	
	{elseif $action == 'add'}
	
		{t}The following will be added as subscribers{/t}
		
		<span style="float: right; width: 50%;">
			<input type="submit" name="submit" value="{t}Click to Add{/t}">
		</span>
		<ul>
		{foreach from=$emails item=email}
			<input type="hidden" name="addEmails[]" value="{$email}">
			<li>{$email}</li>	
		{/foreach}
		</ul>
	
	{/if}
</form>

	
{include file="admin/inc.footer.tpl"}