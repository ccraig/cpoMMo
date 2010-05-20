{capture name=head}{* used to inject content into the HTML <head> *}
{include file="inc/ui.form.tpl"}
{include file="inc/ui.cssTable.tpl"}
{/capture}
{include file="inc/admin.header.tpl" sidebar='off'}

<h2><img src="{$url.theme.shared}images/icons/import.png" class="navimage left" alt="import subscribers"/>{t}Import Subscribers{/t}</h2>&nbsp;&nbsp;&nbsp;<a href="subscribers_import.php">{t 1=$returnStr}Return to %1{/t}</a>

{t escape=no 1='<tt>' 2='</tt>'}Welcome to Subscriber Import! You can import subscribers from a list of email addresses or from a full fledged CSV file containing subscriber field values. CSV files should have one subscriber (email) per line with field information seperated by commas(%1,%2).{/t} {t escape=no 1='<a href="http://www.openoffice.org/">' 2='</a>'} Popular programs like Microsoft Excel and %1 Open Office%2 support saving files in CSV (Comma-Seperated-Value) format.{/t}

<br class="clear"/>

<form action="" method="post" id="assign">

<fieldset>
<h3>{t}Assign Fields{/t}</h3>

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
<a href="#" id="import" class="positive"><img src="{$url.theme.shared}images/icons/tick.png" alt="yes"/>{t}Import{/t}</a>
<a href="subscribers_import.php" class="negative"><img src="{$url.theme.shared}images/icons/cross.png" alt="cancel"/>{t}Cancel{/t}</a>
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