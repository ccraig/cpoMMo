<div id="addOut" class="error"></div>
<div class="warn"></div>

<p>{t escape='no' 1='<a href="subscribers_import.php">' 2='</a>'}Welcome to adding subscribers! You can add subscribers one-by-one here. If you would like to add subscribers in bulk, visit the %1Subscriber Import%2 page.{/t}</p>

<form method="post" action="" id="addForm">
<fieldset>
<legend>{t}Add Subscriber{/t}</legend>

<div>
<label for="email"><strong class="required">{t}Email:{/t}</strong></label>
<input type="text" class="pvEmail pvEmpty" size="32" maxlength="60" name="Email" />
</div>

{foreach name=fields from=$fields key=key item=field}
<div>
<label for="field{$key}">{if $field.required == 'on'}<strong class="required">{/if}{$field.prompt}:{if $field.required == 'on'}</strong>{/if}</label>

{if $field.type == 'checkbox'}
<input type="checkbox" name="d[{$key}]"{if $field.normally == "on"} checked="checked"{/if}{if $field.required == 'on'} class="pvEmpty"{/if} />

{elseif $field.type == 'multiple'}
<select name="d[{$key}]">
{foreach from=$field.array item=option}
<option{if $field.normally == $option} selected="selected"{/if}>{$option}</option>
{/foreach}
</select>

{elseif $field.type == 'date'}
<input type="text" class="pvDate{if $field.required == 'on'} pvEmpty{/if}" size="12" name="d[{$key}]" value={if $field.normally}"{$field.normally|escape}"{else}"{t}mm/dd/yyyy{/t}"{/if} />

{elseif $field.type == 'number'}
<input type="text" class="pvNumber{if $field.required == 'on'} pvEmpty{/if}" size="12" name="d[{$key}]" value="{if $field.normally}{$field.normally|escape}{/if}" />

{else}
<input type="text" size="32"{if $field.required == 'on'} class="pvEmpty"{/if} name="d[{$key}]" value="{if $field.normally}{$field.normally|escape}{/if}" />
{/if}

</div>

{/foreach}

</fieldset>

<div class="buttons">

<input type="submit" value="{t}Add Subscriber{/t}" />

<input type="reset" value="{t}Reset{/t}" />

</div>

<p><a href="#" id="forceAdd">{t}Force Addition (bypasses validation){/t}</a></p>

<p>{t escape=no 1="<span class=\"required\">" 2="</span>"}%1Fields%2 are required{/t}</p>

</form>

{literal}
<script type="text/javascript">
$().ready(function(){

	$('#addForm').submit(function() {
		var input = $(this).formToArray();

		url = "ajax/subscriber_add2.php";
		if($('#forceAdd').is('.force')) {
			$('#forceAdd').removeClass('force');
			url = url+"?force=TRUE";
		}

		$.post(url, input, function(json) {
			eval("var args = " + json);

			if (typeof(args.success) == 'undefined') {
				alert('ajax error!');
				return;	
			}

			$('#addOut').html(args.msg);

			if (args.success === true) {
				var options = {
					KEY: args.key, 
					CLASS: 'newRow', 
					VALUES: args.data,
					COPY: false 
				}
				jQuery.tableEditor.lib.appendRow(options);
			}
		});

		return false;
	});

	$('#forceAdd').click(function() {
		$(this).addClass('force');
		$('#addForm').submit();
		return false;
	});

	PommoValidate.reset(); // TODO -- validate must be scoped to this ROW. Modify validate.js
	PommoValidate.init('input[@type="text"], input[@type="checkbox"], select','input[@type="submit"]', true, $('#addForm'));
});
</script>
{/literal}