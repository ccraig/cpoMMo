<div id="addOut" class="error"></div><div class="warn"></div>

<p>{t}Welcome to user editing.{/t}</p>

<form method="post" action="" id="editForm">


	<fieldset>

	<legend>{t}Edit User{/t}</legend>

		<div class="actioncontainer" style="border: 1px solid silver; background-color:#eeeeee;padding:8px;" >
	
			<div><input type="hidden" name="userid" value="{$userinfo.id}">
				Editing user with ID: {$userinfo.id}
			</div>
			<div>
				<label for="username"><strong class="required">{t}Username:{/t}</strong></label>
				<input class="pvEmpty pvInvalid" size="32" maxlength="60" type="text" name="username" value="{$userinfo.name}"><br>
			</div>
				
			<div>
				(Passwort editing not allowed)
				{*<label for="userpass"><strong class="required">{t}Password:{/t}</strong></label>
				<input class="pvEmpty pvInvalid" size="32" maxlength="60" type="password" name="userpass" value="{$userinfo.pass}"><br>*}
			</div>
			
			<div>
				<label for="usergroup"><strong class="required">{t}Permission Group:{/t}</strong></label>
						<select name="usergroup"><!--TODO --wert-- werte funzen net-->
									<option name="group_name" value="nogroup" {if $userinfo.perm==""}selected{/if}>--no group--</option>
							{foreach key=nr item=groupitem from=$permgroups}
									<option name="group_name" value="{$groupitem.id}" 
										{if $userinfo.perm==$groupitem.name}selected{/if}>{$groupitem.name}</option>
							{/foreach}
						</select>
						Old Permission Group:({$userinfo.perm})
			</div>
		
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
