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

{include file="inc/ui.tooltips.tpl"}

<form class="json" action="{$smarty.server.PHP_SELF}" method="post">

<div class="formSpacing">
<label for="list_name"><strong class="required">{t}Mailing List Name:{/t}&nbsp;</strong>{fv message="list_name"}</label>
<input type="text" name="list_name" value="{$list_name|escape}" size="30" maxlength="60" />
<!--<span class="notes">{t}(the name of your mailing list){/t}</span>-->
</div>

<div class="formSpacing">
<label for="site_name"><strong class="required">{t}Your Company Name:{/t}&nbsp;</strong>{fv message="site_name"}</label>
<input type="text" name="site_name" value="{$site_name|escape}" size="30" maxlength="60" />
<!--<span class="notes">{t}(the name of your website){/t}</span>-->
</div>

<div class="formSpacing">
<label for="site_url"><strong class="required">{t}Your Website URL:{/t}&nbsp;</strong>{fv message="site_url"}</label>
<input type="text" name="site_url" value="{$site_url|escape}" size="30" maxlength="60" />
<!--<span class="notes">{t}(web address of your website){/t}</span>-->
</div>

<div class="formSpacing">
<label for="site_success">{t}Success URL:{/t}&nbsp;{fv message="site_success"}</label>
<input type="text" name="site_success" value="{$site_success|escape}" size="30" maxlength="60" />&nbsp;<a href="#" class="tooltip" title="{t}webpage users will see upon successfull subscription... leave blank to display default welcome page{/t}"><img src="{$url.theme.shared}/images/icons/help-small.png" alt="tip" /></a>
<!--<span class="notes">{t}(webpage users will see upon successfull subscription... leave blank to display default welcome page){/t}</span>-->
</div>

<div class="formSpacing">
<label for="site_confirm">{t}Confirm URL:{/t}&nbsp;{fv message="site_confirm"}</label>
<input type="text" name="site_confirm" value="{$site_confirm|escape}" size="30" maxlength="60" />&nbsp;<a href="#" class="tooltip" title="{t}webpage users will see upon subscription attempt... leave blank to display default confirmation page{/t}"><img src="{$url.theme.shared}/images/icons/help-small.png" alt="tip" /></a>
<!--<span class="notes">{t}(webpage users will see upon subscription attempt... leave blank to display default confirmation page){/t}</span>-->
</div>

<div class="formSpacing">
<label for="list_confirm">{t}Email Confirmation:{/t}&nbsp;{fv message="list_confirm"}</label>
<input type="radio" name="list_confirm" value="on"{if $list_confirm == 'on'} checked="checked"{/if} /> {t}on{/t}&nbsp;&nbsp;
<input type="radio" name="list_confirm" value="off"{if $list_confirm != 'on'} checked="checked"{/if} /> {t}off{/t}&nbsp;&nbsp;<a href="#" class="tooltip" title="set to validate email upon subscription attempt"><img src="{$url.theme.shared}/images/icons/help-small.png" alt="tip" /></a>
<!--<span class="notes">{t}(set to validate email upon subscription attempt){/t}</span>-->
</div>

<div class="formSpacing">
<label for="list_exchanger"><strong class="required">{t}Select Mail Exchanger:{/t}&nbsp;</strong>{fv message="list_exchanger"}</label>
<select name="list_exchanger">
<option value="sendmail"{if $list_exchanger == 'sendmail'} selected="selected"{/if}>Sendmail</option>
<option value="mail"{if $list_exchanger == 'mail'} selected="selected"{/if}>{t}PHP Mail Function{/t}</option>
<option value="smtp"{if $list_exchanger == 'smtp'} selected="selected"{/if}>{t}SMTP Relay{/t}</option>
</select>
&nbsp;&nbsp; - &nbsp;&nbsp; <a href="config/ajax.testexchanger.php" id="testTrigger">{t}Test Exchanger{/t}</a>
<!--<span class="notes">{t}(select mail exchanger){/t}</span>-->

<div class="hidden" id="configSMTP" style="padding-left: 338px;">
	<a href="config/ajax.smtp.php" id="smtpTrigger">{t}Setup your SMTP Servers{/t}</a>&nbsp;<a href="#" class="tooltip" title="{t}configure SMTP relays{/t}"><img src="{$url.theme.shared}/images/icons/help-small.png" alt="tip" /></a>
	<!--<span class="notes">{t}(configure SMTP relays){/t}</span>-->
</div>
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