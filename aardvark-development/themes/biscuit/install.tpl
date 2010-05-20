{include file="inc/admin.header.tpl" sidebar='off'}

<h2>{t}Installation - Configuration Options{/t}</h2>

<p>{t}Welcome to the online installation process. We have connected to the database and set your language successfully. Fill in the values below, and you'll be on your way! You will be able to change these values at any time in the "Setup" section.{/t}</p>

{include file="inc/messages.tpl"}

{if !$installed}
<form method="post" action="">
<fieldset>

<div>
<div class="error">{validate id="list_name" message=$formError.list_name}</div>
<label for="list_name"><strong class="required">{t}Mailing List Name:{/t}&nbsp;</strong></label>
<input type="text" size="30" maxlength="60" name="list_name" value="{$list_name|escape}" id="list_name" />
<span class="notes">{t}(ie. Brice's Mailing List){/t}</span>
</div>

<div class="formSpacing">
<div class="error">{validate id="site_name" message=$formError.site_name}</div>
<label for="site_name"><strong class="required">{t}Your Company Name:{/t}&nbsp;</strong></label>
<input type="text" size="30" maxlength="60" name="site_name" value="{$site_name|escape}" id="site_name" />
<span class="notes">{t}(ie. The poMMo Website){/t}</span>
</div>

<div class="formSpacing">
<div class="error">{validate id="site_url" message=$formError.site_url}</div>
<label for="site_url"><strong class="required">{t}Your Website URL:{/t}&nbsp;</strong></label>
<input type="text" size="30" maxlength="60" name="site_url" value="{$site_url|escape}" id="site_url" />
<span class="notes">{t}(ie. http://www.pommo-rocks.com/){/t}</span>
</div>

<div class="formSpacing">
<div class="error">{validate id="admin_password" message=$formError.admin_password}</div>
<label for="admin_password"><strong class="required">{t}Admin Password:{/t}&nbsp;</strong></label>
<input type="password" size="30" maxlength="60" name="admin_password" value="{$admin_password|escape}" id="admin_password" />
<span class="notes">{t}(you will use this to login){/t}</span>
</div>

<div class="formSpacing">
<div class="error">{validate id="admin_password2" message=$formError.admin_password2}</div>
<label for="admin_password2"><strong class="required">{t}Verify Password:{/t}&nbsp;</strong></label>
<input type="password" size="30" maxlength="60" name="admin_password2" value="{$admin_password2|escape}" id="admin_password2" />
<span class="notes">{t}(enter password again){/t}</span>
</div>

<div class="formSpacing">
<div class="error">{validate id="admin_email" message=$formError.admin_email}</div>
<label for="admin_email"><strong class="required">{t}Admin Email:{/t}&nbsp;</strong></label>
<input type="text" size="30" maxlength="60" name="admin_email" value="{$admin_email|escape}" id="admin_email" />
<span class="notes">{t}(enter your valid email address){/t}</span>
</div>

</fieldset>

<div class="buttons">

<button type="submit" id="installerooni" name="installerooni" value="{t}Install{/t}" class="green"><img src="{$url.theme.shared}/images/icons/add-small.png" alt="install"/>Install</button>

{if $debug}
<input type="hidden" name="debugInstall" value="true" />

<button type="submit" id="disableDebug" name="disableDebug" value="{t}Disable Debugging{/t}"><img src="{$url.theme.shared}/images/icons/bug_bw.png" alt="debug"/>{t}Disable Debugging{/t}</button>

{else}

<button type="submit" id="debugInstall" name="debugInstall" value="{t}Enable Debugging{/t}"><img src="{$url.theme.shared}/images/icons/bug_bw.png" alt="debug"/>{t}Enable Debugging{/t}</button>

{/if}

</div>

</form>
{/if}

<div class="buttons">
<a href="{$url.base}index.php"><img src="{$url.theme.shared}images/icons/continue_bw.png" alt="continue to login">{t}Continue to login page{/t}</a>
{$config.app.weblink}
</div>

{include file="inc/admin.footer.tpl"}