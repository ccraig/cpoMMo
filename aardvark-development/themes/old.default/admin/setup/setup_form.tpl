{include file="inc/admin.header.tpl"}

<h2>{t}Subscription Forms{/t}</h2>

<div id="boxMenu">

<div class="advanced"><a href="{$url.base}user/subscribe.php"><img src="{$url.theme.shared}images/icons/form.png" alt="form icon" class="navimage" />{t}Default Subscription Form{/t}</a> - {t}Preview the default subscription form. Its look and feel can be adjusted through the theme template ([theme]/user/subscribe.tpl).{/t}</div>

<div><a href="{$url.base}admin/setup/form_embed.php"><img src="{$url.theme.shared}images/icons/embed.png" alt="embed icon" class="navimage" />{t}Embedded Subscription From{/t}</a> - {t}Preview subscription forms that you can embed into an area of an existing webpage.{/t}</div>

<div><a href="{$url.base}admin/setup/form_generate.php"><img src="{$url.theme.shared}images/icons/plain.png" alt="plain icon" class="navimage" />{t}HTML Subscription Form{/t}</a> - {t}Generate a plain HTML subscription form that you can customize to fit your site.{/t}</div>

</div>

{include file="inc/admin.footer.tpl"}