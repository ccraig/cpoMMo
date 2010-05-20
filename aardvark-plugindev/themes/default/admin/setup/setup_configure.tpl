{capture name=head}{* used to inject content into the HTML <head> *}
<script type="text/javascript" src="{$url.theme.shared}js/jq/jq11.js"></script>
<script type="text/javascript" src="{$url.theme.shared}js/jq/accordion.js"></script>
<script type="text/javascript" src="{$url.theme.shared}js/jq/interface.js" ></script>
<script type="text/javascript" src="{$url.theme.shared}js/jq/form.js"></script>
<script type="text/javascript" src="{$url.theme.shared}js/jq/jqModal.js"></script>
<link type="text/css" rel="stylesheet" href="{$url.theme.shared}css/modal.css" />
<link type="text/css" rel="stylesheet" href="{$url.theme.shared}css/accordion.css" />
<link type="text/css" rel="stylesheet" href="{$url.theme.shared}css/slider.css" />
{/capture}
{include file="inc/admin.header.tpl"}

<h2>{t}Configure{/t}</h2>

<p><img src="{$url.theme.shared}images/icons/settings.png" alt="settings icon" class="navimage right" /> {t}You can change the login information, set website and mailing list parameters, end enable demonstration mode. If you enable demonstration mode, no emails will be sent from the system.{/t}</p>

{include file="inc/messages.tpl"}

<br clear="right" />

<div id="setup">
 
 <div>
   <div class="acMenu acFirst">{t}Users{/t}</div>
   <div class="acBody acFirst ajaxLoad">
   <a href="ajax/config_users.php"><img src="{$url.theme.shared}images/loader.gif" alt="Loading Icon" title="Please Wait" border="0" /></a> {t}Please Wait{/t}...
  </div>
 </div>
 
 <div>
   <div class="acMenu">{t}General{/t}</div>
   <div class="acBody ajaxLoad">
   	<a href="ajax/config_general.php"><img src="{$url.theme.shared}images/loader.gif" alt="Loading Icon" title="Please Wait" border="0" /></a> {t}Please Wait{/t}...
   </div>
 </div>
 
 <div>
   <div class="acMenu">{t}Mailings{/t}</div>
   <div class="acBody ajaxLoad">
   <a href="ajax/config_mailings.php"><img src="{$url.theme.shared}images/loader.gif" alt="Loading Icon" title="Please Wait" border="0" /></a> {t}Please Wait{/t}...
   </div>
 </div>
 
 <div>
   <div class="acMenu">{t}Messages{/t}</div>
   <div class="acBody ajaxLoad">
   <a href="ajax/config_messages.php"><img src="{$url.theme.shared}images/loader.gif" alt="Loading Icon" title="Please Wait" border="0" /></a> {t}Please Wait{/t}...
   </div>
 </div>
 
</div>

<br class="clear" />
</div>
<!-- end content (no footer)-->

{include file="inc/dialog.tpl" dialogID="throttleWindow" dialogTitle=$throttleTitle dialogDrag=true dialogClass="configWindow" dialogBodyClass="jqmdTall"}
{include file="inc/dialog.tpl" dialogID="smtpWindow" dialogTitle=$smtpTitle dialogDrag=true dialogClass="configWindow" dialogBodyClass="jqmdTall"}
{include file="inc/dialog.tpl" dialogID="testWindow" dialogTitle=$testTitle dialogDrag=true dialogClass="configWindow"}

{literal}
<style>
div.configWrap {
	position: absolute;
	top: 30%;
	left: 50%;
}

div.configWindow {
	left: -240px;
	width: 480px;
}
</style>

<script type="text/javascript">

$().ready(function(){ 

	$('#setup').Accordion({
		header: 'div.acMenu'
	});
	
	$('#setup div.acMenu').hover(
		function() { $(this).css('text-decoration','underline'); },
		function() { $(this).css('text-decoration','none'); });
	
	$('div.ajaxLoad').each(function() {
		var url = $('a',this).click(function(){ return false; }).attr('href');
		$(this).load(url,function(){ assignForm(this); });
	});
	
});

function assignForm(scope) {
	$('form',scope).ajaxForm( { 
		target: scope,
		beforeSubmit: function() {
			$('input[@type=submit]', scope).hide();
			$('img[@name=loading]', scope).show();
		},
		success: function() { 
			assignForm(this); 
			$('div.output',this).fadeTo(1800,1).fadeTo(5000,0); }
		}
	);
}
</script>

</script>
{/literal}