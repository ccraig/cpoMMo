{include file="admin/inc.header.tpl"}
{include file="admin/inc.sidebar.tpl"}

<div id="mainbar">

	<h1>{t}Mailings Page{/t}</h1>
	
	{if $mailing}
		<p>
			<a href="{$url.base}admin/mailings/mailing_status.php">
			<img src="{$url.theme.shared}images/icons/status.png" class="navimage">							
			{t}Status{/t}</a> - 
			{t}A mailing is currently taking place. You can not create a mailing until this one completes. Visit this page to check on the status of this mailing.{/t}
			<br>&nbsp;
		</p>
	{else}
		<p>
			<a href="{$url.base}admin/mailings/mailings_send.php">
			<img src="{$url.theme.shared}images/icons/typewritter.png" class="navimage" />
			{t}Send{/t}</a> - 
			{t}Create and send a mailing.{/t} 
			<br>&nbsp;
		</p>
	{/if}
	
	<p>
		<a href="{$url.base}admin/mailings/mailings_history.php">
		<img src="{$url.theme.shared}images/icons/history.png" class="navimage" />							
		{t}History{/t}</a> - {t}View mailings that have already been sent.{/t}
		<br>&nbsp;
	</p>	
	
</div>
<!-- end mainbar -->

{include file="admin/inc.footer.tpl"}