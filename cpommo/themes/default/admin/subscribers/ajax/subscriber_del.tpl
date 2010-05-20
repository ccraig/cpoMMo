<p>{t escape='no'}Enter email addresses of subscribers in the box below. Seperate emails with commas, spaces, or line breaks.{/t}</p>

<form class="json" action="ajax/manage.rpc.php?call=delSubscriber" method="post">

<div class="output alert"></div>

<fieldset>
<legend>{t}Remove Subscribers{/t}</legend>

<div>
<label for="emails"><strong class="required">{t}Email Addresses:{/t}</strong></label>
<textarea name="emails" cols="40" rows="8">{t}Enter Emails...{/t}</textarea>
</div>

</fieldset>

<div class="buttons">

<input type="submit" value="{t}Remove Subscribers{/t}" />

<input type="hidden" name="status" value="{$status}" />

<input type="reset" value="{t}Reset{/t}" />

</div>

</form>

{literal}
<script type="text/javascript">
$().ready(function(){

	var box = $('textarea[@name=emails]');
	var orig = box.val();
	
	poMMo.callback.delSubscriber = function(ids) {
		poMMo.grid.delRow(ids);
		box.val("");
	};

	box.focus(function() {
		if ($(this).val() == orig)
			$(this).val("");
	});

	box.blur(function() {
		if($.trim($(this).val()) == '')
			$(this).val(orig);
	});
	
	var rows = poMMo.grid.getRowIDs();
	if(rows) {
		var emails = new Array();
		var row = null;
		for (i=0; i<rows.length; i++) {
			row = poMMo.grid.getRow(rows[i]);
			emails.push(row.email);
		}
		box.val(emails.join("\n"));
	}
});
</script>
{/literal}