{* Field Validation - see docs/template.txt documentation *}
{fv prepend='<span class="error">' append='</span>'}
{fv validate="email"}

{include file="inc/messages.tpl"}

{if $sent}
	<div class="alert">
	{t escape=no 1="<strong>`$sent`</strong>"}Mailing successfully sent to %1{/t}
	</div>
{/if}

<p>{t}Verify the appeareance of a mailing by sending a message to yourself.{/t}</p>

<form class="ajax" action="{$smarty.server.PHP_SELF}" method="post">

<fieldset>
<h3>{t}Recipient{/t}</h3>

<div>
<label class="required" for="email">{t}Email:{/t}{fv message="email"}</label>
<input type="text" size="30" maxlength="60" name="email" value="{$email|escape}" />
</div>

<div class="buttons" style="padding-left: 41px;">
<button type="submit" value="{t}Send Test{/t}" class="positive"><img src="{$url.theme.shared}images/icons/tick.png" alt="send email" />{t}Send Test{/t}</button>
</div>

</fieldset>

<br class="clear" />

<fieldset>
<h3>{t}Personalizations{/t}</h3>
{t escape='no' 1='<strong>' 2='</strong>'}If your mailing includes personalizations, you can %1optionally%2 supply test values{/t}

{foreach name=fields from=$fields key=key item=field}
<div>
<label{if $field.required == 'on'} class="required"{/if} for="field{$key}">{$field.prompt}:</label>

{if $field.type == 'checkbox'}
<input type="checkbox" name="d[{$key}]"{if $field.normally == "on"} checked="checked"{/if}{if $field.required == 'on'} class="pvEmpty"{/if} />

{elseif $field.type == 'multiple'}
<select name="d[{$key}]">
{foreach from=$field.array item=option}
{$option}</option>
{/foreach}
</select>

{elseif $field.type == 'date'}
<input type="text" class="pvDate{if $field.required == 'on'} pvEmpty{/if}" size="12" name="d[{$key}]" value="{if $field.normally}{$field.normally|escape}{else}{$config.app.dateformat}{/if}" />

{elseif $field.type == 'number'}
<input type="text" class="pvNumber{if $field.required == 'on'} pvEmpty{/if}" size="12" name="d[{$key}]" value="{if $field.normally}{$field.normally|escape}{/if}" />

{else}
<input type="text"{if $field.required == 'on'} class="pvEmpty"{/if} size="32" name="d[{$key}]" value="{if $field.normally}{$field.normally|escape}{/if}" />
{/if}

</div>

{/foreach}

</fieldset>

</form>