<div style="margin: 20px 0 20px 5px;">

<form class="json" action="ajax/group.rpc.php?call=addRule" method="post">
<input type="hidden" name="type" value="{$type}" />
<input type="hidden" name="field" value="{$match_id}" />

<select name="logic">
<option value="is_in">{t}Include{/t}</option>
<option value="not_in">{t}Exclude{/t}</option>
</select>

{t escape=no 1="<strong>$match_name</strong>"}members in group %1.{/t}

<div class="buttons" style="padding-bottom: 20px;">
	<button type="submit" value="{t}Add{/t}" class="positive"><img src="{$url.theme.shared}/images/icons/tick.png" alt="add"/>{t}Add{/t}</button>
	<button type="submit" value="{t}Cancel{/t}" class="jqmClose negative"><img src="{$url.theme.shared}/images/icons/cross.png" alt="cancel"/>{t}Cancel{/t}</button>
</div>

</form>
</div>


{literal}
<script type="text/javascript">

$().ready(function(){
	// shrink window
	$('#dialog div.jqmdBC').removeClass('jqmdTall');
});

</script>
{/literal}