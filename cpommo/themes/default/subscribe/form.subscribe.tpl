<div id="subscribeForm">

<form method="post" action="{$url.base}user/process.php">
<fieldset>
<legend>{t}Join newsletter{/t}</legend>

<input type="hidden" name="formSubmitted" value="1" />
{if $referer}
<input type="hidden" name="bmReferer" value="{$referer}" />
{/if}

<div class="notes">
<p>{t escape=no 1="<span class=\"required\">" 2="</span>"}Fields marked like %1 this %2 are required.{/t}</p>
</div>

<div>
<label class="required" for="email"><strong>{t}Your Email:{/t}</strong></label>
<input type="text" size="32" maxlength="60" name="Email" id="email" value="{$Email|escape}" />
</div>

{foreach name=fields from=$fields key=key item=field}
<div>
<label for="field{$key}">{if $field.required == 'on'}<strong class="required">{/if}{$field.prompt}{if $field.required == 'on'}</strong>{/if}:</label>

{if $field.type == 'text' || $field.type == 'number'}
<input type="text" size="32" name="d[{$key}]" id="field{$key}"{if isset($d.$key)} value="{$d.$key|escape}"{elseif $field.normally} value="{$field.normally|escape}"{/if} />

{elseif $field.type == 'checkbox'}
<input type="checkbox" name="d[{$key}]" id="field{$key}"{if $d.$key == "on"} checked="checked"{elseif !$formSubmitted && $field.normally == "on"} checked="checked"{/if} />

{elseif $field.type == 'multiple'}
<select name="d[{$key}]" id="field{$key}">
<option value="">{t}Choose Selection{/t}</option>
{foreach from=$field.array item=option}
<option{if $d.$key == $option} selected="selected"{elseif !isset($d.$key) && $field.normally == $option} selected="selected"{/if}>{$option}</option>
{/foreach}
</select>

{elseif $field.type == 'date'}
<input type="text" class="datepicker" size="12" name="d[{$key}]" id="field{$key}" value={if isset($d.$key)}"{$d.$key|escape}"{elseif $field.normally}"{$field.normally|escape}"{else}"{$config.app.dateformat}"{/if} />

{elseif $field.type == 'comment'}
<textarea name="comments" rows="3" cols="33" maxlength="255">{if isset($d.$key)}{$d.$key}{elseif $field.normally}{$field.normally}{/if}</textarea>

{/if}

</div>

{/foreach}

</fieldset>

<div class="buttons">

<input type="hidden" name="pommo_signup" value="true" />
<input type="submit" name="pommo_signup" value="{t}Subscribe{/t}" />

</div>
		
</form>

</div>