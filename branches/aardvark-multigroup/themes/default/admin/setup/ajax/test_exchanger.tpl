{* Field Validation - see docs/template.txt documentation *}
{fv form='exchanger'}
{fv prepend='<span class="error">' append='</span>'}
{fv validate="email"}

<div id="ebody">

<p>{t escape=no 1="<strong>$exchanger</strong>"}A test message will be sent to the supplied recipient. If you receive it, poMMo can use the %1 exchanger. Remember to check your SPAM folder too.{/t}</p>

<form action="{$smarty.server.PHP_SELF}" method="post">

<fieldset>
<legend>{t}Recipient{/t}</legend>

<div>
<label for="email"><strong class="required">{t}Email:{/t}</strong></label>
<input type="text" name="email" value="{$email|escape}" />
<span class="notes">{t}(address to send test message to){/t}</span>
</div>

<input type="submit" value="{t}Send Mailing{/t}"/>
<img src="{$url.theme.shared}images/loader.gif" alt="loading..." class="hidden" name="loading" />
<div class="output alert">{if $output}{$output}{/if}</div>

</form>

</div>

{literal}
<script type="text/javascript">
$().ready(function(){
	assignForm($('#ebody')[0]);
});
</script>
{/literal}