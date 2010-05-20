{capture name=head}{* used to inject content into the HTML <head> *}
{if $datePicker}
{include file="`$config.app.path`themes/shared/datepicker/datepicker.tpl"}
{else}
<script type="text/javascript" src="{$url.theme.shared}js/jq/jquery.js"></script>
{/if}
{/capture}
{include file="inc/user.header.tpl"}

<h3>{t}Subscriber Update{/t}</h3>

{include file="inc/messages.tpl"}
 
{if !$unsubscribe}

{include file="subscribe/form.update.tpl"}

<form method="post" action="">
<input type="hidden" name="email" value="{$email}" />
<input type="hidden" name="code" value="{$code}" />

<h3>{t}Unsubscribe{/t}</h3>

<label>{t}Comments{/t}:</label>
<textarea name="comments" rows="3" cols="33" maxlength="255">{if isset($d.$key)}{$d.$key}{elseif $field.normally}{$field.normally}{/if}</textarea>

<div class="buttons">

<button type="submit" name="unsubscribe" value="true" class="warn">
<img src="{$url.theme.shared}images/icons/nok.png" alt="not ok icon" /> {t}Click to unsubscribe{/t} {$Email}
</button>

</div>

</form>

{/if}


{literal}
<script type="text/javascript">
$().ready(function() {
	$('.warn').click(function() {
		var str = this.innerHTML;
		return confirm("{/literal}{t}Really unsubscribe?{/t}{literal}");
	});
});
</script>
{/literal}

{include file="inc/user.footer.tpl"}