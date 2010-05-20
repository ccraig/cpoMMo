{capture name=head}{* used to inject content into the HTML <head> *}
<script type="text/javascript" src="{$url.theme.shared}js/jq/jquery.js"></script>
<script type="text/javascript" src="{$url.theme.shared}js/thickbox/thickbox.js"></script>
<link type="text/css" rel="stylesheet" href="{$url.theme.shared}js/thickbox/thickbox.css" />
{/capture}{include file="inc/admin.header.tpl"}

<h2>{t}Embedded Subscription Forms{/t}</h2>

<ul class="inpage_menu">
<li><a href="setup_form.php">{t}Return to Subscription Forms{/t}</a></li>
</ul>

<p><img src="{$url.theme.shared}images/icons/embed.png" alt="embed" class="navimage right" /> {t}Subscription forms can easily be added to your website using a line of code. You can use the PHP include listed below, or embed the actual HTML. Remember, you can also direct subscribers to the {/t}<a href="{$url.base}user/subscribe.php">{t}Default Subscribe Form{/t}</a>.</p>

<h3>{t}Mini Subscription Form{/t}</h3>

<p>{t}This prints a form which prompts for a user's email address. If the inputted email exists as a registered subscriber, it redirects to the subscriber update page. If not, it redirects to the the default subscription form.{/t}</p>

<ul>
<li><a href="#TB_inline?height=400&amp;width=500&amp;inlineId=miniPreview" class="thickbox">{t}Preview{/t}</a></li>
<li><a href="{$url.base}embed.miniform.php">{t}URL{/t}</a></li>
</ul>

<p>PHP: <tt>include('{$config.app.path}embed.miniform.php');</tt></p>

<div id="miniPreview" class="hidden">


<h4>{t}Mini Subscription Form{/t} {t}Preview{/t}</h4>

{include file="subscribe/form.mini.tpl"}

<h4>PHP Include</h4>

<code>
include('{$url.base}embed.mini.php');
</code>

<h4>HTML Source</h4>

<textarea cols="70" rows="10">
{include file="subscribe/form.mini.tpl"}
</textarea>

</div>

<h3>{t}Full Subscription Form{/t}</h3>

<p>{t}This prints the entire subscription form.{/t}</p>

<ul>
<li><a href="#TB_inline?height=400&amp;width=500&amp;inlineId=fullPreview" class="thickbox">{t}Preview{/t}</a></li>
<li><a href="{$url.base}embed.form.php">{t}URL{/t}</a></li>
</ul>

<p>PHP: <tt>include('{$config.app.path}embed.form.php');</tt></p>

<div id="fullPreview" class="hidden">

<h4>{t}Subscription Form{/t} {t}Preview{/t}</h4>

{include file="subscribe/form.subscribe.tpl"}

<h4>PHP Include</h4>

<code>
include('{$url.base}embed.form.php');
</code>

<h4>HTML Source</h4>

<textarea cols="70" rows="10">
{include file="subscribe/form.subscribe.tpl"}
</textarea>

</div>

{include file="inc/admin.footer.tpl"}