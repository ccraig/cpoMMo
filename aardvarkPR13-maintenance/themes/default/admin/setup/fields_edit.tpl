{include file="admin/inc.header.tpl"}
{include file="admin/inc.sidebar.tpl"}

<div id="mainbar">

<h1>{t}Edit Field{/t}</h1>

<img src="{$url.theme.shared}images/icons/fields.png" class="articleimg">

{if $intro}<p>{$intro}</p>{/if}

<a href="{$url.base}admin/setup/setup_fields.php">
		<img src="{$url.theme.shared}images/icons/back.png" align="middle" class="navimage" border='0'>
		{t}Return to Fields Page{/t}</a>
		<h2>{$field_name} &raquo;</h2>
  
{if $messages}
    <div class="msgdisplay">
    {foreach from=$messages item=msg}
   	 <div>* {$msg}</div>
    {/foreach}
    </div>
 {/if}
 
 <form action="" method="POST">
 <input type="hidden" name="field_id" value="{$field.id}">
  
<fieldset>
    <legend>Parameters</legend>

	<div class="field">
		<div class="error">{validate id="field_name" message=$formError.field_name}</div>
		<label for="field_name"><span class="required">{t}Short Name:{/t} </span></label>
		<input type="text" class="text" maxlength="60" size="32"
		  name="field_name" value="{$field_name|escape}" id="field_name" />
		<div class="notes">{t}Identifying name. NOT displayed on Subscription Form or seen by users.{/t}</div>
	</div>
	
	<br />
		
	<div class="field">
		<div class="error">{validate id="field_prompt" message=$formError.field_prompt}</div>
		<label for="field_prompt"><span class="required">{t}Form Name:{/t} </span></label>
		<input type="text" class="text" maxlength="60" size="32"
		  name="field_prompt" value="{$field_prompt|escape}" id="field_prompt" />
		<div class="notes">{t}Prompt for field on the Subscription Form. e.g. 'Type your city'{/t}</div>
	</div>
	
	<br />

	<div class="field">
		<label for="field_required">{t}Required:{/t} </label>
		<input type="radio" name="field_required" value="on" {if $field_required == 'on'}checked{/if}>on
		<input type="radio" name="field_required" value="off" {if $field_required != 'on'}checked{/if}>off
		<div class="notes">{t}Toggle ON to require field on Subscription Form (user cannot leave blank){/t}</div>
	</div>

	<br />

	<div class="field">
		<label for="field_active">{t}Active:{/t} </label>
		<input type="radio" name="field_active" value="on" {if $field_active == 'on'}checked{/if}>on
		<input type="radio" name="field_active" value="off" {if $field_active != 'on'}checked{/if}>off
	<div class="notes">{t}Toggle OFF to HIDE field from Subscription Form{/t}</div>
	</div>
	
	<br />

	{if $field.type == 'text' || $field.type == 'number' || $field.type == 'date'}
	
	<div class="field">
		<label for="field_normally">{t}Default:{/t} </label>
		<input type="text" class="text" maxlength="60" size="32"
		name="field_normally" value="{$field_normally|escape}"  id="field_normally" />
		<div class="notes">{t}If provided, this value will appear pre-filled on the subscription form{/t}</div>
	</div>
	
	{elseif $field.type == 'checkbox'}
	
	<div class="field">
		<label for="field_normally">{t}Default:{/t} </label>
		<select name="field_normally" id="field_normally" />
			<option value="on" {if $field_normally}SELECTED{/if}>Checked</option>
			<option value="off" {if !$field_normally}SELECTED{/if}>Not Checked</option>
		</select>
		<div class="notes">{t}If provided, this value will appear pre-filled on the subscription form{/t}</div>
	</div>
	
	{elseif $field.type == 'multiple'}
	
	<div class="field">
		<label for="field_normally">{t}Default:{/t} </label>
		<select name="field_normally" id="field_normally" />
			<option value="">Select default choice</option>
			 {if $field.options}
    			{foreach from=$field.options item=option}
    				<option {if $field_normally == $option}SELECTED{/if}>{$option}</option>
    			{/foreach}
 			{/if}
		</select>
		<div class="notes">{t}If provided, this value will appear pre-filled on the subscription form{/t}</div>
	</div>
	
	{/if}
	
	<br />
</fieldset>
 	
 <div>
 	<input class="button" type="submit" value="{t}Update{/t}" />
 </div>
</form>

{if $field.type == 'multiple'}

<form id="dVal" name="dVal" action="" method="POST">
	<input type="hidden" name="field_id" value="{$field.id}">
	
	<div class="field" align="right">
		<input type="text" class="text" name="addOption" id="addOption" 
		  title="{t}type option(s){/t}" value="" size="50" />
		<input class="button" id="dVal-add" name="dVal-add" type="submit" value="{t}Add Option(s){/t}" />
	</div>
	<div class="msgdisplay">
		{t}NOTE: You can add multiple options by separating each with a comma. To include special characters such as quote marks and commas, prefix them with a backslash (\\).{/t}
	</div>
	<br><br>

	<div class="field" align="right">
		<select name="delOption" id="delOption"/>
			{if $field.options}
    			{foreach from=$field.options item=option}
    				<option>{$option}</option>
    			{/foreach}
 			{/if}
		</select>
		<input class="button" id="dVal-del" name="dVal-del" type="submit" value="{t}Remove Selected Option{/t}" />
	</div>
</form>
{/if}


</div>
<!-- end mainbar -->

{include file="admin/inc.footer.tpl"}