{include file="admin/inc.header.tpl"}

<h1>{t}Preview Mailing{/t}</h1>

 {if $messages}
    <div class="msgdisplay">
    {foreach from=$messages item=msg}
   	 <div>* {$msg}</div>
    {/foreach}
    </div>
 {/if}
 
 {if $errors}
 	<br>
	<div class="errdisplay">
	{foreach from=$errors item=msg}
	<div>* {$msg}</div>
	{/foreach}
	</div>
 {/if}

<div style="background-color: #E6ECDA;">

	<table border="0" cellpadding="2" cellspacing="0">
	<tr>
	<td valign="middle">
		<a href="mailings_send.php">
			<img src="{$url.theme.shared}images/icons/left.png" border="0">&nbsp;<br>{t}edit{/t}
		</a>
	<td>
	<p><b>{t}From:{/t} </b>{$fromname} &lt;{$fromemail}&gt;</p>
	{if $fromemail != $frombounce}<p><b>{t}Bounces:{/t} </b>&lt;{$frombounce}&gt;</p>{/if}
	<p><b>{t}Subject:{/t} </b>{$subject}</p>
	<p><b>{t}To:{/t} </b>{$groupName}, <i>{$subscriberCount}</i> {t}recipients.{/t}</p>
	<p><b>{t}Character Set:{/t} </b>{$charset}</p>
	</td></tr></table>
	<hr>
	
	<form method="POST" action="" name="test">
	<div align="center">
		{t}Test Mailing on:{/t}  
		<input type="text" name="testTo" size="25" value="{$config.admin_email}" maxlength="60">
		&nbsp;&nbsp;<input type="submit" name="testMail" value="{t}Send Test{/t}">
	</div>
	</form>
	
	<hr>
</div>

<div style="background-color: #F6F8F1;">
	<table border="0" cellpadding="2" cellspacing="0">

	<tr>
	<td valign="top">
	<br>
	<a href="mailings_send2.php">
		<img src="{$url.theme.shared}images/icons/left.png" border="0">&nbsp;<br>{t}edit{/t}
	</a>
	<td>
	
	{if $ishtml == 'html'}
		<p>
			<b>{t}HTML Body:{/t} </b>
			 <a href="mailing_preview.php" target="_blank">{t escape=no 1='</a>'}Click here %1 to view in a new browser window.{/t}
		</p>
		{if $altbody}
		<p>
			<b>{t}Alt Body:{/t} </b>
			<br>
			<pre>{$altbody}</pre>
		</p>
		{/if}
	{else}
		<p>
			<b>{t}Body:{/t} </b>
			<br>
			<pre>{$body}</pre>
		</p>
	{/if}
	
	</td></tr>
	</table>
</div>

	<br>

	<div align="center">
	<br><br>
		<a href="mailings_send3.php?sendaway=TRUE\">
			<img src="{$url.theme.shared}images/icons/send.png" class="navimage" border="0">
			<br>{t}Send Mailing{/t}
		</a>
	</div>
	

{include file="admin/inc.footer.tpl"}