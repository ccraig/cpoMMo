{include file="inc/admin.header.tpl"}

<h2>{t}Setup Page{/t}</h2>

<div id="boxMenu">

<div class="advanced"><a href="{$url.base}admin/setup/setup_configure.php"><img src="{$url.theme.shared}images/icons/settings.png" alt="settings icon" class="navimage" />{t}Configure{/t}</a> - {t}Set your mailing list name, its default behavior, and the administrator's information.{/t}</div>

<div><a href="{$url.base}admin/setup/setup_fields.php"><img src="{$url.theme.shared}images/icons/fields.png" alt="subscriber icon" class="navimage" />{t}Subscriber Fields{/t}</a> - {t}Choose the information you'd like to collect from your subscribers.{/t}</div>

<div><a href="{$url.base}admin/setup/setup_form.php"><img src="{$url.theme.shared}images/icons/form.png" alt="form icon" class="navimage" />{t}Subscription Form{/t}</a> - {t}Preview and Generate the subscription form for your website.{/t}</div>

</div>

{include file="inc/admin.footer.tpl"}