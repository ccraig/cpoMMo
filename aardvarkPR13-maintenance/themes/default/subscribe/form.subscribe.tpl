<div id="subscribeForm">

<form method="post" action="{$url.base}user/process.php">
<fieldset>
<legend>{t}Your Information{/t}</legend>

{if $referer}
<input type="hidden" name="bmReferer" value="{$referer}" />
{/if}

<div class="notes">
{t escape=no 1="<span class=\"required\">" 2="</span>"}Fields in %1bold%2 are required{/t}
</div>

<div>
<label class="required" for="email">{t}Your Email:{/t}</label>
<input type="text" class="text" size="32" maxlength="60" name="bm_email" id="email" value="{$bm_email|escape}" />
</div>

{foreach name=demos from=$fields key=key item=demo}
<div>
<label {if $demo.required == 'on'}class="required"{/if}>{$demo.prompt}</label>

{if $demo.type == 'text' || $demo.type == 'number'}
<input type="text" class="text" size="32" name="d[{$key}]" id="field{$key}"{if isset($d.$key)} value="{$d.$key|escape}"{elseif $demo.normally} value="{$demo.normally|escape}"{/if} />

{elseif $demo.type == 'checkbox'}
<input type="hidden" name="chkSubmitted" value="TRUE" />
<input type="checkbox" name="d[{$key}]" id="field{$key}"{if $d.$key == "on"} checked="checked"{elseif !isset($chkSubmitted) && $demo.normally == "on"} checked="checked"{/if} />

{elseif $demo.type == 'multiple'}
<select name="d[{$key}]" id="field{$key}">
<option value="">{t}Choose Selection{/t}</option>
{foreach from=$demo.options item=option}
<option{if $d.$key == $option} selected="selected"{elseif !isset($d.$key) && $demo.normally == $option} selected="selected"{/if}>{$option}</option>
{/foreach}
</select>

{else}
{t}Unsupported field type{/t}
{/if}

</div>

{/foreach}
	
</fieldset>

<div class="buttons">

<input type="hidden" name="pommo_signup" value="true" />
<input class="button" type="submit" name="pommo_signup" value="{t}Subscribe{/t}" />

</div>
		
</form>

</div>