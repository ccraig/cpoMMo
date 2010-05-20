<div style="width: 100%; text-align: center; margin: 40px 0; font-size: 130%;">

<form class="json" action="ajax/group.rpc.php?call=addRule" method="post">
<input type="hidden" name="type" value="{$type}" />
<input type="hidden" name="field" value="{$match_id}" />

<select name="logic">
<option value="is_in">{t}Include{/t}</option>
<option value="not_in">{t}Exclude{/t}</option>
</select>

{t escape=no 1="<strong>$match_name</strong>"}members in group %1.{/t}

<div>
	<input type="submit" value="{t}Add{/t}" />
	<input type="submit" value="{t}Cancel{/t}" class="jqmClose" />
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