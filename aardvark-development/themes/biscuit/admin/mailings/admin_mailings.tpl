{include file="inc/admin.header.tpl"}

<h2>{t}Mailings Page{/t}</h2>

<ul id="sec_nav">
	<li><a href="{$url.base}admin/mailings/mailings_start.php"><img src="{$url.theme.shared}images/icons/send_mail.png" alt="send mail icon" class="navimage" />
	<h3>{t}Send Mail{/t}</h3>
	<span>{t}Create and send mailings to the entire list or to a subset of subscribers. Templates may also be created in this section.{/t}</span></a>
	</li>
    
	<li><a href="{$url.base}admin/mailings/mailing_status.php"><img src="{$url.theme.shared}images/icons/status.png" alt="status icon" class="navimage" />
	<h3>{t}Status{/t}</h3>
	<span>{t}Track mailings currently being sent out in real time. You can also download reports listing sent, unsent, and failed emails.{/t}</span></a>
	</li>

	<li><a href="{$url.base}admin/mailings/mailings_history.php"><img src="{$url.theme.shared}images/icons/history.png" alt="history icon" class="navimage" />
	<h3>{t}History{/t}</h3>
    <span>{t}View mailings that have already been sent, view last notices, or even reload previously sent emails to send again.{/t}</span></a>
	</li>
</ul>

{include file="inc/admin.footer.tpl"}