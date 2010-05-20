{include file="inc/admin.header.tpl" sidebar='off'}

<h2>{t}Upgrader{/t}</h2>

<h3>{t}Online Upgrade Script{/t}</h3>

<ul class="inpage_menu">
<li>{$config.app.weblink}</li>
<li><a href="{$url.base}admin/admin.php">{t}Admin Page{/t}</a></li>
</ul>

<p><img src="{$url.theme.shared}images/icons/alert.png" alt="alert icon" class="navimage" /> {t}Welcome to the poMMo online upgrade process. This script will automatically upgrade your old version of poMMo.{/t}</p>

{include file="inc/messages.tpl"}

{if !$upgraded}
<form method="post" action="">
<input type="hidden" name="debugInstall" value="true" />
<input type="hidden" name="continue" value="true" />

<div class="debug">
{if $debug}

<div>
<label for="disableDebug">{t}To disable debugging{/t}</label>
<input type="submit" name="disableDebug" id="disableDebug" value="{t}Click Here{/t}" />
</div>

{else}

<div>
<label for="debugInstall">{t}To enable debugging{/t}</label>
<input type="submit" name="debugInstall" id="debugInstall" value="{t}Click Here{/t}" />
</div>

{/if}

<div>
<label for="debugInstall">{t}To force the upgrade{/t}</label>
<input type="submit" name="forceUpgrade" value="{t}Click Here{/t}" />
</div>

</div>

</form>
{/if}

{* Print Release Notes *}
{if $attempt && $upgraded} 
<pre>
{$notes}
</pre>
{/if}

<p><a href="{$url.base}"><img src="{$url.theme.shared}images/icons/back.png" alt="back icon" class="navimage" /> {t}Continue to login page{/t}</a></p>

{include file="inc/admin.footer.tpl"}