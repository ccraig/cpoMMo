{* Field Validation - see docs/template.txt documentation *}
{fv prepend='<span class="error">' append='</span>'}
{fv validate="field_name"}
{fv validate="field_prompt"}
{fv validate="field_required"}
{fv validate="field_active"}

<p>
{$intro}
</p>
 
<form class="json" action="{$smarty.server.PHP_SELF}" method="post">
<div class="output alert">{include file="inc/messages.tpl"}</div>

<input type="hidden" name="field_id" value="{$field.id}" />
  
<fieldset>
<legend>'{$field.name}' parameters</legend>

<div>
<label for="field_name"><strong class="required">{t}Short Name:{/t}</strong>{fv message="field_name"}</label>
<input type="text" name="field_name" value="{$field.name|escape}"/>
<div class="notes">{t}Identifying name. NOT displayed on Subscription Form or seen by users.{/t}</div>
</div>

<div>
<label for="field_prompt"><strong class="required">{t}Form Name:{/t}</strong>{fv message="field_prompt"}</label>
<input type="text" name="field_prompt" value="{$field.prompt|escape}"/>
<div class="notes">{t escape='no' 1='<tt>' 2='</tt>'}Prompt for field on the Subscription Form. e.g. %1Type your city%2{/t}</div>
</div>

{if $field.type != 'comment'}
<div>
<label for="field_required">{t}Required:{/t}{fv message="field_required"}</label>
<input type="radio" name="field_required" value="on"{if $field.required == 'on'} checked="checked"{/if} /> yes
<input type="radio" name="field_required" value="off"{if $field.required != 'on'} checked="checked"{/if} /> no
<div class="notes">{t escape='no' 1='<tt>' 2='</tt>'}Toggle to require field on Subscription Form (user cannot leave blank if %1yes%2){/t}</div>
</div>
{else}
<input type="hidden" name="field_required" value="off" />
{/if}

<div>
<label for="field_active">{t}Active:{/t}{fv message="field_active"}</label>
<input type="radio" name="field_active" value="on" {if $field.active == 'on'} checked="checked"{/if} /> show
<input type="radio" name="field_active" value="off" {if $field.active != 'on'} checked="checked"{/if} /> hide
<div class="notes">{t}Toggle display of field for Subscription Form{/t}</div>
</div>

{if $field.type == 'text' || $field.type == 'number' || $field.type == 'date'}
<div>
<label for="field_normally">{t}Default:{/t}</label>
<input type="text" name="field_normally" value="{$field.normally|escape}" />
<div class="notes">{t}If provided, this value will appear pre-filled on the subscription form{/t}</div>
</div>

{elseif $field.type == 'checkbox'}
<div>
<label for="field_normally">{t}Default:{/t}</label>
<select name="field_normally">
<option value="on"{if $field.normally == 'on'} selected="selected"{/if}>Checked</option>
<option value="off"{if $field.normally == 'off'} selected="selected"{/if}>Not Checked</option>
</select>
<div class="notes">{t}If provided, this value will appear pre-filled on the subscription form{/t}</div>
</div>

{elseif $field.type == 'multiple'}
<div>
<label for="field_normally">{t}Default:{/t}</label>
<select name="field_normally" id="normally">
<option value="">{t}Select default choice{/t}</option>
{if $field.array}
{foreach from=$field.array item=option}
<option{if $field.normally == $option} selected="selected"{/if}>{$option}</option>
{/foreach}
{/if}
</select>
<div class="notes">{t}If provided, this value will appear pre-filled on the subscription form{/t}</div>
</div>
{/if}

</fieldset>

<input type="submit" value="{t}Update{/t}" />
<img src="{$url.theme.shared}images/loader.gif" name="loading" class="hidden" title="{t}loading...{/t}" alt="{t}loading...{/t}" />


</form>

{if $field.type == 'multiple'}

<fieldset>
<legend>{t}Multiple Choices{/t}</legend>

<form class="json" action="ajax/fields.rpc.php?call=addOption" method="post">
	<input type="hidden" name="field_id" value="{$field.id}" />
	<div>
	<label for="options">{t}Add Option(s){/t}</label>
	<input type="text" name="options" title="{t}type option(s){/t}" size="50" id="addOptions" />
	<input type="submit" value="{t}Add{/t}" />
	<img src="{$url.theme.shared}images/loader.gif" name="loading" class="hidden" title="{t}loading...{/t}" alt="{t}loading...{/t}" />
	<div class="notes">{t}Enter a multiple choice option. You can add more than one choice at a time by separating each with a comma.{/t}</div>
	<div class="output"></div>
	</div>
</form>

<form class="json confirm" action="ajax/fields.rpc.php?call=delOption" method="post">
	<input type="hidden" name="field_id" value="{$field.id}" />
	<div>
	<label for="options">{t}Delete Option(s){/t}</label>
	<select name="options" id="delOptions">
		{if $field.array}
		{foreach from=$field.array item=option}
		<option>{$option}</option>
		{/foreach}
		{/if}
	</select>
	<input type="submit" value="{t}Delete{/t}" />
	<img src="{$url.theme.shared}images/loader.gif" name="loading" class="hidden" title="{t}loading...{/t}" alt="{t}loading...{/t}" />
	<div class="output"></div>
	</div>
</form>

</fieldset>
{/if}