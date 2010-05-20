{capture name=head}{* used to inject content into the HTML <head> *}
<script type="text/javascript" src="{$url.theme.shared}js/jq/jq11.js"></script>
<script type="text/javascript" src="{$url.theme.shared}js/jq/interface.js"></script>
<script type="text/javascript" src="{$url.theme.shared}js/jq/jqModal.js"></script>
<script type="text/javascript" src="{$url.theme.shared}js/jq/form.js"></script>
<link type="text/css" rel="stylesheet" href="{$url.theme.shared}css/modal.css" />
<link type="text/css" rel="stylesheet" href="{$url.theme.shared}css/grid.css" />
{/capture}
{include file="inc/admin.header.tpl"}

<h2>{t}Fields Page{/t}</h2>

<p>
<img src="{$url.theme.shared}images/icons/fields.png" alt="fields icon" class="navimage right" />
{t escape=no 1="<a href='`$url.base`admin/subscribers/subscribers_groups.php'>" 2="</a>"}Subscriber fields allow you to collect information on list members. They are typically displayed on the subscription form, although "hidden" ones can be used for administrative purposes. %1Groups%2 are based on subscriber field values.{/t}
</p>

<form method="post" action="">

{include file="inc/messages.tpl"}

<fieldset>
<legend>{t}Fields{/t}</legend>

<div>
<label for="field_name">{t}New field name:{/t}</label>
<input type="text" title="{t}type new field name{/t}" maxlength="60" size="30" name="field_name" id="field_name" />
</div>

<div>
<label for="field_type">{t}Field type:{/t}</label>
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

<input type="submit" value="{t}Add{/t}" />

</div>

</fieldset>
</form>

<h3>{t}Field Ordering{/t}</h3>

<ul>
<li>{t}Change the ordering of fields on the subscription form by dragging and dropping the order icon{/t}</li>
</ul>

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
<a href="{$smarty.server.PHP_SELF}?field_id={$key}&amp;delete=TRUE&amp;field_name={$field.name}"><img src="{$url.theme.shared}images/icons/delete.png" alt="delete icon" /></a>
</span>

<span>
<a href="ajax/field_edit.php?field_id={$key}" class="editTrigger"><img src="{$url.theme.shared}images/icons/edit.png" alt="edit icon" /></a>
</span>

<span>
<img src="{$url.theme.shared}images/icons/order.png" alt="order icon" class="handle" id="a{$key}" />
</span>

<span class="name{if $field.active == 'on'} green{/if}">
{if $field.required == 'on'}<strong>{$field.name}</strong>{else}{$field.name}{/if}
</span>

</div>

{/foreach}

</div>

<p>
{t escape=no 1='<strong>' 2='</strong>'}%1Bold%2 fields are required.{/t} 
{t escape=no 1='<span class="green">' 2='</span>'}%1Green%2 fields are active.{/t}
</p>

{if $added}
<a href="ajax/field_edit.php?field_id={$added}" id="added" class="hidden"></a>
{/if}


{literal}
<script type="text/javascript">
var pommoSort = {
	init: function() {
		var s = $.SortSerialize('grid');
		this.hash = s.hash;
	},
	update: function(hash) {
		if (this.hash == hash)
			return false; // don't do a thing if unchanged...
		this.hash = hash;

		$.post("ajax/fields_order.php", this.hash, function(json) {
			eval("var args = " + json);

			if (typeof(args.success) == 'undefined') {
				alert('ajax error!');
				return;
			}

			$('#ajaxOut').html(args.msg).fadeIn('fast');

			if (args.success === true)
				$('#ajaxOut').fadeOut(6000);

		});

		return false;
	}
};

$().ready(function(){

	$('#grid').Sortable({
		accept : 'sortable',
		handle: 'img.handle',
		opacity: 0.8,
		tolerance: 'intersect',
		onStop: function() {
			var s = $.SortSerialize('grid');
			pommoSort.update(s.hash);
		}
	});
	pommoSort.init();
	
	$('#editWindow').jqm({
		modal: true,
		ajax: '@href',
		target: '.jqmdMSG',
		trigger: '.editTrigger',
		onLoad: function(){assignForm(this);}
	}).jqDrag('div.jqmdTC');
	
	if($('#added')[0]) {
		var a = $('#added');
		$('#editWindow').jqmAddTrigger(a);
		a.click();
	}
	
	$('#confirm').jqm({trigger:false,modal:true,zIndex:5000});
});

function assignForm(scope) {
	$('form',scope).ajaxForm( { 
		target: scope,
		beforeSubmit: function() {
			$('input[@type=submit]', scope).hide();
			$('img[@name=loading]', scope).show();
		},
		success: function() {
			assignForm(this); 
			$('div.output',this).fadeOut(5000); 
			
			if($('input[@name=success]',this).val()) {
				var name=$('input[@name=field_name]',this).val();
				var req=$('input[@name=field_required]:checked',this).val();
				var active=$('input[@name=field_active]:checked',this).val();
				
				if(req == 'on')
					name='<strong>'+name+'</strong>';
				
				var e=$('#id'+$('input[@name=field_id]',this).val()+' span.name').html(name);
				
				if(active == 'on')
					e.addClass('green');
				else
					e.removeClass('green');}
			}
		}
	);
}
</script>
{/literal}



{capture name=dialogs}
{include file="inc/dialog.tpl" dialogID="editWindow" dialogTitle=$testTitle dialogDrag=true dialogClass="jqmdWide" dialogBodyClass="jqmdTall"}
{include file="inc/dialog.tpl" dialogID="confirm" dialogTitle=$confirm.title dialogClass="jqmdWide"}
{/capture}
{include file="inc/admin.footer.tpl"}