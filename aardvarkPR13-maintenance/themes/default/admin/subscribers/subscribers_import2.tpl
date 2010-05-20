{include file="admin/inc.header.tpl"}

</div>
<!-- wide layout -->

{literal}
<style>
.bg1 {
	background-color: #b7cfec;
}

.bg2 {
	background-color: #87addc;
}
</style>
{/literal}


<div style="width:90%;">
	<span style="float: right; margin-right: 30px;">
		<a href="admin_subscribers.php">{t 1=$returnStr}Return to %1{/t}</a>
	</span>
	<span style="float: right; margin-right: 30px;">
		<a href="subscribers_import.php">{t}Upload a different file{/t}</a> 
	</span>
</div>

<p style="clear: both;"></p>
<hr>

<div align="center">
	
{if $page == 'preview'}
	
	<div style="width: 60%">
		<h2>{t}Preview Import{/t}</h2>
		<br>
		{t escape=no 1="<strong>`$totalImported`</strong>" 2="<strong>`$totalImported`</strong>"}%1 Subscribers will be imported. Of these, %2 will be flagged for update due to invalidity.{/t}
		<br>
		{t escape=no 1="<strong>`$totalDuplicate`</strong>"}%1 Duplicate{/t}
		
		{include file="admin/confirm.tpl"}
		
		{if $messages}
    	<div class="msgdisplay" style="text-align: left;">
    	{foreach from=$messages item=msg}
    	<div>* {$msg}</div>
    	{/foreach}
    	</div>
 		{/if}
		
	</div>

{elseif $page == 'import'}

	<div style="width: 60%">
		<h2>{t}Import Complete!{/t}</h2>
		<br>
		
		<a href="{$url.base}admin/subscribers/admin_subscribers.php">
		<img src="{$url.theme.shared}images/icons/back.png" align="middle" class="navimage" border='0'>
		{t 1=$returnStr}Return to %1{/t}</a>
	
{elseif $page == 'assign'}
	<div style="width: 60%">
		<h2>{t}Upload Success{/t}</h2>
		<br>
		{t escape=no 1='<strong>' 2='</strong>'}Optionally, you may match the values to a subscriber field. If an imported subscriber is missing a value for a required field, they will be %1 flagged %2 to update their information.{/t}
	</div>
	
	<form method="POST" action="">
	
		<br>
		<table cellspacing="0" cellpadding="7">
			<tr>
				<td></td>
			{section name="fieldloop" start=1 loop=$numFields}
				<td class="{cycle values="bg1,bg2"}">
					{t 1=$smarty.section.fieldloop.index}Field #%1{/t}
				</td>
			{/section}
			</tr>
				<td>
					line #
				</td>
			{section name="field" start=0 loop=$numFields}
				<td class="{cycle values="bg1,bg2"}">
					{if $smarty.section.field.index == $emailField}
					<i>email</i><input type="hidden" name="field[{$smarty.section.field.index}]" value="email">
					{else}
					<SELECT name="field[{$smarty.section.field.index}]">
						<option value="ignore">{t}Ignore Field{/t}</option>
						<option value="ignore">----------------</option>
					{foreach from=$fields key=key item=item}
						<option value="{$key}">{$item.name}</option>
					{/foreach}
					</SELECT>
					{/if}
				</td>
			{/section}
			</tr>
			{* output from file now... use data from lineWithMostFields *}
			<tr>
				<td style="border-right: thin dotted #000000;">
					{$csvArray.lineWithMostFields+1}
				</td>
				{section name="field" start=0 loop=$numFields}
					<td style="border-right: thin dotted #000000;">
						{$entry[$smarty.section.field.index]}
					</td>
				{/section}
			</tr>
			<tr style="height: 2px;">
				<td style="border-right: thin dotted #000000; height: 2px;"></td>
				<td colspan="*" bgcolor="#000000" style="height: 2px;"></td>
			</tr>
		</table>
	
		<br>
		
		{t 1=$csvArray.csvFile|@count}%1 subscribers to import.{/t}
		<br>
		<img src="{$url.theme.shared}images/icons/download.png"><br>
		<input type="submit" name="preview" value="{t}Click to Preview{/t}">

	</form>
{/if}


</div>
{include file="admin/inc.footer.tpl"}