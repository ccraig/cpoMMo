<div id="addOut" class="error"></div>
<div class="warn"></div>

<p>{t}Welcome to the test mailer! Verify a mailing looks correct and that the mailer works by sending a message to yourself.{/t}</p>

{if $msg}<div class="warn">{$msg}</div>{/if}

<form method="post" action="" id="testForm">
<fieldset>
<legend>{t}Recipient{/t}</legend>

<div>
<label class="required" for="email">{t}Email:{/t}</label>
<input type="text" class="pvEmail pvEmpty" size="32" maxlength="60" name="Email" />
<input type="submit" value="{t}Send Mailing{/t}"/>
</div>

</fieldset>

<p>{t escape='no' 1='<strong>' 2='</strong>'}If your mailing includes personalizations, you can %1optionally%2 supply test values{/t}</p>

<fieldset>
<legend>{t}Personalizations{/t}</legend>

{foreach name=fields from=$fields key=key item=field}
<div>
<label{if $field.required == 'on'} class="required"{/if} for="field{$key}">{$field.prompt}:</label>

{if $field.type == 'checkbox'}
<input type="checkbox" name="d[{$key}]"{if $field.normally == "on"} checked="checked"{/if}{if $field.required == 'on'} class="pvEmpty"{/if} />

{elseif $field.type == 'multiple'}
<select name="d[{$key}]">
{foreach from=$field.array item=option}
<option{if $field.normally == $option} selected="selected"{/if}>{$option}</option>
{/foreach}
</select>

{elseif $field.type == 'date'}
<input type="text" class="pvDate{if $field.required == 'on'} pvEmpty{/if}" size="12" name="d[{$key}]" value="{if $field.normally}{$field.normally|escape}{else}{t}mm/dd/yyyy{/t}{/if}" />

{elseif $field.type == 'number'}
<input type="text" class="pvNumber{if $field.required == 'on'} pvEmpty{/if}" size="12" name="d[{$key}]" value="{if $field.normally}{$field.normally|escape}{/if}" />

{else}
<input type="text"{if $field.required == 'on'} class="pvEmpty"{/if} size="32" name="d[{$key}]" value="{if $field.normally}{$field.normally|escape}{/if}" />
{/if}

</div>

{/foreach}

</fieldset>

</form>

{literal}
<script type="text/javascript">
$().ready(function(){

	$('#testForm').submit(function() {
		var input = $(this).formToArray();

		url = "ajax/mailing_test2.php";

		$.post(url, input, function(json) {
			eval("var args = " + json);

			if (typeof(args.success) == 'undefined') {
				alert('ajax error!');
				return;	
			}

			$('#addOut').html(args.msg);

			if(args.success)
				$('#testForm input[@type="submit"]').hide();
		});

		return false;
	});

});
</script>
{/literal}