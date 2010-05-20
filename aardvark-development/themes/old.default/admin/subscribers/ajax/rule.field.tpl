<div class="alert">
{if $field.type == 'date'}
	{t}value must be a date{/t} ({$config.app.dateformat})

{elseif $field.type == 'number'}
	{t}value must be a number{/t}

{elseif $field.type == 'text'}
	{t}value must not be blank{/t}
{/if}
</div>

<div style="width: 100%; text-align: center; margin: 15px 0;">
<form class="json validate" action="ajax/group.rpc.php?call=addRule" method="post">
<input type="hidden" name="type" value="{$type}" />
<input type="hidden" name="field" value="{$field.id}" />

{t}Match subscribers where{/t} <strong>{$field.name}</strong>

<select name="logic">
{foreach from=$logic key=val item=desc}
<option value="{$val}">{$desc}</option>
{/foreach}
</select>

{if $field.type != 'checkbox'}
	
	<hr />
	{t}Value(s){/t}
	
	<div id="values">
	
	<div>
	{if $field.type == 'multiple'}
		<select name="match[]" class="pvEmpty">
		{foreach from=$field.array item=option}
		<option{if $option == $firstVal} selected="selected"{/if}>{$option}</option>
		{/foreach}
		</select>
	{else}
		<input type="text" name="match[]" value="{$firstVal}" class="pvEmpty{if $field.type == 'number'} pvNumber{elseif $field.type == 'date'} pvDate{/if}" />
	{/if}
	<input type="submit" value="+" class="addMatch pvSkip" />
	</div>
	
	{* If we're editing, add another input populated w/ value *}
	{foreach name=outter from=$values item=val}
		<div>
		{if $field.type == 'multiple'}
			<select name="match[]" class="pvEmpty">
			{foreach name=inner from=$field.array item=option}
			<option{if $option == $val} selected="selected"{/if}>{$option}</option>
			{/foreach}
			</select>
		{else}
			<input type="text" value="{$val}" name="match[]" class="pvEmpty{if $field.type == 'number'} pvNumber{elseif $field.type == 'date'} pvDate{/if}" />
		{/if}
		<input type="submit" value="-" class="delMatch pvSkip" />
		</div>
	{/foreach}
	
	</div>
	
	<hr />
{/if}

<div>
	<input type="submit" value="{if $firstVal}{t}Update{/t}{else}{t}Add{/t}{/if}" />
	<input type="submit" value="{t}Cancel{/t}" class="jqmClose" />
</div>

</form>
</div>

{literal}
<script type="text/javascript">

$().ready(function(){
	// stretch window
	$('#dialog div.jqmdBC').addClass('jqmdTall');
	
	$('#values input.addMatch').click(function() {
		var add = $(this).parent().clone().find(':input:last').val('-').end();
		$('#values').append(add).find(':input:last')
		.one('click', function() {
			$(this).parent().remove();
			return false;
		});
		$('#dialog form.validate').jqValidate();
		return false;
	});
	
	$('#values input.delMatch').one('click', function() {
		$(this).parent().remove();
		$('#dialog form.validate').jqValidate();
		return false;
	});

});
</script>
{/literal}