{include file="inc/user.header.tpl"}

<h2>{t}Subscription Review{/t}</h2>

{if $back}
<p><a href="{$referer}" onClick="history.back(); return false;"><img src="{$url.theme.shared}images/icons/back.png" alt="back icon" class="navimage" /> {t}Back to Subscription Form{/t}</a></p>

{else}
<p><a href="{$config.site_url}"><img src="{$url.theme.shared}images/icons/back.png" alt="back icon" class="navimage" />{t 1=$config.site_name}Return to %1{/t}</a></p>

{/if}

{include file="inc/messages.tpl"}

{if $dupe}
<p>{t escape=no 1="<a href=\"login.php\">" 2='</a>'}%1Update your records%2{/t}</p>
{/if}

{include file="inc/user.footer.tpl"}