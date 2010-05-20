{capture name=head}
{* used to inject content into the HTML <head> *}
{include file="inc/ui.dialog.tpl"}
{include file="inc/ui.grid.tpl"}
{include file="inc/ui.form.tpl"}
{/capture}

{include file="inc/admin.header.tpl" sidebar='off'}

<h2><img src="{$url.theme.shared}images/icons/examine.png" class="navimage left" alt="manage subscribers"/>{t}List / Manage Subscribers{/t}</h2>

<div class="buttons">
<a href="ajax/subscriber_add.php" title="{t}Add Subscribers{/t}" class="addTrigger positive"><img src="{$url.theme.shared}images/icons/tick.png" alt="text field button"/>{t}Add{/t}</a>
<a href="ajax/subscriber_del.php?status={$state.status}" title="{t}Remove Subscribers{/t}" class="delTrigger negative"><img src="{$url.theme.shared}images/icons/cross.png" alt=""/>{t}Remove{/t}</a>
<a href="ajax/subscriber_export.php" title="{t}Export Subscribers{/t}" class="expTrigger"><img src="{$url.theme.shared}images/icons/export.png" alt="export button"/>{t}Export{/t}</a>
</div>

<br class="clear"/>

<p>{include file="inc/messages.tpl"}</p>

<form method="post" action="" id="orderForm">

	<fieldset class="click">
	<h3 class="click">{t}View{/t} &raquo;</h3>
	<ul class="inpage_menu view">
	
		<li>
		<label>{t}View: {/t} 
		<select name="status">
		<option value="1"{if $state.status == 1} selected="selected"{/if}>{t}Active Subscribers{/t}</option>
		<option value="1">------------------</option>
		<option value="0"{if $state.status == 0} selected="selected"{/if}>{t}Unsubscribed{/t}</option>
		<option value="2"{if $state.status == 2} selected="selected"{/if}>{t}Pending{/t}</option>
		</select></label>
		</li>
		
		<li>
		<label>&nbsp;&nbsp;&nbsp;&nbsp;{t}Belonging to Group: {/t} 
		<select name="group">
		<option value="all"{if $state.group == 'all'} selected="selected"{/if}>{t}All Subscribers{/t}</option>
		<option value="all">---------------</option>
		{foreach from=$groups key=id item=g}
		<option value="{$id}"{if $state.group == $id} selected="selected"{/if}>{$g.name}</option>
		{/foreach}
		</select></label>
		</li>
		
	</ul>
	</fieldset>
</form>

<br class="clear"/>

<form method="post" action="" id="searchForm">
	
	<fieldset class="click">
	<h3 class="click">{t}Search{/t} &raquo;</h3>
	<ul class="inpage_menu search">
	
		<li>
		<label>{t}Find Subscribers where{/t}
		<select name="searchField">
		<option value="email"{if $state.search.field == 'email'} selected="selected"{/if}>{t}email{/t}</option>
		{foreach from=$fields key=id item=f}
		<option value="{$id}"{if $state.search.field == $id} selected="select"{/if}>{$f.name}</option>
		{/foreach}
		<option value="time_registered"{if $state.search.field == 'time_registered'} selected="selected"{/if}>{t}time registered{/t}</option>
		<option value="time_touched"{if $state.search.field == 'time_touched'} selected="selected"{/if}>{t}time last updated{/t}</option>
		<option value="ip"{if $state.search.field == 'ip'} selected="selected"{/if}>{t}IP Address{/t}</option>
		</select>
		</label>
		</li>	
	
		<li>
		<label>{t}is like{/t}
		<input type="text" name="searchString" value="{$state.search.string|escape}" />
		</label>
		</li>
		
		<div class="buttons">
		<button type="submit" name="submit" value="{t}Search{/t}"  class="positive"><img src="{$url.theme.shared}images/icons/examine_small.png" alt="search" />{t}Search{/t}</button>
		</div>
		
		{if !empty($state.search)}
        <div class="buttons">
		<button type="submit" name="searchClear" value="{t}Reset{/t}" /><img src="{$url.theme.shared}images/icons/reload-small.png" alt="reset" />{t}Reset{/t}</buttons>
        </div>
		{/if}
		
	</ul>
	</fieldset>
</form>

<br class="clear"/>

{if $tally > 0}
<table id="grid" class="scroll" cellpadding="0" cellspacing="0"></table>
<div id="gridPager" class="scroll" style="text-align:center;"></div>

<div class="buttons">
<a href="ajax/subscriber_edit.php" class="editTrigger positive"><img src="{$url.theme.shared}images/icons/edit_button.png" alt="edit button"/>{t}Edit Checked{/t}</a>
<a href="ajax/subscriber_del.php?status={$state.status}" class="delTrigger negative"><img src="{$url.theme.shared}images/icons/cross.png" alt="delete button"/>{t}Delete Checked{/t}</a>
</div>

<script type="text/javascript">
$().ready(function() {ldelim}	
	
	var p = {ldelim}	
	url: 'ajax/manage.list.php',
	colNames: [
		'ID',
		'Email',
		{foreach from=$fields key=id item=f}'{$f.name|escape}',{/foreach}
		'{t escape=js}Registered{/t}',
		'{t escape=js}Updated{/t}',
		'{t escape=js}IP Address{/t}'
	],
	colModel: [
		{ldelim}name: 'id', index: 'id', hidden: true, width: 1{rdelim},
		{ldelim}name: 'email', width: 200{rdelim},
		{foreach from=$fields key=id item=f}{ldelim}name: 'd{$id}', width: 100{rdelim},{/foreach}
		{literal}{name: 'registered', width: 130},
		{name: 'touched', width: 130},
		{name: 'ip', width: 90}
	]
	};
	
	poMMo.grid = PommoGrid.init('#grid',p);
});
</script>
{/literal}
{else}
<strong>{t}No records returned.{/t}</strong>
{/if}


{literal}
<script type="text/javascript">
$().ready(function() {
	
	// Setup Modal Dialogs
	PommoDialog.init();
	$('#add').jqmAddTrigger('a.addTrigger');
	$('#del').jqmAddTrigger('a.delTrigger');
	$('#exp').jqmAddTrigger('a.expTrigger');
	
	$('a.editTrigger').click(function(){
		// prevent edit window from appearing if no row is selected
		if(poMMo.grid.getRowID())
			$('#edit').jqmShow(this);
		return false;
	});
	
	
	$('#orderForm select').change(function() {
		$('#orderForm')[0].submit();
	});
	
	$('h3.click').click(function(){ 
		$(this).siblings('ul').slideToggle(); 
	});
	
	
	{/literal}
	{if !empty($state.search)}
		$('ul.search').slideDown();
	{/if}
	
	{if $state.group != 'all' || $state.status != 1}
		$('ul.view').slideDown('slow');
	{/if}
	
{rdelim});
</script>

{capture name=dialogs}
{include file="inc/dialog.tpl" id="add" wide=true tall=true}
{include file="inc/dialog.tpl" id="edit" wide=true tall=true}
{include file="inc/dialog.tpl" id="del" tall=true}
{include file="inc/dialog.tpl" id="exp" tall=true}
{/capture}

{include file="inc/admin.footer.tpl"}