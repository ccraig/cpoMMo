{if !$embeddedConfirm}
{include file="inc/admin.header.tpl"}

{if $confirm.title}
<h2>{$confirm.title}</h2>
{else}
<h2>{t}Confirm{/t}</h2>
{/if}

{/if}

<div id="warnmsg" class="warn">

{if $confirm.msg}
<p>{$confirm.msg}</p>
{/if}

<p><strong>{t}Confirm your action.{/t}</strong></p>

</div>

<p><a href="{$confirm.nourl}"><img src="{$url.theme.shared}images/icons/undo.png" alt="undo icon" class="navimage" /> {t}No{/t} {t}please return{/t}</a></p>

<p><a href="{$confirm.yesurl}"><img src="{$url.theme.shared}images/icons/ok.png" alt="accept icon" class="navimage" /> {t}Yes{/t} {t}I confirm{/t}</a></p>

{if !$embeddedConfirm}
{include file="inc/admin.footer.tpl"}
{/if}