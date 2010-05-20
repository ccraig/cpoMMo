<p>{t}Welcome to subscriber export!{/t}

<form method="post" action="ajax/subscriber_export2.php" id="pForm">
<fieldset>
<legend>{t}Export Subscribers{/t}</legend>

<div>
<label for="emails"><strong class="required">{t}Export Type{/t}:</strong></label>
<select name="type">
<option value="txt">{t}.TXT - Only Email Addresses{/t}</option>
<option value="csv">{t}.CSV - All subscriber Data{/t}</option>
</select>
</div>

<div>
<label for="emails"><strong class="required">{t}Export{/t}:</strong></label>
<select name="who">
<option value="all">{t}All Pages{/t}</option>
<option value="cur">{t}Current Page{/t}</option>
</select>
</div>

<div id="extended" class="hidden">
<label for="registered">{t}Include{/t} {t}Date Registered{/t}</label>
<input type="checkbox" name="registered" value="true" />

&nbsp;&nbsp;

<label for="ip">{t}Include{/t} {t}IP Address{/t}</label>
<input type="checkbox" name="ip" value="true" />
</div>

</fieldset>

<div class="buttons">

<input type="hidden" name="ids" id="ids" value="" />
<input type="submit" value="{t}Export Subscribers{/t}" />

</div>

</form>

{literal}
<script type="text/javascript">
$().ready(function(){

	$('#pForm select[@name=type]').change(function() {
		if (this.value == 'txt')
			$('#extended').hide();
		else
			$('#extended').show();
	});


	$('#pForm').submit(function() {

		var visible = new Array();
		if ($("select[@name='who']", this).val() == 'cur')
			visible = $('#grid').getDataIDs()
			
		$('#ids',this).val(visible.join());
		return true;
	});

});
</script>
{/literal}