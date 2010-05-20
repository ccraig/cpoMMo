<input type="hidden" id="fwGroupID" name="group_id" value="{$group_id}" />
<input type="hidden" id="fwMatchID" name="match_id" value="{$match_id}" />

<div>{t}Add new rule:{/t}</div>

<select name="logic" id="fwLogic">
<option value="is_in">{t}Include{/t}</option>
<option value="not_in">{t}Exclude{/t}</option>
</select>

{t 1=$match_name}subscribers belonging to group %1{/t}

<div class="buttons">

<input type="button" value="{t}Add{/t}" id="fwSubmit" />

</div>

{literal}
<script type="text/javascript">
$('#fwSubmit').oneclick(function() {
	var _logic = $('#fwLogic').val();
	var _group = $('#fwGroupID').val();
	var _match = $('#fwMatchID').val();

	$.post("ajax/rule_update.php",
		{ logic: _logic, group: _group, match: _match },
		function(out) {
			var name = $('#filterWindow a.fwClose').attr('alt');
			$('#newFilter select').each(function() { $(this).show().val(''); });
			$('#filterWindow').fadeOut(200, function(){$(this).TransferTo({
				to: name,
				className:'fwTransfer',
				duration: 300,
				complete: function() { location.reload(true); }
			})});
		}
	);
});
</script>
{/literal}