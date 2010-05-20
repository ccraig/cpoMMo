<div id="addOut" class="error"></div><div class="warn"></div>

{*<p>{t}Welcome to permission group editing.{/t}</*}

<form method="post" action="" id="editForm">


	<fieldset>

	<legend>{t}Edit Permission Group{/t}</legend>

		<div class="actioncontainer" style="border: 1px solid silver; background-color:#eeeeee;padding:8px;" >
	
			<div>{*<input type="hidden" name="groupid" value="{$groupid}">*}
				Editing permission group with ID: {$groupid}
			</div>
			
			{$group}/{$perm}
			<div>
				<label for="groupname"><strong class="required">{t}Group Name:{/t}</strong></label>
				<input class="pvEmpty pvInvalid" size="32" maxlength="60" type="text" name="groupname" value="{$group.name}"><br>
			</div>
			<div>
				<label for="groupdesc"><strong class="required">{t}Group Description:{/t}</strong></label>
				<input class="pvEmpty pvInvalid" size="200" maxlength="400" type="text" name="groupdesc" value="{$group.desc}"><br>
			</div>
	
			
			{foreach key=key item=item from=$perm}
			<div>
				<input type="checkbox" name="{$item.name}" value="">{$item.name} ({$item.id})
			</div>
			{/foreach}
				
		</div>


	</fieldset>

	<div class="buttons">
		<input type="submit" name="EditUser" value="{t}Edit User{/t}" />
		<input type="reset" name="reset" value="{t}Reset{/t}" />
	</div>

	<p>{t escape=no 1="<span class=\"required\">" 2="</span>"}%1Fields%2 are required{/t}</p>

</form>


{literal}
<script type="text/javascript">
$().ready(function(){

	$('#editForm').submit(function() {
		var input = $(this).formToArray();

		url = "ajax/usecasehandler.php";
	
		//alert('abgeschickt');
		
	
	});

});
</script>
{/literal}
