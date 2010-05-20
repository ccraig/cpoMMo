{* Field Validation - see docs/template.txt documentation *}
{fv prepend='<span class="error">' append='</span>'}
{fv validate="name"}
{fv validate="description"}

<div>
<p>{t}Templates allow you to re-use your crafted message bodies. The HTML and text version is remembered.{/t}<br />
{t escape=no 1="<span class=\"required\">" 2="</span>"}%1Bold%2 fields are required.{/t}</p>
</div>

<div class="output" style="font-size: 130%;">
{include file="inc/messages.tpl"}
</div>

<form class="ajax" action="{$smarty.server.PHP_SELF}" method="post">

<div class="formSpacing">
<label for="name"><span class="required">{t}Name:{/t}</span>{fv message="name"}</label>
<input type="text" name="name" value="{$smarty.post.name|escape}" size="30" maxlength="60" />
<span class="notes">{t}(maximum of 60 characters){/t}</span>
</div>

<div>
<label for="description">{t}Description:{/t}</span>{fv message="description"}</label><br />
<textarea name="description" style="height: 80px; width: 80%;">{$smarty.post.description}</textarea>
<span class="notes">{t}(Brief Summary - 255 characters){/t}</span>
</div>

<br class="clear">

<div class="buttons">
<button type="submit" id="submit" name="submit" value="{t}Save{/t}" class="positive"><img src="{$url.theme.shared}/images/icons/tick.png" alt="save"/>{t}Save{/t}</button>
<button type="submit" class="jqmClose negative" value="{t}Cancel{/t}" ><img src="{$url.theme.shared}/images/icons/cross.png" alt="cancel"/>{t}Cancel{/t}</button>
</div>

</form>

{literal}
<script type="text/javascript">
$().ready(function(){
	$('form .jqmClose',$('#dialog')[0]).click(function(){$('#dialog').jqmHide();});
});
</script>
{/literal}