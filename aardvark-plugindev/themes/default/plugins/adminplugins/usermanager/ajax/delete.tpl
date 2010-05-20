
<div id="addOut" class="error"></div><div class="warn"></div>

<form method="post" action="" id="deleteForm">

	<fieldset>

	{if $deluser}
		<legend>{t}Delete User{/t}</legend>
		<div><b>Do you really want to delete this user from the database.</b></div>
		<div class="actioncontainer" style="border: 1px solid silver; background-color:#eeeeee;padding:8px;" >
		<input type="hidden" name="userid" value="{$userid}">
			<div>ID: {$info.id}</div>
			<div>Username: {$info.name}</div>
			<div>Group: {$info.perm}</div>
		</div>
		<div class="buttons">
			<input type="submit" name="DeleteUser" value="{t}Delete User{/t}" />
		</div>
	{elseif $delgroup}
		<legend>{t}Delete Permission group{/t}</legend>
		<div><b>Do you really want to delete this permission group from the database.</b></div>
		<div>Note that users from this group will have no group after deletion.</div>
		<div class="actioncontainer" style="border: 1px solid silver; background-color:#eeeeee;padding:8px;" >
		<input type="hidden" name="groupid" value="{$groupid}">
			<div>ID: {$info.id}</div>
			<div>Username: {$info.name}</div>
			<div>Group: {$info.desc}</div>
		</div>
		<div class="buttons">
			<input type="submit" name="DeleteGroup" value="{t}Delete group{/t}" />
		</div>
	{else}
		<div>Error.</div>
	{/if}

	</fieldset>

</form>


{literal}
<script type="text/javascript">
$().ready(function(){

	$('#deleteForm').submit(function() {
		var input = $(this).formToArray();

		url = "ajax/usecasehandler.php";
	
		//alert('abgeschickt');
		
	
	});

});
</script>
{/literal}
