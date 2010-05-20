{include file="admin/inc.header.tpl"}
{assign var='mailingCount' value=$mailings|@count}
</div>

<h1>{$actionStr}</h1>
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

<div style="width:100%;">
	<span style="float: right; margin-right: 30px;">
		<a href="mailings_history.php">{t 1=$returnStr}Return to %1{/t}</a>
	</span>
</div>
<p style="clear: both; text-align:center;"><hr></p>

<form name="aForm" id="aForm" method="POST" action="">

<p><span style="text-align: center;">
{if $action == 'view'}
	{t 1=$mailingCount}Displaying %1 mailings.{/t}
{elseif $action == 'delete'}
	{t 1=$mailingCount}The following %1 mailings will be deleted{/t}:
	<p>
		<input type="submit" name="deleteMailings" value="{t}Delete Mailings{/t}">
	</p>
{/if}
</span></p>

		
{foreach from=$mailings key=key item=mailing}
	<input type="hidden" name="delid[]" value="{$mailing.id}">
	<div style="background-color: #E6ECDA; width: 80%; text-align:left;">
		<table border="0" cellpadding="0" cellspacing="0" style="text-align:left; padding:10px;">
			<tr>
				<td>
					<p><b>{t}From:{/t} </b>{$mailing.fromname} &lt;{$mailing.fromemail}&gt;</p>
					{if $mailing.fromemail != $mailing.frombounce}<p><b>{t}Bounces:{/t} </b>&lt;{$mailing.frombounce}&gt;</p>{/if}
					<p><b>{t}To:{/t} </b>{$mailing.mailgroup}, <i>{$mailing.subscriberCount}</i> {t}recipients.{/t}</p>
					<p><b>{t}Subject:{/t} {$mailing.subject}</b></p>
				</td>	
			</tr>
		</table>
	</div>
	
	
	<div style="background-color: #F6F8F1;  width: 80%; text-align:left;">
		<table border="0" cellpadding="0" cellspacing="0" style="text-align:left; padding:10px;">
			<tr>
				<td valign="top">
					{if $mailing.ishtml == 'on'}
						<p>
							<b>{t}HTML Body:{/t} </b>
								 <a href="mailing_preview.php?viewid={$key}" target="_blank">{t escape=no 1='</a>'}Click here %1 to view in a new browser window.{/t}
						</p>
						{if $mailing.altbody}
							<p>
							<b>{t}Alt Body:{/t} </b>
							<br>
							<pre>{$mailing.altbody}</pre>
							</p>
						{/if}
					{else}
						<p>
						<b>{t}Body:{/t} </b>
						<br>
						<pre>{$mailing.body}</pre>
						</p>
					{/if}

				</td>
			</tr>
		</table>
		<hr>
	</div>

	<br>
{/foreach}

{if $action == 'delete'}
<p>
	<input type="submit" name="deleteMailings" value="{t}Delete Mailings{/t}">
</p>
{/if}
			
</form>

{include file="admin/inc.footer.tpl"}

