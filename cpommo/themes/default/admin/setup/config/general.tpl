{* Field Validation - see docs/template.txt documentation *}
{fv form='general'}
{fv prepend='<span class="error">' append='</span>'}
{fv validate="list_name"}
{fv validate="site_name"}
{fv validate="site_url"}
{fv validate="site_success"}
{fv validate="site_confirm"}
{fv validate="list_confirm"}
{fv validate="list_exchanger"}

<form class="json" action="{$smarty.server.PHP_SELF}" method="post">

<div class="output alert">{if $output}{$output}{/if}</div>

<div>
<label for="list_name"><strong class="required">{t}List Name:{/t}</strong>{fv message="list_name"}</label>
<input type="text" name="list_name" value="{$list_name|escape}" />
<span class="notes">{t}(The name of your Mailing List){/t}</span>
</div>

<div>
<label for="site_name"><strong class="required">{t}Website Name:{/t}</strong>{fv message="site_name"}</label>
<input type="text" name="site_name" value="{$site_name|escape}" />
<span class="notes">{t}(The name of your Website){/t}</span>
</div>

<div>
<label for="site_url"><strong class="required">{t}Website URL:{/t}</strong>{fv message="site_url"}</label>
<input type="text" name="site_url" value="{$site_url|escape}" />
<span class="notes">{t}(Web address of your Website){/t}</span>
</div>


<div>
<label for="site_success">{t}Success URL:{/t}{fv message="site_success"}</label>
<input type="text" name="site_success" value="{$site_success|escape}" />
<span class="notes">{t}(Webpage users will see upon successfull subscription. Leave blank to display default welcome page.){/t}</span>
</div>

<div>
<label for="site_confirm">{t}Confirm URL:{/t}{fv message="site_confirm"}</label>
<input type="text" name="site_confirm" value="{$site_confirm|escape}" />
<span class="notes">{t}(Webpage users will see upon subscription attempt. Leave blank to display default confirmation page.){/t}</span>
</div>

<div>
<label for="list_confirm">{t}Email Confirmation:{/t}{fv message="list_confirm"}</label>
<input type="radio" name="list_confirm" value="on"{if $list_confirm == 'on'} checked="checked"{/if} /> {t}on{/t}
<input type="radio" name="list_confirm" value="off"{if $list_confirm != 'on'} checked="checked"{/if} /> {t}off{/t}
<span class="notes">{t}(Set to validate email upon subscription attempt.){/t}</span>
</div>

<div>
<label for="list_exchanger"><strong class="required">{t}Mail Exchanger:{/t}</strong>{fv message="list_exchanger"}</label>
<select name="list_exchanger">
<option value="sendmail"{if $list_exchanger == 'sendmail'} selected="selected"{/if}>Sendmail</option>
<option value="mail"{if $list_exchanger == 'mail'} selected="selected"{/if}>{t}PHP Mail Function{/t}</option>
<option value="smtp"{if $list_exchanger == 'smtp'} selected="selected"{/if}>SMTP Relay</option>
</select>
&nbsp;&nbsp; - &nbsp;&nbsp; <a href="config/ajax.testexchanger.php" id="testTrigger">{t}Test Exchanger{/t}</a>
<span class="notes">{t}(Select Mail Exchanger){/t}</span>
</div>

<div class="hidden" id="configSMTP">
	<br clear="both" />
	<a href="config/ajax.smtp.php" id="smtpTrigger"><img src="{$url.theme.shared}images/icons/right.png" alt="icon" class="navimage" /> {t}Setup your SMTP Servers{/t}</a>
	<span class="notes">{t}(configure SMTP relays){/t}</span>
	<br clear="both" />
</div>


<input type="submit" value="{t}Update{/t}" />
<img src="{$url.theme.shared}images/loader.gif" alt="loading..." class="hidden" name="loading" />
</form>

{literal}
<script type="text/javascript">

var showSMTP = function() {
	if(exchanger.val() == 'smtp')
		$('#configSMTP').show();
	else
		$('#configSMTP').hide();
}
var exchanger = $('select[@name=list_exchanger]');

$().ready(function(){
	$('#smtpWindow').jqmAddTrigger($('#smtpTrigger'));
	$('#testWindow').jqmAddTrigger($('#testTrigger'));
	
	exchanger.change(function(){
		$(this).parents('form:eq(0)').submit();
		showSMTP(); 
	});
	showSMTP();
	
});
</script>
{/literal}