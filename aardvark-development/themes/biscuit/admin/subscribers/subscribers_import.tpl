{include file="inc/admin.header.tpl"}

<h2><img src="{$url.theme.shared}images/icons/import.png" class="navimage left" alt="import subscribers"/>{t}Import Subscribers{/t}</h2>

{t escape=no 1='<tt>' 2='</tt>'}Welcome to Subscriber Import! You can import subscribers from a list of email addresses or from a full fledged CSV file containing subscriber field values. CSV files should have one subscriber (email) per line with field information seperated by commas(%1,%2).{/t} {t escape=no 1='<a href="http://www.openoffice.org/">' 2='</a>'} Popular programs like Microsoft Excel and %1 Open Office%2 support saving files in CSV (Comma-Seperated-Value) format.{/t}

<p class="warn">{t}Duplicate subscribers or invalid email addresses will be ignored.{/t}</p>

<form method="post" enctype="multipart/form-data" action="">
<input type="hidden" name="MAX_FILE_SIZE" value="{$maxSize}" />{* <-- DO NOT CHANGE THE LOCATION OF THIS *}

{include file="inc/messages.tpl"}

<fieldset>
<h3>{t}Import{/t}</h3>

<div>
<label for="type">{t}Type{/t}: </label>
<select name="type" id="type">
<option value="txt">{t}List of Email Addresses{/t}</option>
<option value="csv">{t}.CSV - All subscriber Data{/t}</option>
</select>
</div>
</fieldset>

<fieldset>
<h3>{t}Import Subscribers{/t}</h3>

<div id="file" style="display: none;">
<div><a href="#" title="{t}Type subscribers into a box{/t}">{t}Manually type in subscriber contents{/t}</a></div>
<label for="csvfile">{t}CSV file:{/t}</label>
<input type="file" accept="text/csv" name="csvfile" id="csvfile" maxlength="60" size="25" />
</div>

<div id="box">
<div><a href="#" title="{t}Upload subscribers from a file{/t}">{t}Upload subscribers from a CSV file{/t}</a></div>
<textarea name="box" cols="60" rows="8">{t}Type/Paste in subscriber contents...{/t}</textarea>
</div>

<input type="checkbox" name="excludeUnsubscribed" />{t}Allow unsubscribed emails to be re-subscribed.{/t}

<div class="buttons">
<button type="submit" name="submit" value="{t}Import{/t}" class="positive"><img src="{$url.theme.shared}/images/icons/tick.png" alt="import"/>{t}Import{/t}</button>
</div>

</fieldset>

</form>

{literal}
<script type="text/javascript">
$().ready(function(){

	var box = $('#box textarea');
	var orig = box.val();

	box.focus(function() {
		if ($(this).val() == orig)
			$(this).val("");
	});

	box.blur(function() {
		var val = $(this).val();
		val.replace(/^\s*|\s*$/g,"");
		if (val == "")
			$(this).val(orig);
	});
	
	$('#box a').click(function() { 
		$('#box').hide().find('textarea').val(orig);
		$('#file').show();
		return false;
	});
	
	$('#file a').click(function() { 
		$('#file').hide().find('input').val("");
		$('#box').show();
		return false;
	});
	
	$('#type').change(function() {
		if($(this).val() != 'csv')
			return;
		$('#box').hide().find('textarea').val(orig);
		$('#file').show();
	});

});
</script>
{/literal}

{include file="inc/admin.footer.tpl"}