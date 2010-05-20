{include file="inc/admin.header.tpl"}

<h2>{t}Mailings Page{/t}</h2>


<div id="boxMenu">

<div>
<a href="{$url.base}admin/mailings/mailings_start.php"><img src="{$url.theme.shared}images/icons/typewritter.png" alt="typewritter icon" class="navimage" />{t}Send{/t}</a> - {t}Create and send a mailing.{/t}
</div>

<div>
<a href="{$url.base}admin/mailings/mailings_history.php"><img src="{$url.theme.shared}images/icons/history.png" alt="calendar icon" class="navimage" />{t}History{/t}</a> - {t}View mailings that have already been sent.{/t}
</div>

</div>

{include file="inc/admin.footer.tpl"}