{* Field Validation - see docs/template.txt documentation *}
{fv form='users'}
{fv prepend='<span class="error">' append='</span>'}
{fv validate="admin_username"}
{fv validate="admin_password2"}
{fv validate="admin_email"}

<form action="{$smarty.server.PHP_SELF}" method="post" class="json">

<div class="formSpacing">
<label for="admin_username"><strong class="required">{t}Admin Username:{/t}&nbsp;</strong>{fv message="admin_username"}</label>
<input type="text" name="admin_username" value="{$admin_username|escape}" size="20" maxlength="60" />
<!--<span class="notes">{t}(you will use this to login){/t}</span>-->
</div>

<div class="formSpacing">
<label for="admin_password">{t}Admin Password:{/t}&nbsp;</label>
<input type="password" name="admin_password" value="{$admin_password|escape}" size="20" maxlength="60" />
<!--<span class="notes">{t}(you will use this to login){/t}</span>-->
</div>

<div class="formSpacing">
<label for="admin_password2">{t}Verify Password:{/t}&nbsp;{fv message="admin_password"}</label>
<input type="password" name="admin_password2" value="{$admin_password2|escape}" size="20" maxlength="60" />
<!--<span class="notes">{t}(enter password again){/t}</span>-->
</div>

<div class="formSpacing">
<label for="admin_email"><strong class="required">{t}Admin Email:{/t}&nbsp;</strong>{fv message="admin_email"}</label>
<input type="text" name="admin_email" value="{$admin_email|escape}" size="30" maxlength="60" />
<!--<span class="notes">{t}(email address of administrator){/t}</span>-->
</div>

<div class="formSpacing"></div>

<div class="buttons">
<button type="submit" value="{t}Update{/t}" class="positive"><img src="{$url.theme.shared}/images/icons/tick.png" alt="update"/>{t}Update{/t}</button>
<img src="{$url.theme.shared}images/loader.gif" alt="loading..." class="hidden" name="loading" />
</div>

<br class="clear">

<div class="output">{if $output}{$output}{/if}</div>

<div class="clear"></div>
</form>