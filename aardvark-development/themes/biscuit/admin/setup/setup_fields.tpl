{capture name=head}{* used to inject content into the HTML <head> *}
{include file="inc/ui.form.tpl"}
{include file="inc/ui.dialog.tpl"}
{include file="inc/ui.cssTable.tpl"}
{include file="inc/ui.sort.tpl"}
{/capture}
{include file="inc/admin.header.tpl"}

<h2><img src="{$url.theme.shared}images/icons/fields.png" alt="fields icon" class="navimage left" />{t}Subscriber Fields{/t}</h2>

{t escape=no 1="<a href='`$url.base`admin/subscribers/subscribers_groups.php'>" 2="</a>"}Subscriber fields allow you to collect information on list members. They are typically displayed on the subscription form, although "hidden" ones can be used for administrative purposes. %1Groups%2 are based on subscriber field values.{/t}

<br class="clear"/>

<form method="post" action="">

{include file="inc/messages.tpl"}

<fieldset>
<h3>{t}Fields{/t}</h3>

<div>
<label for="field_name">{t}Field Name: {/t}</label>
<input type="text" title="{t}type new field name{/t}" maxlength="60" size="30" name="field_name" id="field_name" />
</div>

<div>
<label for="field_type">{t}Select Type: {/t}</label>
<select name="field_type" id="field_type">
<option value="text">{t}Text{/t}</option>
<option value="number">{t}Number{/t}</option>
<option value="checkbox">{t}Checkbox{/t}</option>
<option value="multiple">{t}Multiple Choice{/t}</option>
<option value="date">{t}Date{/t}</option>
<option value="comment">{t}Comment{/t}</option>
</select>
</div>

<div class="buttons">
<button type="submit" value="{t}Add{/t}" class="positive" /><img src="{$url.theme.shared}/images/icons/tick.png" alt="add field"/>{t}Add Field{/t}</button>
</div>

</fieldset>
</form>

<br class="clear"/>

<fieldset>
<h3>{t}Field Ordering{/t}</h3>

<p>{t}Change the ordering of fields on the subscription form by dragging and dropping the order icon{/t}</p>

<div id="grid">

<div class="header">
<span>{t}ID{/t}</span>
<span>{t}Delete{/t}</span>
<span>{t}Edit{/t}</span>
<span>{t}Order{/t}</span>
<span>{t}Field Name{/t}</span>
</div>

{foreach from=$fields key=key item=field}

<div class="{cycle values="r1,r2,r3"} sortable" id="id{$key}">

<span>
{$field.id}
</span>

<span>
<a href="{$smarty.server.PHP_SELF}?field_id={$key}&amp;delete=TRUE&amp;field_name={$field.name}" onclick="return confirm('Delete this field?');"><img src="{$url.theme.shared}images/icons/delete.png" alt="delete icon" /></a>
</span>

<span>
<a href="ajax/field_edit.php?field_id={$key}" class="editTrigger"><img src="{$url.theme.shared}images/icons/edit.png" alt="edit icon" /></a>
</span>

<span>
<img src="{$url.theme.shared}images/icons/order.png" alt="order icon" class="handle" />
</span>

<span class="name{if $field.active == 'on'} green{/if}">
{if $field.required == 'on'}<strong>{$field.name}</strong>{else}{$field.name}{/if}
</span>

</div>

{/foreach}

</div>

<p>
{t escape=no 1='<span class="required">' 2='</span>'}%1 Bolded %2 fields are required.{/t} 
{t escape=no 1='<span class="green">' 2='</span>'}%1Green%2 fields are active.{/t}
</p>

{if $added}
<a href="ajax/field_edit.php?field_id={$key}" id="added" class="hidden"></a>
{/if}
</fieldset>

{literal}
<script type="text/javascript">
$().ready(function(){

	// setup dialogs
	PommoDialog.init('#dialog',{modal: true, trigger: '.editTrigger'});
	
	// setup sorting
	PommoSort.init('#grid',{updateURL: 'ajax/fields.rpc.php?call=updateOrdering'});
	
	// trigger editing of recently added field
	var a = $('#added')[0];
	if(a) 
		$('#dialog').jqmShow(a);
	
});

poMMo.callback.updateField = function(f) {
	var name = f.name;
	if(f.required == 'on')
		name = '<strong>'+name+'</strong';
		
	var e = $('#id'+f.id+' span.name').html(name);
	
	e.removeClass('green');
	if(f.active == 'on')
		e.addClass('green');
};

poMMo.callback.updateOptions = function(json) {
	
	// remember #normally
	var normally = $('#normally').val();
	
	// remove existing options
	$('#delOptions option, #normally option:gt(0)').remove();
	
	
	// clear addOptions input
	$('#addOptions').val('');
	
	// populate options
	for(var i=0;i<json.length;i++)
		$('#delOptions, #normally').append('<option>'+json[i]+'</option>');

	// restore #normally
	$('#normally').val(normally);
	
};

</script>
{/literal}


{capture name=dialogs}
{include file="inc/dialog.tpl" id="dialog" wide=true tall=true}
{/capture}
{include file="inc/admin.footer.tpl"}