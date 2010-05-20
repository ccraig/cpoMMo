
<div id="addOut" class="error"></div><div class="warn"></div>

<p>{t}Welcome to adding users! You can add users one-by-one here.{/t}</p>

<form method="post" action="" id="addForm">

	<fieldset>

	<legend>{t}Add User{/t}</legend>

		<div class="actioncontainer" style="border: 1px solid silver; background-color:#eeeeee;padding:8px;" >
	
			<div>
				<label for="username"><strong class="required">{t}List name:{/t}</strong></label>
				<input class="pvEmpty pvInvalid" size="32" maxlength="60" type="text" name="username" value="{$username}"><br>
			</div>
				
			<div>
				<label for="userpass"><strong class="required">{t}Description:{/t}</strong></label>
				<input class="pvEmpty pvInvalid" size="200" maxlength="400" type="password" name="userpass" value="{$userpass}"><br>
			</div>
			
			<div>
				<label for="userpasscheck"><strong class="required">{t}Retype password:{/t}</strong></label>
				<input class="pvEmpty pvInvalid" size="32" maxlength="60" type="password" name="userpasscheck" value="{$userpasscheck}"><br>
			</div>
			
			<div>
				<label for="usergroup"><strong class="required">{t}Permission Group:{/t}</strong></label>
					<select name="usergroup" class="pvEmpty pvInvalid">
						<!--<option>--Select permission group--</option>-->
						{foreach key=nr item=groupitem from=$permgroups}
								<option name="name" value="{$groupitem.id}">{$groupitem.name}</option>
						{/foreach}
								
					</select>
			</div>
		
			{* Created is generated in DB, last_login will be written in the future *}		

		</div>





{****<div>
<label for="email"><strong class="required">{t}Email:{/t}</strong></label>
<input type="text" class="pvEmail pvEmpty" size="32" maxlength="60" name="Email" />
</div>
{foreach name=fields from=$fields key=key item=field}
<div>
<label for="field{$key}">{if $field.required == 'on'}<strong class="required">{/if}{$field.prompt}:{if $field.required == 'on'}</strong>{/if}</label>
{if $field.type == 'checkbox'}
<input type="checkbox" name="d[{$key}]"{if $field.normally == "on"} checked="checked"{/if}{if $field.required == 'on'} class="pvEmpty"{/if} />
{elseif $field.type == 'multiple'}
<select name="d[{$key}]">
{foreach from=$field.array item=option}
<option{if $field.normally == $option} selected="selected"{/if}>{$option}</option>
{/foreach}
</select>
{elseif $field.type == 'date'}
<input type="text" class="pvDate{if $field.required == 'on'} pvEmpty{/if}" size="12" name="d[{$key}]" value={if $field.normally}"{$field.normally|escape}"{else}"{t}mm/dd/yyyy{/t}"{/if} />
{elseif $field.type == 'number'}
<input type="text" class="pvNumber{if $field.required == 'on'} pvEmpty{/if}" size="12" name="d[{$key}]" value="{if $field.normally}{$field.normally|escape}{/if}" />
{else}
<input type="text" size="32"{if $field.required == 'on'} class="pvEmpty"{/if} name="d[{$key}]" value="{if $field.normally}{$field.normally|escape}{/if}" />
{/if}
</div>
{/foreach}******}

	</fieldset>

	<div class="buttons">
		<input type="submit" name="AddUser" value="{t}Add User{/t}" />
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
