<p>{t escape='no' 1='<a href="subscribers_import.php">' 2='</a>'}Welcome to adding subscribers! You can add subscribers one-by-one here. If you would like to add subscribers in bulk, visit the %1Subscriber Import%2 page.{/t}</p>

<form class="json validate" action="ajax/manage.rpc.php?call=addSubscriber" method="post">

<div class="output alert"></div>

<fieldset>
<legend>{t}Add Subscriber{/t}</legend>

<div>
<label for="email"><strong class="required">{t}Email:{/t}</strong></label>
<input type="text" class="pvEmail pvEmpty" size="32" maxlength="60" name="Email" />
</div>

{foreach name=fields from=$fields key=key item=field}
<div>
<label for="field{$key}">{if $field.required == 'on'}<strong class="required">{/if}{$field.name}:{if $field.required == 'on'}</strong>{/if}</label>

{if $field.type == 'checkbox'}
<input type="checkbox" name="d[{$key}]"{if $field.normally == "on"} checked="checked"{/if}{if $field.required == 'on'} class="pvEmpty"{/if} />

{elseif $field.type == 'multiple'}
<select name="d[{$key}]">
{foreach from=$field.array item=option}
<option{if $field.normally == $option} selected="selected"{/if}>{$option}</option>
{/foreach}
</select>

{elseif $field.type == 'date'}
<input type="text" class="pvDate{if $field.required == 'on'} pvEmpty{/if}" size="12" name="d[{$key}]" value={if $field.normally}"{$field.normally|escape}"{else}"{$config.app.dateformat}"{/if} />

{elseif $field.type == 'number'}
<input type="text" class="pvNumber{if $field.required == 'on'} pvEmpty{/if}" size="12" name="d[{$key}]" value="{if $field.normally}{$field.normally|escape}{/if}" />

{else}
<input type="text" size="32"{if $field.required == 'on'} class="pvEmpty"{/if} name="d[{$key}]" value="{if $field.normally}{$field.normally|escape}{/if}" />
{/if}

</div>

{/foreach}

</fieldset>

<fieldset>
	<input type="checkbox" name="force" />{t}Force (bypasses validation){/t}
</fieldset>

<div class="buttons">

<input type="submit" value="{t}Add Subscriber{/t}" />

<input type="reset" value="{t}Reset{/t}" />

</div>

<p>{t escape=no 1="<span class=\"required\">" 2="</span>"}Fields marked like %1 this %2 are required.{/t}</p>

</form>

{literal}
<script type="text/javascript">
$().ready(function(){

	poMMo.callback.addSubscriber = function(json) {
		if($('#grid').size() == 0)
        	history.go(0); // refresh the page if no grid exists, else add new subscriber to grid
        else
        	poMMo.grid.addRow(json.key,json);
	};
	
	$('input[@name="force"]').click(function(){
		if(this.checked)
			$(this).jqvDisable();
		else
			$(this).jqvEnable();
	});

});
</script>
{/literal}