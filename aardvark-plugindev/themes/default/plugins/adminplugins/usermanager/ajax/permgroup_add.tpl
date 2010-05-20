
<div id="addOut" class="error"></div><div class="warn"></div>

{*
<p>{t}Welcome to adding permission Groupss! You can add permission groups one-by-one here.{/t}</p>
*}

<form method="post" action="" id="addForm">

	<fieldset>

	<legend>{t}Create Permission Group{/t}</legend>

		<div class="actioncontainer" style="border: 1px solid silver; background-color:#eeeeee;padding:8px;" >


			<div>
				<label for="groupname"><strong class="required">{t}Groupname:{/t}</strong></label>
				<input class="pvEmpty pvInvalid" size="32" maxlength="60" type="text" name="groupname" value="{$groupname}"><br>
			</div>

			{*<div><label for="permlvl"><strong class="required">{t}Permission Level:{/t}</strong></label>
				<select>
					<option name="permlvl" value="1">1</option>
					<option name="permlvl" value="2">2</option>
					<option name="permlvl" value="3">3</option>
					<option name="permlvl" value="4">4</option>
				</select>
			</div>*}
			
			<div>
				<label for="groupdesc"><strong class="required">{t}Group Description:{/t}</strong></label>
				<input class="pvEmpty pvInvalid" size="32" maxlength="60" type="text" name="groupdesc" value="{$groupdesc}"><br>
			</div>
				
			<div>
				<label><strong class="required">{t}Permissions:{/t}</strong></label>
				{*<div>*}


				{**{foreach item=icat key=key from=$perm.cat}
					Categories{$icat}
				{/foreach}**}


				{foreach item=item key=key from=$perm}
					
					{if $item.cat!=$mom}
						{assign var="mom" value=$item.cat}
						<div style="border:1px solid black;">{$mom}<br>
					{/if}
						
					<input type="checkbox" class="pvEmpty" name="groupperm[{$key}]" value="{$item.id}"><label for="groupperm[{$key}]">{$item.name} [id: {$item.id}]</label><br>
					
					{if $item.cat!=$mom}
						</div></div>
					{/if}
					
				{/foreach}
				{*</div>*}
			</div>
			
	
		</div>


	</fieldset>

	<div class="buttons">
		<input type="submit" name="AddGroup" value="{t}Create Permission Group{/t}" />
		<input type="reset" name="reset" value="{t}Reset{/t}" />
	</div>

	<p>{t escape=no 1="<span class=\"required\">" 2="</span>"}%1Fields%2 are required{/t}</p>

</form>


{literal}
<script type="text/javascript">
$().ready(function(){

	$('#addForm').submit(function() {
		var input = $(this).formToArray();

		url = "ajax/usecasehandler.php";
	
		//alert('abgeschickt');
		
	
	});

});
</script>
{/literal}
