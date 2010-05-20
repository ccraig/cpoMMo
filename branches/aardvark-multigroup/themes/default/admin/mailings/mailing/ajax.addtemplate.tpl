{if $success}
{include file="inc/messages.tpl"}
<div class="buttons">
<input type="submit" class="jqmClose" value="{t}Close{/t}" />
</div>
{php}return;{/php}
{/if}

{* Field Validation - see docs/template.txt documentation *}
{fv prepend='<span class="error">' append='</span>'}
{fv validate="name"}
{fv validate="description"}

<div class="output">
{include file="inc/messages.tpl"}
</div>

<div class="alert">
{t}Templates allow you to re-use your crafted message bodies. The HTML and text version is remembered.{/t}
</div>

<form class="ajax" action="{$smarty.server.PHP_SELF}" method="post">

<p>{t escape=no 1="<strong class=\"required\">" 2="</strong>"}Fields in %1bold%2 are required{/t}</p>

<div>
<label for="name"><span class="required">{t}Name:{/t}</span>{fv message="name"}</label>
<input type="text" name="name" value="{$smarty.post.name|escape}" />
<span class="notes">{t}(maximum of 60 characters){/t}</span>
</div>

<div>
<label for="description">{t}Description:{/t}</span>{fv message="description"}</label>
<textarea name="description" style="height: 60px;">{$smarty.post.description}</textarea>
<span class="notes">{t}(Brief Summary - 255 characters){/t}</span>
</div>

<div class="buttons">
<input type="submit" id="submit" name="submit" value="{t}Save{/t}" />
<input type="submit" class="jqmClose" value="{t}Cancel{/t}" />
</div>

</form>