{* Field Validation - see docs/template.txt documentation *}
{fv prepend='<span class="error">' append='</span>'}
{fv validate="field_name"}
{fv validate="field_prompt"}
{fv validate="field_required"}
{fv validate="field_active"}

<p>
{$intro}
</p>

<div class="output">{include file="inc/messages.tpl"}</div>
 
<form action="{$smarty.server.PHP_SELF}" method="post">
<input type="hidden" name="field_id" value="{$field.id}" />
<input type="hidden" name="success" value="{$updated}" />
  
<fieldset>
<legend>'{$field_name}' parameters</legend>

<div>
<label for="field_name"><strong class="required">{t}Short Name:{/t}</strong>{fv message="field_name"}</label>
<input type="text" name="field_name" value="{$field_name|escape}"/>
<div class="notes">{t}Identifying name. NOT displayed on Subscription Form or seen by users.{/t}</div>
</div>

<div>
<label for="field_prompt"><strong class="required">{t}Form Name:{/t}</strong>{fv message="field_prompt"}</label>
<input type="text" name="field_prompt" value="{$field_prompt|escape}"/>
<div class="notes">{t escape='no' 1='<tt>' 2='</tt>'}Prompt for field on the Subscription Form. e.g. %1Type your city%2{/t}</div>
</div>

{if $field.type != 'comment'}
<div>
<label for="field_required">{t}Required:{/t}{fv message="field_required"}</label>
<input type="radio" name="field_required" value="on"{if $field_required == 'on'} checked="checked"{/if} /> yes
<input type="radio" name="field_required" value="off"{if $field_required != 'on'} checked="checked"{/if} /> no
<div class="notes">{t escape='no' 1='<tt>' 2='</tt>'}Toggle to require field on Subscription Form (user cannot leave blank if %1yes%2){/t}</div>
</div>
{else}
<input type="hidden" name="field_required" value="off" />
{/if}

<div>
<label for="field_active">{t}Active:{/t}{fv message="field_active"}</label>
<input type="radio" name="field_active" value="on" {if $field_active == 'on'} checked="checked"{/if} /> show
<input type="radio" name="field_active" value="off" {if $field_active != 'on'} checked="checked"{/if} /> hide
<div class="notes">{t}Toggle display of field for Subscription Form{/t}</div>
</div>

{if $field.type == 'text' || $field.type == 'number' || $field.type == 'date'}
<div>
<label for="field_normally">{t}Default:{/t}</label>
<input type="text" name="field_normally" value="{$field_normally|escape}" />
<div class="notes">{t}If provided, this value will appear pre-filled on the subscription form{/t}</div>
</div>

{elseif $field.type == 'checkbox'}
<div>
<label for="field_normally">{t}Default:{/t}</label>
<select name="field_normally">
<option value="on"{if $field_normally == 'on'} selected="selected"{/if}>Checked</option>
<option value="off"{if $field_normally == 'off'} selected="selected"{/if}>Not Checked</option>
</select>
<div class="notes">{t}If provided, this value will appear pre-filled on the subscription form{/t}</div>
</div>

{elseif $field.type == 'multiple'}
<div>
<label for="field_normally">{t}Default:{/t}</label>
<select name="field_normally">
<option value="">{t}Select default choice{/t}</option>
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

<input type="submit" value="{t}Update{/t}" />
<img src="{$url.theme.shared}images/loader.gif" alt="loading..." class="hidden" name="loading" />

</form>

{if $field.type == 'multiple'}
<form action="{$smarty.server.PHP_SELF}" method="post" id="dVal" name="dVal">
<input type="hidden" name="field_id" value="{$field.id}" />

<fieldset>
<legend>{t}Multiple options{/t}</legend>

<div class="alert">{t}NOTE: You can add multiple options at once by separating each with a comma.{/t}</div>

<div>
<label for="addOption">{t}Options:{/t}</label>
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