<div id="subscribeForm">

<form method="post" action="">

<fieldset>
<legend>{t}Your Information{/t}</legend>
<input type="hidden" name="updateForm" value="true" />
<input type="hidden" name="original_email" value="{$original_email}" />

<p>{t escape=no 1="<span class=\"required\">" 2="</span>"}Fields in %1bold%2 are required{/t}</p>

<div>
<label class="required" for="email">{t}Your Email:{/t}</label>
<input type="text" class="text" size="32" maxlength="60" name="bm_email" id="email" value="{$bm_email}" />
</div>

<div>
<label class="required" for="email2">{t}Verify Email:{/t}</label>
<input type="text" class="text" size="32" maxlength="60" name="email2" id="email2" value="{$email2}" />
</div>

{foreach name=demos from=$fields key=key item=demo}
<div>
<label{if $demo.required} class="required"{/if} for="field{$key}">{$demo.prompt}</label>

{if $demo.type == 'text'}
<input type="text" class="text" size="32" name="d[{$key}]" id="field{$key}"{if isset($d.$key)} value="{$d.$key}"{elseif $demo.normally} value="{$demo.normally}"{/if} />

{elseif $demo.type == 'checkbox'}
<input type="hidden" name="chkSubmitted" value="TRUE" />
<input type="checkbox" name="d[{$key}]" id="field{$key}" {if $d.$key == "on"} checked="checked"{elseif !isset($chkSubmitted) && $demo.normally == "on"} checked="checked"{/if} />

{elseif $demo.type == 'multiple'}
<select name="d[{$key}]" id="field{$key}">
<option value="">{t}Choose Selection{/t}</option>
{foreach from=$demo.options item=option}
<option {if $d.$key == $option} selected="selected"{elseif !isset($d.$key) && $demo.normally == $option} selected="selected"{/if}>{$option}</option>
{/foreach}
</select>

{else}
{t}Unsupported Field Type{/t}

{/if}
</div>

{/foreach}

</fieldset>

<div class="buttons">

<input class="button" type="submit" name="update" value="{t}Update Records{/t}" />

</div>
		
</form>
</div>