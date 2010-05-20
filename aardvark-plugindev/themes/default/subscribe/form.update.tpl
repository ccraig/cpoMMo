<div id="subscribeForm">

<form method="post" action="">
<input type="hidden" name="formSubmitted" value="1" />

<fieldset>
<legend>{t}Your Information{/t}</legend>
<input type="hidden" name="updateForm" value="true" />

<div class="notes">

<p>{t escape=no 1="<strong class=\"required\">" 2="</strong>"}%1Fields%2 are required{/t}</p>

</div>

<div>
<label class="required" for="email"><strong>{t}Your Email:{/t}</strong></label>
<input type="text" size="32" maxlength="60" name="Email" id="email" value="{$Email|escape}" readonly="readonly" />
</div>

<div>
<label for="email">{t}New Email:{/t}</label>
<input type="text" size="32" maxlength="60" name="newemail" id="newemail" value="{$newemail|escape}" />
</div>

<div>
<label for="email">{t}Verify New Email:{/t}</label>
<input type="text" size="32" maxlength="60" name="newemail2" id="newemail2" value="{$newemail2|escape}" />
</div>

{foreach name=fields from=$fields key=key item=field}
<div>
<label{if $field.required == 'on'} class="required"{/if} for="field{$key}">{$field.prompt}:</label>

{if $field.type == 'text' || $field.type == 'number'}
<input type="text" size="32" name="d[{$key}]" id="field{$key}"{if isset($d.$key)} value="{$d.$key|escape}"{/if} />

{elseif $field.type == 'checkbox'}
<input type="checkbox" name="d[{$key}]" id="field{$key}"{if $d.$key == "on"} checked="checked"{/if} />

{elseif $field.type == 'multiple'}
<select name="d[{$key}]" id="field{$key}">
<option value="">{t}Choose Selection{/t}</option>
{foreach from=$field.array item=option}
<option{if $d.$key == $option} selected="selected"{/if}>{$option}</option>
{/foreach}
</select>

{elseif $field.type == 'date'}
<input type="text" class="text datepicker" size=12 name="d[{$key}]" id="field{$key}" value={if isset($d.$key)}"{$d.$key|date_format:"%m/%d/%Y"}"{/if} />

{else}
<p class="error">{t}Unsupported field type{/t}</p>
{/if}

</div>

{/foreach}

</fieldset>

<div class="buttons">

<input type="submit" name="update" value="{t}Update Records{/t}" />

</div>
		
</form>
</div>