<div id="addOut" class="error"></div>
<div class="warn"></div>

<p>{t escape='no'}Enter email addresses of subscribers in the box below. Seperate emails with commas, spaces, or line breaks. Also, you can auto populate the box with subscribers from the current view.{/t}</p>

<form method="post" action="" id="delForm">

<fieldset>
<legend>{t}Remove Subscribers{/t}</legend>

<div>
<label for="emails"><strong class="required">{t}Email Addresses:{/t}</strong></label>
<textarea name="emails" cols="40" rows="8">{t}Enter Emails...{/t}</textarea>
</div>

</fieldset>

<div class="buttons">

<input type="submit" value="{t}Remove Subscribers{/t}" />

<input type="reset" value="{t}Reset{/t}" />

</div>

<p><a href="#" id="autoFill">{t}Fill box with shown subscribers!{/t}</a></p>

</form>

{literal}
<script type="text/javascript">
$().ready(function(){

	var box = $('#delForm textarea');
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

	$('#autoFill').click(function() {
		box.val("");
		var emails = new Array();

		$('#subs tbody/tr:visible').find('td:eq(1)').each(function() {
			emails.push($(this).html());
		});

		box.val(emails.join("\n"));

		return false;
	});

	$('#delForm').submit(function() {
		var val = box.val();
		val.replace(/^\s*|\s*$/g,"");
		if (val == "" || val == orig)
			return false;

		var input = $(this).formToArray();

		url = "ajax/subscriber_del2.php";

		$.post(url, input, function(json) {
			eval("var args = " + json);

			if (typeof(args.success) == 'undefined') {
				alert('ajax error!');
				return;	
			}

			$('#addOut').html(args.msg);

			if (args.success === true) {
				box.val("");			
				jQuery.tableEditor.lib.deleteRow({KEY: args.ids});
			}
		});

		return false;
	});

});
</script>
{/literal}