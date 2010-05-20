{include file="admin/inc.header.tpl"}
{include file="admin/inc.sidebar.tpl"}

<div id="mainbar">

	<h1>{t}Configure{/t}</h1>
	<img src="{$url.theme.shared}images/icons/settings.png" class="articleimg">

	<p>
		{t}Use this page to configure poMMo. You can change the login information, set website and mailing list parameters, end enable demonstration mode. If you enable demonstration mode, no emails will be sent from the system.{/t}
	</p>

	{if $messages}
    	<div class="msgdisplay">
    	{foreach from=$messages item=msg}
    	<div>* {$msg}</div>
    	{/foreach}
    	</div>
 	{/if}

<form action="" method="POST">

  <fieldset>
    <legend>{t}Administrative{/t}</legend>

		<div class="field">
			<div class="error">{validate id="admin_username" message=$formError.admin_username}</div>
			<label for="admin_username"><span class="required">{t}Administrator Username:{/t} </span></label>
			<input type="text" class="text" size="32" maxlength="60"
			  name="admin_username" value="{$admin_username|escape}" id="admin_username" />
			<div class="notes">{t}(you will use this to login){/t}</div>
		</div>

		<div class="field">
			<label for="admin_password">{t}Administrator Password:{/t} </label>
			<input type="text" class="text" size="32" maxlength="60"
			  name="admin_password" value="{$admin_password|escape}" id="admin_password" />
			<div class="notes">{t}(you will use this to login){/t}</div>
		</div>

		<div class="field">
			<div class="error">{validate id="admin_password2" message=$formError.admin_password2}</div>
			<label for="admin_password2">{t}Verify Password:{/t} </label>
			<input type="text" class="text" size="32" maxlength="60"
			  name="admin_password2" value="{$admin_password2|escape}" id="admin_password2" />
			<div class="notes">{t}(enter password again){/t}</div>
		</div>

		<div class="field">
			<div class="error">{validate id="admin_email" message=$formError.admin_email}</div>
			<label for="admin_email"><span class="required">{t}Administrator Email:{/t}</span></label>
			<input type="text" class="text" size="32" maxlength="60"
			  name="admin_email" value="{$admin_email|escape}" id="admin_email" />
			<div class="notes">{t}(email address of administrator){/t}</div>
		</div>

	</fieldset>

	<fieldset>
    <legend>{t}Website{/t}</legend>

		<div class="field">
			<div class="error">{validate id="site_name" message=$formError.site_name}</div>
			<label for="site_name"><span class="required">{t}Website Name:{/t}</span> </label>
			<input type="text" class="text" size="32" maxlength="60"
			  name="site_name" value="{$site_name|escape}" id="site_name" />
			<div class="notes">{t}(The name of your Website){/t}</div>
		</div>

		<div class="field">
			<div class="error">{validate id="site_url" message=$formError.site_url}</div>
			<label for="site_url"><span class="required">{t}Website URL:{/t}</span> </label>
			<input type="text" class="text" size="32" maxlength="60"
			  name="site_url" value="{$site_url|escape}" id="site_url" />
			<div class="notes">{t}(Web address of your Website){/t}</div>
		</div>

		<div class="field">
			<div class="error">{validate id="site_success" message=$formError.site_success}</div>
			<label for="site_success">{t}Success URL:{/t} </label>
			<input type="text" class="text" size="32" maxlength="60"
			  name="site_success" value="{$site_success|escape}" id="site_success" />
			<div class="notes">{t}(Webpage users will see upon successfull subscription. Leave blank to display default welcome page.){/t}</div>
		</div>

		<div class="field">
			<div class="error">{validate id="site_confirm" message=$formError.site_confirm}</div>
			<label for="site_confirm">{t}Confirm URL:{/t} </label>
			<input type="text" class="text" size="32" maxlength="60"
			  name="site_confirm" value="{$site_confirm|escape}" id="site_confirm" />
			<div class="notes">{t}(Webpage users will see upon subscription attempt. Leave blank to display default confirmation page.){/t}</div>
		</div>

	</fieldset>

	<div style="margin-left: 15%; margin-top: 15px; margin-bottom: 15px;">
		<input class="button" type="submit" value="{t}Update{/t}" />
	</div>

	<fieldset>
   	<legend>{t}Mailing List{/t}</legend>

		<div class="field">
			<label for="demo_mode">{t}Demonstration Mode:{/t} </label>
			<input type="radio" name="demo_mode" value="on" {if $demo_mode == 'on'}checked{/if}>on
			<input type="radio" name="demo_mode" value="off" {if $demo_mode != 'on'}checked{/if}>off
			<div class="notes">{t}(Toggle Demonstration Mode){/t}</div>
		</div>

		<div class="field">
			<label for="list_confirm">{t}Email Confirmation:{/t} </label>
			<input type="radio" name="list_confirm" value="on" {if $list_confirm == 'on'}checked{/if}>on
			<input type="radio" name="list_confirm" value="off" {if $list_confirm != 'on'}checked{/if}>off
			<div class="notes">{t}(Set to validate email upon subscription attempt.){/t}</div>
		</div>

		<div class="field">
			<div class="error">{validate id="list_name" message=$formError.list_name}</div>
			<label for="list_name"><span class="required">{t}List Name:{/t}</span> </label>
			<input type="text" class="text" size="32" maxlength="60"
			  name="list_name" value="{$list_name|escape}" id="list_name" />
			<div class="notes">{t}(The name of your Mailing List){/t}</div>
		</div>

		<div class="field">
			<div class="error">{validate id="list_fromname" message=$formError.list_fromname}</div>
			<label for="list_fromname"><span class="required">{t}From Name:{/t}</span> </label>
			<input type="text" class="text" size="32" maxlength="60"
			  name="list_fromname" value="{$list_fromname|escape}" id="list_fromname" />
			<div class="notes">{t}(Default name mails will be sent from){/t}</div>
		</div>

		<div class="field">
			<div class="error">{validate id="list_fromemail" message=$formError.list_fromemail}</div>
			<label for="list_fromemail"><span class="required">{t}From Email:{/t}</span> </label>
			<input type="text" class="text" size="32" maxlength="60"
			  name="list_fromemail" value="{$list_fromemail|escape}" id="list_fromemail" />
			<div class="notes">{t}(Default email mails will be sent from){/t}</div>
		</div>

		<div class="field">
			<div class="error">{validate id="list_frombounce" message=$formError.list_frombounce}</div>
			<label for="list_frombounce"><span class="required">{t}Bounce Address:{/t}</span> </label>
			<input type="text" class="text" size="32" maxlength="60"
			  name="list_frombounce" value="{$list_frombounce|escape}" id="list_frombounce" />
			<div class="notes">{t}(Returned emails will be sent to this address){/t}</div>
		</div>

	</fieldset>

	<fieldset>
    <legend>{t}Advanced{/t}</legend>

		<div class="field">
			<label for="list_charset"><span class="required">{t}Character Set:{/t}</span> </label>
			<div class="error">{validate id="list_charset" message=$formError.list_charset}</div>
			<select name="list_charset" id="list_charset">
				<option value="UTF-8" {if $list_charset == 'UTF-8'}SELECTED{/if}>{t}UTF-8 (recommended){/t}</option>
				<option value="ISO-8859-1" {if $list_charset == 'ISO-8859-1'}SELECTED{/if}>{t}western (ISO-8859-1){/t}</option>
				<option value="ISO-8859-2" {if $list_charset == 'ISO-8859-2'}SELECTED{/if}>{t}Central/Eastern European (ISO-8859-2){/t}</option>
				<option value="ISO-8859-7" {if $list_charset == 'ISO-8859-7'}SELECTED{/if}>{t}Greek (ISO-8859-7){/t}</option>
				<option value="ISO-8859-15" {if $list_charset == 'ISO-8859-15'}SELECTED{/if}>{t}western (ISO-8859-15){/t}</option>
				<option value="cp1251" {if $list_charset == 'cp1251'}SELECTED{/if}>{t}cyrillic (Windows-1251){/t}</option>
				<option value="KOI8-R" {if $list_charset == 'KOI8-R'}SELECTED{/if}>{t}cyrillic (KOI8-R){/t}</option>
				<option value="GB2312" {if $list_charset == 'GB2312'}SELECTED{/if}>{t}Simplified Chinese (GB2312){/t}</option>
				<option value="EUC-JP" {if $list_charset == 'EUC-JP'}SELECTED{/if}>{t}Japanese (EUC-JP){/t}</option>
			</select>
			<div class="notes">{t}(Select Default Character Set of Mailings){/t}</div>
		</div>

		<div class="field">
			<label for="list_exchanger"><span class="required">{t}Mail Exchanger:{/t}</span> </label>
			<select name="list_exchanger" id="list_exchanger">
				<option value="sendmail" {if $list_exchanger == 'sendmail'}SELECTED{/if}>Sendmail</option>
				<option value="mail" {if $list_exchanger == 'mail'}SELECTED{/if}>{t}PHP Mail Function{/t}</option>
				<option value="smtp" {if $list_exchanger == 'smtp'}SELECTED{/if}>SMTP Relay</option>
			</select>
			<div class="notes">{t}(Select Mail Exchanger){/t}</div>
		</div>

		{if $list_exchanger == 'smtp'}
			<div class="field">
				<a href="setup_smtp.php">
					<img src="{$url.theme.shared}images/icons/right.png" align="center" border="0">
				</a>
				  &nbsp; {t escape=no 1='<a href="setup_smtp.php">' 2='</a>}SMTP Servers: %1 Click Here %2 to setup your relays.{/t}
				<div class="notes">{t}(configure SMTP relays){/t}</div>
			</div>
		{/if}

		<div class="field">
			<a href="setup_messages.php">
				<img src="{$url.theme.shared}images/icons/right.png" align="center" border="0">
			</a>
			  &nbsp; {t escape=no 1='<a href="setup_messages.php">' 2='</a>}Messages: %1 Click Here %2 to customize mailed messages.{/t}
			<div class="notes">{t}(define the email messages sent during subscription, updates, etc.){/t}</div>
		</div>

		<br />

		<div class="field">
			<a href="setup_throttle.php">
				<img src="{$url.theme.shared}images/icons/right.png" align="center" border="0">
			</a>
			  &nbsp; {t escape=no 1='<a href="setup_throttle.php">' 2='</a>}Throttling: %1 Click Here %2 to set mail throttle values.{/t}
			<div class="notes">{t}(controls mails per second, bytes per second, and domain limits){/t}</div>
		</div>

		<br />

  </fieldset>

 	<div style="margin-left: 15%; margin-top: 15px;">
		<input class="button" type="submit" value="{t}Update{/t}" />
	</div>

 </form>

</div>
<!-- end mainbar -->
{include file="admin/inc.footer.tpl"}