{capture name=head}{* used to inject content into the HTML <head> *}
<script type="text/javascript" src="{$url.theme.shared}js/jq/jquery.js"></script>
<script type="text/javascript" src="{$url.theme.shared}js/jq/interface.js"></script>
{* Styling of CSS table *}
<link type="text/css" rel="stylesheet" href="{$url.theme.shared}css/grid.css" />
{/capture}
{include file="inc/admin.header.tpl"}

<h2>{t}Fields Page{/t}</h2>

{if $intro}<p><img src="{$url.theme.shared}images/icons/fields.png" alt="fields icon" class="navimage right" /> {$intro}</p>{/if}

<form method="post" action="">

{include file="inc/messages.tpl"}

<fieldset>
<legend>{t}Fields{/t}</legend>

<div>
<label for="field_name">{t}New field name:{/t}</label>
<input type="text" title="{t}type new field name{/t}" maxlength="60" size="30" name="field_name" id="field_name" />
</div>

<div>
<label for="field_type">{t}Value type:{/t}</label>
<select name="field_type" id="field_type">
<option value="text">{t}Text{/t}</option>
<option value="number">{t}Number{/t}</option>
<option value="checkbox">{t}Checkbox{/t}</option>
<option value="multiple">{t}Multiple Choice{/t}</option>
<option value="date">{t}Date{/t}</option>
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
<span>{t}Delete{/t}</span>
<span>{t}Edit{/t}</span>
<span>{t}Order{/t}</span>
<span>{t}Field Name{/t}</span>
</div>

{foreach name=fields from=$fields key=key item=field}

<div class="{cycle values="r1,r2,r3"} sortable" id="id{$key}">

<span>
<a href="{$smarty.server.PHP_SELF}?field_id={$key}&amp;delete=TRUE&amp;field_name={$field.name}"><img src="{$url.theme.shared}images/icons/delete.png" alt="delete icon" /></a>
</span>

<span>
<a href="fields_edit.php?field_id={$key}"><img src="{$url.theme.shared}images/icons/edit.png" alt="edit icon" /></a>
</span>

<span>
<img src="{$url.theme.shared}images/icons/order.png" alt="order icon" class="handle" id="a{$key}" />
</span>

<span{if $field.active == 'on'} class="green"{/if}>
{if $field.required == 'on'}
<strong>
{$field.name}
</strong>
{else}
{$field.name}
{/if}
- <em>{$field.type}</em>
</span>

</div>

{/foreach}

</div>

<p>
{t escape=no 1='<strong>' 2='</strong>'}%1Bold%2 fields are required.{/t} 
{t escape=no 1='<span class="green">' 2='</span>'}%1Green%2 fields are active.{/t}
</p>



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

});
// old prototype code
//Sortable.create('fieldOrder',{tag:'div', handle: 'handle', onUpdate:function(){new Ajax.Updater('ajaxOutput', 'ajax_fieldOrder.php', {onComplete:function(request){new Effect.Highlight('fieldOrder',{});}, parameters:Sortable.serialize('fieldOrder'), evalScripts:true, asynchronous:true})}});
</script>
{/literal}

{include file="inc/admin.footer.tpl"}