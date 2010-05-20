<input type="hidden" id="fwGroupID" name="group_id" value="{$group_id}" />
<input type="hidden" id="fwRuleType" name="rule_type" value="{$type}" />
<input type="hidden" id="fwMatchID" name="field_id" value="{$field.id}" />

<p>
{t}Add new rule:{/t} 

{if $field.type == 'date'}
	{t}value must be a date (mm/dd/yyyy){/t}

{elseif $field.type == 'number'}
	{t}value must be a number{/t}

{elseif $field.type == 'text'}
	{t}value must not be blank{/t}

{/if}
</p>

<table summary="rule setup">
<tbody>
<tr>
<td valign="top">
{t}Match subscribers where{/t} <strong>{$field.name}</strong>

<select name="logic" id="fwLogic">
{foreach from=$logic key=val item=desc}
<option value="{$val}">{$desc}</option>
{/foreach}
</select>

</td>

{if $field.type == 'multiple'}

<td valign="top" id="fwValue">
<select name="v" class="pvEmpty">
{foreach from=$field.array item=option}
<option{if $option == $firstVal} selected="selected"{/if}>{$option}</option>
{/foreach}
</select>

<input type="button" value="+" id="fwAddValue" />

{* If we're editing, add another input populated w/ value *}
{foreach name=outter from=$values item=val}
<div>
<select name="v" class="pvEmpty">
{foreach name=inner from=$field.array item=option}
<option{if $option == $val} selected="selected"{/if}>{$option}</option>
{/foreach}
</select>

<input type="button" value="-" class="fwDelVal" />

</div>
{/foreach}

</td>

{elseif $field.type != 'checkbox'}

<td valign="top" id="fwValue">
<input type="text" name="v" value="{$firstVal}" class="pvEmpty{if $field.type == 'number'} pvNumber{elseif $field.type == 'date'} pvDate{/if}" />
<input type="button" value="+" id="fwAddValue" />

{* If we're editing, add another input populated w/ value *}
{foreach name=outter from=$values item=val}
<div>
<input type="text" name="v" value="{$val}" class="pvEmpty{if $field.type == 'number'} pvNumber{elseif $field.type == 'date'} pvDate{/if}" />

<input type="button" value="-" class="fwDelVal" />

</div>
{/foreach}

</td>

{/if}

</tr>
</tbody>
</table>

<div class="buttons">

<input type="button" value="{if $firstVal}{t}Update{/t}{else}{t}Add{/t}{/if}" id="fwSubmit" />

</div>

{literal}
<script type="text/javascript">
// need these for ie -- IE can't find functions declared here due to scoping??
$('#fwAddValue').click(function() {
	$(this).parent().append('<div></div>');
	e = $('*:first-child', $(this).parent()).get(0);
	$('div:last-child', $(this).parent()).
		append($(e).clone().val('')).
		append(' <input type="button" value="-" />');

	$('div:last-child input[@type=button]', $(this).parent()).oneclick(function() {
		$(this).parent().remove();
		PommoValidate.reset();
		PommoValidate.init('#fwValue input[@name=v]', '#fwSubmit', false);
	});

	PommoValidate.reset();
	PommoValidate.init('#fwValue input[@name=v]', '#fwSubmit', false);
});

$('#fwValue input.fwDelVal').each(function(){
	$(this).oneclick(function() {
		$(this).parent().remove();
		PommoValidate.reset();
		PommoValidate.init('#fwValue input[@name=v]', '#fwSubmit', false);
	});
});

$('#fwSubmit').oneclick(function() {
	var _logic = $('#fwLogic').val();
	var _group = $('#fwGroupID').val();
	var _match = $('#fwMatchID').val();
	var _type = $('#fwRuleType').val();
	var _value = $('#fwValue input[@type=text], #fwValue select').serialize();

	$.post("ajax/rule_update.php",
		{ logic: _logic, group: _group, match: _match, value: _value, type: _type },
		function(out) {
			var name = $('#filterWindow a.fwClose').attr('alt');
			$('#newFilter select').each(function() { $(this).show().val(''); });
			$('#filterWindow').fadeOut(200, function(){$(this).TransferTo({
				to: name,
				className:'fwTransfer',
				duration: 300,
				complete: function() { location.reload(true); }
			})});
		}
	);
});
</script>
{/literal}