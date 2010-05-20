
<div class="alert">
{if $field.type == 'date'}
	{t}value must be a date (mm/dd/yyyy){/t}

{elseif $field.type == 'number'}
	{t}value must be a number{/t}

{elseif $field.type == 'text'}
	{t}value must not be blank{/t}
{/if}
</div>

<div style="width: 100%; text-align: center; margin: 15px 0;">


{t}Match subscribers where{/t} <strong>{$field.name}</strong>

<select name="logic" id="fwLogic">
{foreach from=$logic key=val item=desc}
<option value="{$val}">{$desc}</option>
{/foreach}
</select>

{if $field.type == 'multiple'}
	<hr />
	{t}Value(s){/t}
	
	<div id="values">

	<div>
		<select name="v" class="pV pvEmpty">
		{foreach from=$field.array item=option}
		<option{if $option == $firstVal} selected="selected"{/if}>{$option}</option>
		{/foreach}
		</select>
		
		<input type="submit" value="+" class="fwAddValue" />
	</div>
	
	{* If we're editing, add another input populated w/ value *}
	{foreach name=outter from=$values item=val}
		<div>
			<select name="v" class="pV pvEmpty">
			{foreach name=inner from=$field.array item=option}
			<option{if $option == $val} selected="selected"{/if}>{$option}</option>
			{/foreach}
			</select>
			
			<input type="submit" value="-" class="fwDelVal" />
		</div>
	{/foreach}
	
	</div>
	
	<hr />
{elseif $field.type != 'checkbox'}
	<hr />
	{t}Value(s){/t}
	
	<div id="values">
	
	<div>
	<input type="text" name="v" value="{$firstVal}" class="pV pvEmpty{if $field.type == 'number'} pvNumber{elseif $field.type == 'date'} pvDate{/if}" />
	<input type="submit" value="+" class="fwAddValue" />
	</div>
	
	
	{* If we're editing, add another input populated w/ value *}
	{foreach name=outter from=$values item=val}
		<div>
		<input type="text" value="{$val}" name="v" class="pV pvEmpty{if $field.type == 'number'} pvNumber{elseif $field.type == 'date'} pvDate{/if}" />
		<input type="submit" value="-" class="fwDelVal" />
		</div>
	{/foreach}
	
	</div>
	
	<hr />
{/if}

<div>
	<input type="submit" value="{if $firstVal}{t}Update{/t}{else}{t}Add{/t}{/if}" id="fwSubmit" />
	<input type="submit" value="{t}Cancel{/t}" class="jqmClose" />
</div>

</div>


{literal}
<script type="text/javascript">

// stretch window
$('#dialog div.jqmdBC').addClass('jqmdTall');

// apply validation
PommoValidate.reset();
PommoValidate.init('#values .pV', '#fwSubmit', false);

$('#values input.fwAddValue').click(function() {
	var add = $(this).parent().clone().find(':input:last').val('-').end();
	$('#values').append(add).find(':input:last')
		.one('click', function() {
			$(this).parent().remove();
			PommoValidate.reset();
			PommoValidate.init('#values .pV', '#fwSubmit', false);
			return false;
		});
		
	PommoValidate.reset();
	PommoValidate.init('#values .pV', '#fwSubmit', false);
	return false;
});

$('#values input.fwDelVal').one('click', function() {
	$(this).parent().remove();
	PommoValidate.reset();
	PommoValidate.init('#values .pV', '#fwSubmit', false);
	return false;
});

$('#fwSubmit').one("click",function() {
	var _logic = $('#fwLogic').val();
	var _group = groupID;
	var _match = fieldID;
	var _type = ruleType;
	var _value = $('#values .pV').serialize();

	$.post("ajax/rule_update.php",
		{ logic: _logic, group: _group, match: _match, value: _value, type: _type },
		function(out) {
			setTimeout("location.reload(true);",1000);
			$('#dialog').jqmHide();
		});
});
</script>
{/literal}