{capture name=head}{* used to inject content into the HTML <head> *}
<script type="text/javascript" src="{$url.theme.shared}js/jq/jquery.js"></script>
<script type="text/javascript" src="{$url.theme.shared}js/thickbox/thickbox.js"></script>
<script type="text/javascript" src="{$url.theme.shared}js/jq/form.js"></script>
<link type="text/css" rel="stylesheet" href="{$url.theme.shared}css/default.mailings.css" />
<link type="text/css" rel="stylesheet" href="{$url.theme.shared}js/thickbox/thickbox.css" />
{/capture}
{include file="inc/admin.header.tpl"}

<div id="preview">

<h2>{t}Preview Mailing{/t}</h2>

{include file="inc/messages.tpl"}

<div class="msgheaders">

<p class="edit"><a href="mailings_send.php"><img src="{$url.theme.shared}images/icons/left.png" alt="back arrow icon" /> {t}edit{/t}</a></p>

<p><strong>{t}Subject:{/t}</strong> <tt>{$subject}</tt></p>

<p><strong>{t}To:{/t}</strong> {$group} (<em>{$tally}</em> {t}recipients{/t})</p>

<p><strong>{t}From:{/t}</strong> {$fromname} <tt>&lt;{$fromemail}&gt;</tt></p>

{if $fromemail != $frombounce}
<p><strong>{t}Bounces:{/t}</strong> <tt>&lt;{$frombounce}&gt;</tt></p>
{/if}

<p class="advanced"><strong>{t}Character Set:{/t}</strong> <tt>{$list_charset}</tt></p>

</div>

<div class="msgpreview">

<p class="edit"><a href="mailings_send2.php"><img src="{$url.theme.shared}images/icons/left.png" alt="back arrow icon" /> {t}edit{/t}</a></p>

{if $ishtml == 'on'}

<p><strong>{t}HTML Body:{/t}</strong> <a href="ajax/mailing_preview.php" title="{t}Preview Message{/t}" onclick="return !window.open(this.href)">{t}Preview Message{/t}</a></p>

{if $altbody}
<p><strong>{t}Alt Body:{/t}</strong></p>

<pre>
{$altbody}
</pre>
{/if}

{else}

<p><strong>{t}Body:{/t}</strong></p>

<pre>
{$body}
</pre>

{/if}
</div>

<fieldset>
<legend>{t}Test Mailing{/t}</legend>

<div class="buttons">
<a href="ajax/mailing_test.php?height=400&amp;width=500" title="{t}Send Test Mailing{/t}" class="thickbox"><button>{t}Send Test{/t}</button></a>
</div>

</fieldset>

<form method="get" action="">

<fieldset>
<legend>{t}Send Mailing{/t}</legend>

<div class="buttons">

<button type="submit" name="sendaway" value="TRUE"><img src="{$url.theme.shared}images/icons/send.png" alt="broadcast icon" />{t}Send Mailing{/t}</button>

</div>

</fieldset>
</form>

</div>

{include file="inc/admin.footer.tpl"}