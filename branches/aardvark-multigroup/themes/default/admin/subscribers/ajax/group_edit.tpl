<input type="hidden" id="fwGroupID" name="group_id" value="{$group_id}" />
<input type="hidden" id="fwMatchID" name="match_id" value="{$match_id}" />


<div style="width: 100%; text-align: center; margin: 40px 0; font-size: 130%;">

<select name="logic" id="fwLogic">
<option value="is_in">{t}Include{/t}</option>
<option value="not_in">{t}Exclude{/t}</option>
</select>

{t escape=no 1="<strong>$match_name</strong>"}members in group %1.{/t}

<div>
	<input type="submit" value="{t}Add{/t}" id="fwSubmit" />
	<input type="submit" value="{t}Cancel{/t}" class="jqmClose" />
</div>

</div>


{literal}
<script type="text/javascript">
$('#fwSubmit').one("click", function() {
	var _logic = $('#fwLogic').val();
	var _match = $('#fwMatchID').val();

	$.post("ajax/rule_update.php",
		{ logic: _logic, group: groupID, match: _match },
		function(out) {
			setTimeout("location.reload(true);",1000);
			$('#dialog').jqmHide();
		}
	);
	
	$('#dialog div.jqmdMSG').html(origHTML);
});
</script>
{/literal}