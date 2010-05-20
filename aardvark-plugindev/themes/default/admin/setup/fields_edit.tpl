{include file="inc/admin.header.tpl"}

<h2>{t}Edit Field{/t}</h2>

<ul class="inpage_menu">
<li><a href="{$url.base}admin/setup/setup_fields.php">{t}Return to Fields Page{/t}</a></li>
</ul>

{if $intro}<p><img src="{$url.theme.shared}images/icons/fields.png" alt="fields icon" class="navimage right" /> {$intro}</p>{/if}

{include file="inc/messages.tpl"}
 
<form method="post" action="">
<input type="hidden" name="field_id" value="{$field.id}" />
  
<fieldset>
<legend>'{$field_name}' parameters</legend>

<div>
<label for="field_name"><span class="required">{t}Short Name:{/t}</span> <span class="error">{validate id="field_name" message=$formError.field_name}</span></label>
<input type="text" maxlength="60" size="32" name="field_name" id="field_name" value="{$field_name|escape}"/>
<div class="notes">{t}Identifying name. NOT displayed on Subscription Form or seen by users.{/t}</div>
</div>

<div>
<label for="field_prompt"><span class="required">{t}Form Name:{/t}</span> <span class="error">{validate id="field_prompt" message=$formError.field_prompt}</span></label>
<input type="text" maxlength="60" size="32" name="field_prompt" id="field_prompt" value="{$field_prompt|escape}"/>
<div class="notes">{t escape='no' 1='<tt>' 2='</tt>'}Prompt for field on the Subscription Form. e.g. %1Type your city%2{/t}</div>
</div>

<div>
<label for="field_required">{t}Required:{/t}</label>
<input type="radio" name="field_required" id="field_required" value="on"{if $field_required == 'on'} checked="checked"{/if} /> yes
<input type="radio" name="field_required" value="off"{if $field_required != 'on'} checked="checked"{/if} /> no
<div class="notes">{t escape='no' 1='<tt>' 2='</tt>'}Toggle to require field on Subscription Form (user cannot leave blank if %1yes%2){/t}</div>
</div>

<div>
<label for="field_active">{t}Active:{/t}</label>
<input type="radio" name="field_active" id="field_active" value="on" {if $field_active == 'on'} checked="checked"{/if} /> show
<input type="radio" name="field_active" value="off" {if $field_active != 'on'} checked="checked"{/if} /> hide
<div class="notes">{t}Toggle display of field for Subscription Form{/t}</div>
</div>

{if $field.type == 'text' || $field.type == 'number' || $field.type == 'date'}
<div>
<label for="field_normally">{t}Default:{/t}</label>
<input type="text" maxlength="60" size="32" name="field_normally" id="field_normally" value="{$field_normally|escape}" />
<div class="notes">{t}If provided, this value will appear pre-filled on the subscription form{/t}</div>
</div>

{elseif $field.type == 'checkbox'}
<div>
<label for="field_normally">{t}Default:{/t}</label>
<select name="field_normally" id="field_normally">
<option value="on"{if $field_normally == 'on'} selected="selected"{/if}>Checked</option>
<option value="off"{if $field_normally == 'off'} selected="selected"{/if}>Not Checked</option>
</select>
<div class="notes">{t}If provided, this value will appear pre-filled on the subscription form{/t}</div>
</div>

{elseif $field.type == 'multiple'}
<div>
<label for="field_normally">{t}Default:{/t}</label>
<select name="field_normally" id="field_normally">
<option value="">Select default choice</option>
{if $field.array}
{foreach from=$field.array item=option}
<option{if $field_normally == $option} selected="selected"{/if}>{$option}</option>
{/foreach}
{/if}
</select>
<div class="notes">{t}If provided, this value will appear pre-filled on the subscription form{/t}</div>
</div>
{/if}

</fieldset>
 	
<div class="buttons">

<input type="submit" value="{t}Update{/t}" />

</div>

</form>

{if $field.type == 'multiple'}
<form method="post" action="" id="dVal" name="dVal">
<fieldset>
<legend>Multiple options</legend>

<input type="hidden" name="field_id" value="{$field.id}" />

<div class="alert">{t}NOTE: You can add multiple options at once by separating each with a comma.{/t}</div>

<div>
<label for="addOption">Options:</label>
<input type="text" name="addOption" id="addOption" title="{t}type option(s){/t}" size="50" />
</div>

<div class="buttons">

<input type="submit" id="dVal-add" name="dVal-add" value="{t}Add Option(s){/t}" />

</div>

<div>
<label for="delOption">Delete:</label>
<select name="delOption" id="delOption">
{if $field.array}
{foreach from=$field.array item=option}
<option>{$option}</option>
{/foreach}
{/if}
</select>
</div>

<div class="buttons">

<input type="submit" id="dVal-del" name="dVal-del" value="{t}Remove Selected Option{/t}" />

</div>

</fieldset>
</form>
{/if}

{include file="inc/admin.footer.tpl"}