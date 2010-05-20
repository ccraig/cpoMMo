{capture name=head}{* used to inject content into the HTML <head> *}
{include file="inc/ui.form.tpl"}
{include file="inc/ui.dialog.tpl"}
{include file="inc/ui.tabs.tpl"}
{include file="inc/ui.slider.tpl"}
{/capture}

{include file="inc/admin.header.tpl" sidebar='off'}

<ul class="inpage_menu">
<li><a href="admin_setup.php" title="{t}Return to Setup Page{/t}">{t}Return to Setup Page{/t}</a></li>
</ul>

<h2>{t}Configure{/t}</h2>

<p><img src="{$url.theme.shared}images/icons/settings.png" alt="settings icon" class="navimage right" /> {t}You can change the login information, set website and mailing list parameters, end enable demonstration mode. If you enable demonstration mode, no emails will be sent from the system.{/t}</p>

{include file="inc/messages.tpl"}

<br class="clear">

<div id="tabs">
	<ul>
	    <li><a href="config/users.php"><span>{t}Users{/t}</span></a></li>
	    <li><a href="config/general.php"><span>{t}General{/t}</span></a></li>
	    <li><a href="config/mailings.php"><span>{t}Mailings{/t}</span></a></li>
	    <li><a href="config/messages.php"><span>{t}Messages{/t}</span></a></li>
	</ul>
</div>

<br class="clear">
<br class="clear">&nbsp;

{literal}
<script type="text/javascript">
$().ready(function(){ 

	PommoDialog.init();
	
	poMMo.tabs = PommoTabs.init('#tabs');
	// override changeTab function
	PommoTabs.change = function() { return true; };
	
	{/literal}
	{if $smarty.get.tab}
	  var hash = "#{$smarty.get.tab|lower}";
	{else}
	  var hash = location.hash.toLowerCase();
	{/if}
	{literal}
	
	switch(hash) {
		case '#users': $('#tabs li a:eq(0)').click();
			break;
		case '#general':  $('#tabs li a:eq(1)').click();
			break;
		case '#mailings':  $('#tabs li a:eq(2)').click();
			break;
		case '#messages':  $('#tabs li a:eq(3)').click();
			break;
	}
	
});

</script>
{/literal}

{capture name=dialogs}
{include file="inc/dialog.tpl" id="throttleWindow" title=$throttleTitle tall=true}
{include file="inc/dialog.tpl" id="smtpWindow" title=$smtpTitle tall=true}
{include file="inc/dialog.tpl" id="testWindow" title=$testTitle}
{/capture}

{include file="inc/admin.footer.tpl"}
