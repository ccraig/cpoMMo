{* Field Validation - see docs/template.txt documentation *}
{fv form='users'}
{fv prepend='<span class="error">' append='</span>'}
{fv validate="admin_username"}
{fv validate="admin_password2"}
{fv validate="admin_email"}

<form action="{$smarty.server.PHP_SELF}" method="post" class="json">

<div class="output alert">{if $output}{$output}{/if}</div>

<div>
<label for="admin_username"><strong class="required">{t}Administrator Username:{/t}</strong>{fv message="admin_username"}</label>
<input type="text" name="admin_username" value="{$admin_username|escape}" />
<span class="notes">{t}(you will use this to login){/t}</span>
</div>

<div>
<label for="admin_password">{t}Administrator Password:{/t}</label>
<input type="password" name="admin_password" value="{$admin_password|escape}" />
<span class="notes">{t}(you will use this to login){/t}</span>
</div>

<div>
<label for="admin_password2">{t}Verify Password:{/t}{fv message="admin_password"}</label>
<input type="password" name="admin_password2" value="{$admin_password2|escape}" />
<span class="notes">{t}(enter password again){/t}</span>
</div>

<div>
<label for="admin_email"><strong class="required">{t}Administrator Email:{/t}</strong>{fv message="admin_email"}</label>
<input type="text" name="admin_email" value="{$admin_email|escape}" />
<span class="notes">{t}(email address of administrator){/t}</span>
</div>

<input type="submit" value="{t}Update{/t}" />
<img src="{$url.theme.shared}images/loader.gif" alt="loading..." class="hidden" name="loading" />
</form>