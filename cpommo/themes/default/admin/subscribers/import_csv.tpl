{capture name=head}{* used to inject content into the HTML <head> *}
{include file="inc/ui.form.tpl"}
{include file="inc/ui.cssTable.tpl"}
{/capture}
{include file="inc/admin.header.tpl" sidebar='off'}

<ul class="inpage_menu">
<li><a href="subscribers_import.php">{t 1=$returnStr}Return to %1{/t}</a></li>
</ul>

<h2>{t}Import Subscribers{/t}</h2>


<form action="" method="post" id="assign">

<fieldset>
<legend>{t}Assign Fields{/t}</legend>

<div>
{t}Below is a preview of your CSV data. You can assign subscriber fields to columns. At the very least, you must assign an email address.{/t}
</div>

{if $excludeUnsubscribed}<input type="hidden" name="excludeUnsubscribed" value="true" />{/if}
<table summary="{t}Assign Fields{/t}">

<thead>
<tr>

{section name=columns start=0 loop=$colNum }
<th>
&nbsp;<select name="f[{$smarty.section.columns.index}]">
<option value="">{t}Ignore Column{/t}</option>
<option value="">-----------</option>
<option value="email">{t}Email{/t}</option>
<option value="registered">{t}Date Registered{/t}</option>
<option value="ip">{t}IP Address{/t}</option>
<option value="">-----------</option>
{foreach from=$fields item=f key=id}
<option value="{$id}">{$f.name}</option>
{/foreach}
</select>&nbsp;
</th>
{/section}

</tr>
</thead>
<tbody>

{foreach from=$preview item=row}
<tr>
{section name=rows start=0 loop=$colNum }
<td>{if $row[$smarty.section.rows.index]}{$row[$smarty.section.rows.index]}{/if}</td>
{/section}
</tr>
{/foreach}

</tbody>
</table>


</form>


<div class="buttons" id="buttons">
<a href="#" id="import"><button>{t}Import{/t}</button></a>
</div>
</fieldset>


<div id="ajax" class="warn hidden">
<img src="{$url.theme.shared}images/loader.gif" alt="Importing..." />... {t}Processing{/t}
</div>


{literal}
<script type="text/javascript">
$().ready(function(){
	
	// stripe table body rows
	$('table tbody').jqStripe();
	
	$('#import').click(function() {
		
		var input = $('#assign').formToArray();
		var c = false;
		
		for (i in input) {
			if(input[i].value == 'email')
				c = true;
		}
		
		if(!c) {
			alert('{/literal}{t}You must assign an email column!{/t}{literal}');
			return false;
		}
		
		$('#buttons').hide();
		
		$('#ajax').show().load('import_csv2.php',input, function() {
			$('#ajax').removeClass('warn').addClass('error');
		});
		
		return false;
	
	});
});
</script>
{/literal}
{include file="inc/admin.footer.tpl"}