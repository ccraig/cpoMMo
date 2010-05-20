{if $messages}
<div id="alertmsg" class="warn">

<ul>
{foreach from=$messages item=msg}
<li><strong>{$msg}</strong></li>
{/foreach}
</ul>

</div>
{/if}

{if $errors}
<div id="alertmsg" class="error">

{if $fatalMsg}<img src="{$url.theme.shared}images/icons/alert.png" alt="fatal error icon" />{/if}

<ul>
{foreach from=$errors item=msg}
<li>{$msg}</li>
{/foreach}
</ul>

</div>
{/if}