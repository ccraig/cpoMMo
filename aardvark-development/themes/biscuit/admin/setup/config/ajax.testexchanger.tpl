{* Field Validation - see docs/template.txt documentation *}
{fv form='exchanger'}
{fv prepend='<span class="error">' append='</span>'}
{fv validate="email"}

<p>{t escape=no 1="<strong>$exchanger</strong>"}A test message will be sent to the supplied recipient. If you receive it, poMMo can use the %1 exchanger. Remember to check your SPAM folder too.{/t}</p>

<div id="scope">
<form action="{$smarty.server.PHP_SELF}" method="post">

<fieldset>
<h3>{t}Send test to:{/t}</h3>

<div>
<!--<span class="notes">{t}(address to send test message to){/t}</span>-->
<!--<label for="email"><strong class="required">{t}Email:{/t}</strong></label><br />-->
<input type="text" name="email" value="{$email|escape}"  size="30" maxlength="60" />
</div>

<div class="buttons">
<button type="submit" value="{t}Send Mailing{/t}" class="positive"><img src="{$url.theme.shared}/images/icons/load.png" alt="send test"/>{t}Send Test{/t}</button>
<img src="{$url.theme.shared}images/loader.gif" alt="loading..." class="hidden" name="loading" />
</div>

<br class="clear">

<div class="output alert">{if $output}{$output}{/if}</div>

</form>
</div>
{literal}
<script type="text/javascript">
poMMo.form.init('#scope form',{type: 'json'});
</script>
{/literal}