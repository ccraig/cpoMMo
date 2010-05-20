{include file="admin/inc.header.tpl"}
{include file="admin/inc.sidebar.tpl"}
	
<div id="mainbar">

	<h1>{t}Message Settings{/t}</h1>
	<img src="{$url.theme.shared}images/icons/settings.png" class="articleimg">

	<p>
		{t}You can configure the messages poMMo sends when users try to subscribe, unsubscribe, or update their records. You can also configure the messages displayed when the user successfully completes this task.{/t}
	</p>
	
	<p>
		<strong>{t}Note:{/t}</strong>
		{t}Using '[[url]]' in the message body will reference the confirmation link.{/t}
	</p>
	
	
	<a href="{$url.base}admin/setup/setup_configure.php">
		<img src="{$url.theme.shared}images/icons/back.png" align="middle" class="navimage" border='0'>
		{t 1=$returnStr}Return to %1{/t}</a>
		
	<h2>{t}Messages{/t} &raquo;</h2>
	
	{if $messages}
    	<div class="msgdisplay">
    	{foreach from=$messages item=msg}
    	<div>* {$msg}</div>
    	{/foreach}
    	</div>
 	{/if}

<form action="" method="POST">

  <fieldset>
    <legend>{t}Subscribe{/t}</legend>

		<div class="field">
			<div class="error">{validate id="subscribe_sub" message=$formError.subscribe_sub}</div>
			<label for="Subscribe_sub"><span class="required">{t}Subject:{/t}</span></label>
			<input type="text" class="text" size="32" maxlength="60"
			  name="Subscribe_sub" value="{$Subscribe_sub|escape}" id="subscribe_sub" />
			<div class="notes">{t}(Subject of Sent Email){/t}</div>
		</div>
		
		<div class="field">
			<div class="error">{validate id="subscribe_msg" message=$formError.subscribe_msg}</div>
			<label for="Subscribe_msg"><span class="required">{t}Message:{/t}</span></label>
			<textarea name="Subscribe_msg" id="subscribe_msg" rows="5">{$Subscribe_msg|escape}</textarea>
			<div class="notes">{t}(Use [[url]] for the confirm link at least once){/t}</div>
		</div>
		
		<div class="field">
			<div class="error">{validate id="subscribe_suc" message=$formError.subscribe_suc}</div>
			<label for="Subscribe_suc"><span class="required">{t}Success:{/t}</span></label>
			<textarea name="Subscribe_suc" id="subscribe_suc" rows="2">{$Subscribe_suc|escape}</textarea>
			<div class="notes">{t}(Message displayed upon success){/t}</div>
		</div>
		
		<div class="eveninputs">
			<input type="submit" value="{t}Update{/t}">
			<input type="submit" name="restore[subscribe]" value="{t}Restore to Defaults{/t}">
		</div>
		
	</fieldset>
	
	<fieldset class="altcolor">
    <legend>{t}Unsubscribe{/t}</legend>

		<div class="field">
			<div class="error">{validate id="unsubscribe_sub" message=$formError.unsubscribe_sub}</div>
			<label for="Unsubscribe_sub"><span class="required">{t}Subject:{/t}</span></label>
			<input type="text" class="text" size="32" maxlength="60"
			  name="Unsubscribe_sub" value="{$Unsubscribe_sub|escape}" id="unsubscribe_sub" />
			<div class="notes">{t}(Subject of Sent Email){/t}</div>
		</div>
		
		<div class="field">
			<div class="error">{validate id="unsubscribe_msg" message=$formError.unsubscribe_msg}</div>
			<label for="Unsubscribe_msg"><span class="required">{t}Message:{/t}</span></label>
			<textarea name="Unsubscribe_msg" id="unsubscribe_msg" rows="5">{$Unsubscribe_msg|escape}</textarea>
			<div class="notes">{t}(Use [[url]] for the confirm link at least once){/t}</div>
		</div>
		
		<div class="field">
			<div class="error">{validate id="unsubscribe_suc" message=$formError.unsubscribe_suc}</div>
			<label for="Unsubscribe_suc"><span class="required">{t}Success:{/t}</span></label>
			<textarea name="Unsubscribe_suc" id="unsubscribe_suc" rows="2">{$Unsubscribe_suc|escape}</textarea>
			<div class="notes">{t}(Message displayed upon success){/t}</div>
		</div>
		
		<div class="eveninputs">
			<input type="submit" value="{t}Update{/t}">
			<input type="submit" name="restore[unsubscribe]" value="{t}Restore to Defaults{/t}">
		</div>
		
	</fieldset>
	
	<fieldset>
    <legend>{t}Update Records{/t}</legend>

		<div class="field">
			<div class="error">{validate id="update_sub" message=$formError.update_sub}</div>
			<label for="Update_sub"><span class="required">{t}Subject:{/t}</span></label>
			<input type="text" class="text" size="32" maxlength="60"
			  name="Update_sub" value="{$Update_sub|escape}" id="update_sub" />
			<div class="notes">{t}(Subject of Sent Email){/t}</div>
		</div>
		
		<div class="field">
			<div class="error">{validate id="update_msg" message=$formError.update_msg}</div>
			<label for="Update_msg"><span class="required">{t}Message:{/t}</span></label>
			<textarea name="Update_msg" id="update_msg" rows="5">{$Update_msg|escape}</textarea>
			<div class="notes">{t}(Use [[url]] for the confirm link at least once){/t}</div>
		</div>
		
		<div class="field">
			<div class="error">{validate id="update_suc" message=$formError.update_suc}</div>
			<label for="Update_suc"><span class="required">{t}Success:{/t}</span></label>
			<textarea name="Update_suc" id="update_suc" rows="2">{$Update_suc|escape}</textarea>
			<div class="notes">{t}(Message displayed upon success){/t}</div>
		</div>
		
		<div class="eveninputs">
			<input type="submit" value="{t}Update{/t}">
			<input type="submit" name="restore[update]" value="{t}Restore to Defaults{/t}">
		</div>
		
	</fieldset>
	
	<fieldset class="altcolor">
    <legend>{t}Change Password{/t}</legend>

		<div class="field">
			<div class="error">{validate id="password_sub" message=$formError.password_sub}</div>
			<label for="Password_sub"><span class="required">{t}Subject:{/t}</span></label>
			<input type="text" class="text" size="32" maxlength="60"
			  name="Password_sub" value="{$Password_sub|escape}" id="password_sub" />
			<div class="notes">{t}(Subject of Sent Email){/t}</div>
		</div>
		
		<div class="field">
			<div class="error">{validate id="password_msg" message=$formError.password_msg}</div>
			<label for="Password_msg"><span class="required">{t}Message:{/t}</span></label>
			<textarea name="Password_msg" id="password_msg" rows="5">{$Password_msg|escape}</textarea>
			<div class="notes">{t}(Use [[url]] for the confirm link at least once){/t}</div>
		</div>
		
		<div class="field">
			<div class="error">{validate id="password_suc" message=$formError.password_suc}</div>
			<label for="Password_suc"><span class="required">{t}Success:{/t}</span></label>
			<textarea name="Password_suc" id="password_suc" rows="2">{$Password_suc|escape}</textarea>
			<div class="notes">{t}(Message displayed upon success){/t}</div>
		</div>
		
		<div class="eveninputs">
			<input type="submit" value="{t}Update{/t}">
			<input type="submit" name="restore[password]" value="{t}Restore to Defaults{/t}">
		</div>
		
	</fieldset>

 </form>
 
</div>
<!-- end mainbar -->
{include file="admin/inc.footer.tpl"}