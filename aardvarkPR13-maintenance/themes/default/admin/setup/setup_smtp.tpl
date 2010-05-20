{include file="admin/inc.header.tpl"}
{include file="admin/inc.sidebar.tpl"}
	
<div id="mainbar">

	<h1>{t}Configure{/t}</h1>
	<img src="{$url.theme.shared}images/icons/settings.png" class="articleimg">

	<p>
		{t}poMMo can relay mail through up to 4 SMTP servers simutaneously. Throttle settings can either be shared or individually controlled per SMTP relay (for maximum thoroughput).{/t}
	</p>
	
	<a href="{$url.base}admin/setup/setup_configure.php">
		<img src="{$url.theme.shared}images/icons/back.png" align="middle" class="navimage" border='0'>
		{t 1=$returnStr}Return to %1{/t}</a>
		
	<h2>{t}SMTP Relays{/t} &raquo;</h2>
	
	{if $messages}
    	<div class="msgdisplay">
    	{foreach from=$messages item=msg}
    	<div>* {$msg}</div>
    	{/foreach}
    	</div>
 	{/if}

<form action="" method="POST" id="form" name="form">


  <fieldset>
    <legend>{t}SMTP Throttling{/t}</legend>

		<div class="field">
			<label for="throttle_SMTP">{t}Throttle Controller:{/t} </label>
			<select name="throttle_SMTP" id="throttle_SMTP" onChange="document.form.submit()">
				<option value="individual"{if $throttle_SMTP == 'individual'} SELECTED{/if}>
					{t}Individual Throttler per Server{/t}
				</option>
				<option value="shared"{if $throttle_SMTP == 'shared'} SELECTED{/if}>
					{t}Share a Global Throttler{/t}
				</option>
			</select>
			<div class="notes">{t}(throttle control can be shared or individual){/t}</div>
		</div>
	</fieldset>
	
	{foreach from=$smtpStatus key=id item=status}
	
		<fieldset>
		   <legend>{t 1=$id}SMTP #%1{/t}</legend>
		 
		 	<div class="field">
		 		<label>{t}SMTP Status:{/t} </label>
		 		<span style="margin-left: 5px;">
		 			{if $status}
		 				<img src="{$url.theme.shared}images/icons/ok.png" align="absmiddle">{t}Connected to SMTP server{/t}
		 			{else}
		 				<img src="{$url.theme.shared}images/icons/nok.png" align="absmiddle">{t}Unable to connect to SMTP server{/t}
		 			{/if}
		 		</span>
		 	</div>
		 	
		 	<div class="field">
				<label for="host[{$id}]">{t}SMTP Host:{/t} </label>
				<input type="text" class="text" size="32" maxlength="60"
				  name="host[{$id}]" id="host[{$id}]" value="{$smtp[$id].host|escape}"  />
				<div class="notes">{t}(IP Address or Name of SMTP server){/t}</div>
			</div>
			
			<div class="field">
				<label for="port[{$id}]">{t}Port Number:{/t} </label>
				<input type="text" class="text" size="32" maxlength="60"
				  name="port[{$id}]" id="port[{$id}]" value="{$smtp[$id].port|escape}"  />
				<div class="notes">{t}(Port # of SMTP server [usually 25]){/t}</div>
			</div>
	
			<div class="field">
				<label for="auth[1]">{t}SMTP Authentication:{/t} </label>
				<input type="radio" name="auth[{$id}]" value="on" {if $smtp[$id].auth == 'on'}checked{/if}>on
				<input type="radio" name="auth[{$id}]" value="off" {if $smtp[$id].auth != 'on'}checked{/if}>off
				<div class="notes">{t}(Toggle SMTP Authentication [usually off]){/t}</div>
			</div>
			
			<div class="field">
				<label for="user[{$id}]">{t}SMTP Username:{/t} </label>
				<input type="text" class="text" size="32" maxlength="60"
				  name="user[{$id}]" id="user[{$id}]" value="{$smtp[$id].user|escape}"  />
				<div class="notes">{t}(optional){/t}</div>
			</div>
			
			<div class="field">
				<label for="pass[{$id}]">{t}SMTP Password:{/t} </label>
				<input type="text" class="text" size="32" maxlength="60"
				  name="pass[{$id}]" id="pass[{$id}]" value="{$smtp[$id].pass|escape}"  />
				<div class="notes">{t}(optional){/t}</div>
			</div>

			<div class="field">
				<input type="submit" name="updateSmtpServer[{$id}]" id="updateSmtpServer[{$id}]" 
					value="{t 1=$id}Update Relay #%1{/t}">
					<span style="margin-left: 30px;">
					{if $id == 1}
						{t}This is your default relay{/t}
					{else}
						<input type="submit" name="deleteSmtpServer[{$id}]" id="deleteSmtpServer[{$id}]" value="{t 1=$id}Remove Relay #%1{/t}">
					{/if}
					</span>	
			</div>
		</fieldset>
	{/foreach}
	
	{if $addServer}
	<div class="field">
		<input type="submit" name="addSmtpServer[{$addServer}]" id="addSmtpServer[{$addServer}]" 
		value="{t}Add Another Relay{/t}">
	</div>
	{/if}
	
 </form>

</div>
<!-- end mainbar -->
{include file="admin/inc.footer.tpl"}