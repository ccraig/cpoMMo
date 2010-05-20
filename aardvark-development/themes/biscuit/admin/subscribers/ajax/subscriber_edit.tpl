<form class="json validate" action="ajax/manage.rpc.php?call=editSubscriber" method="post" id="edForm">

<div class="output alert"></div>

<fieldset>
<h3>{t}Edit Subscriber{/t}</h3>

<div>
<label for="email"><strong class="required">{t}Email:{/t}</strong></label>
<input type="text" class="pvEmail pvEmpty" size="30" maxlength="60" name="email" />
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
<input type="text" size="30"{if $field.required == 'on'} class="pvEmpty"{/if} name="d[{$key}]" value="{if $field.normally}{$field.normally|escape}{/if}" />
{/if}

</div>

{/foreach}

</fieldset>

<fieldset>
	<input type="checkbox" name="force" />{t}Force (bypasses validation){/t}
</fieldset>

<div class="buttons">
<input type="hidden" name="id" value="0" />
<button type="submit" value="{t}Update Subscriber{/t}" class="positive"><img src="{$url.theme.shared}/images/icons/tick.png" alt="update subscriber"/>{t}Update Subscriber{/t}</button>
</div>

<br class="clear">

<p>{t escape=no 1="<span class=\"required\">" 2="</span>"}%1Bold%2 fields are required.{/t}</p>

</form>

{literal}
<script type="text/javascript">


$().ready(function(){

	poMMo.callback.editSubscriber = function(p) {
		poMMo.grid.setRow(p);
	};
	
	// populate form with first selected row... 
	// TODO; add support for multiple subscriber editing at a time.
	var data = poMMo.grid.getRow();
	var scope = $('#edForm')[0];
	
	for (var i in data)  {
		
		// skip empty values/data
		if($.trim(data[i]) == '')
			continue;
			
		// transform "d#" to "d[#]"
		var name = (i.match(/^d\d+$/)) ? 'd['+i.substr(1)+']' : i;
		$(':input[@name="'+name+'"]',scope).each(function(){
			if($(this).attr('type') == 'checkbox')
				this.checked = (data[i] == 'on') ? true : false;
			else
				$(this).val(""+data[i]+"");
		}); 
	}
	
	$('input[@name="force"]',scope).click(function(){
		if(this.checked)
			$(this).jqvDisable();
		else
			$(this).jqvEnable();
	});
		
});
</script>
{/literal}