{include file="inc/admin.header.tpl" sidebar='off'}

<h2>{t}Installation{/t}</h2>

<h3>{t}Online install{/t}</h3>

<ul class="inpage_menu">
<li>{$config.app.weblink}</li>
<li><a href="{$url.base}admin/admin.php">{t}Admin Page{/t}</a></li>
</ul>

<p>{t}Welcome to the online installation process. We have connected to the database and set your language successfully. Fill in the values below, and you'll be on your way!{/t}</p>

{include file="inc/messages.tpl"}

{if !$installed}
<form method="post" action="">
<fieldset>
<legend>{t}Configuration Options{/t}</legend>

<div>
<div class="error">{validate id="list_name" message=$formError.list_name}</div>
<label for="list_name"><strong class="required">{t}Name of Mailing List:{/t}</strong></label>
<input type="text" size="32" maxlength="60" name="list_name" value="{$list_name|escape}" id="list_name" />
<span class="notes">{t}(ie. Brice's Mailing List){/t}</span>
</div>

<div>
<div class="error">{validate id="site_name" message=$formError.site_name}</div>
<label for="site_name"><strong class="required">{t}Name of Website:{/t}</strong></label>
<input type="text" size="32" maxlength="60" name="site_name" value="{$site_name|escape}" id="site_name" />
<span class="notes">{t}(ie. The poMMo Website){/t}</span>
</div>

<div>
<div class="error">{validate id="site_url" message=$formError.site_url}</div>
<label for="site_url"><strong class="required">{t}Website URL:{/t}</strong></label>
<input type="text" size="32" maxlength="60" name="site_url" value="{$site_url|escape}" id="site_url" />
<span class="notes">{t}(ie. http://www.pommo-rocks.com/){/t}</span>
</div>

<div>
<div class="error">{validate id="admin_password" message=$formError.admin_password}</div>
<label for="admin_password"><strong class="required">{t}Administrator Password:{/t}</strong></label>
<input type="password" size="32" maxlength="60" name="admin_password" value="{$admin_password|escape}" id="admin_password" />
<span class="notes">{t}(you will use this to login){/t}</span>
</div>

<div>
<div class="error">{validate id="admin_password2" message=$formError.admin_password2}</div>
<label for="admin_password2"><strong class="required">{t}Verify Password:{/t}</strong></label>
<input type="password" size="32" maxlength="60" name="admin_password2" value="{$admin_password2|escape}" id="admin_password2" />
<span class="notes">{t}(enter password again){/t}</span>
</div>

<div>
<div class="error">{validate id="admin_email" message=$formError.admin_email}</div>
<label for="admin_email"><strong class="required">{t}Administrator Email:{/t}</strong></label>
<input type="text" size="32" maxlength="60" name="admin_email" value="{$admin_email|escape}" id="admin_email" />
<span class="notes">{t}(enter your valid email address){/t}</span>
</div>

</fieldset>

<div class="buttons">

<input type="submit" id="installerooni" name="installerooni" value="{t}Install{/t}" />

{if $debug}
<input type="hidden" name="debugInstall" value="true" />

<input type="submit" id="disableDebug" name="disableDebug" value="{t}To disable debugging{/t} {t}Click Here{/t}" />

{else}

<input type="submit" id="debugInstall" name="debugInstall" value="{t}To enable debugging{/t} {t}Click Here{/t}" />

{/if}

</div>

</form>
{/if}

<p><a href="{$url.base}index.php"><img src="{$url.theme.shared}images/icons/back.png" alt="back icon" class="navimage" />{t}Continue to login page{/t}</a></p>

{include file="inc/admin.footer.tpl"}