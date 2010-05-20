{include file="user/inc.header.tpl"}

<div id="header"><h1>{t}Subscriber Login{/t}</h1></div>

	{t}In order to check your subscribtion status, update your information, or unsubscribe, you must enter your email address in the field below.{/t}
    
    {if $messages}
    	<div class="msgdisplay">
    	{foreach from=$messages item=msg}
    	<div>* {$msg}</div>
    	{/foreach}
    	</div>
 	{/if}

	<form action="" method="POST">
	<fieldset>
		<legend>{t}Login{/t}</legend>
	
		<div class="field">
			<div class="error">{validate id="email" message=$formError.email}</div>
			<label for="Email"><span class="required">{t}Your Email:{/t} </span></label>
			<input type="text" class="text" size="32" maxlength="60"
			  name="Email" value="{$Email|escape}" id="email" />
			  <input style="margin-left: 30px;" type="submit" value="{t}Login{/t}" />
		</div>
		
	</fieldset>
	</form>

{include file="user/inc.footer.tpl"}