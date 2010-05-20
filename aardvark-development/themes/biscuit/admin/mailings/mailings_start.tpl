{capture name=head}{* used to inject content into the HTML <head> *}
<link type="text/css" rel="stylesheet" href="{$url.theme.shared}css/default.mailings.css" />

{include file="inc/ui.form.tpl"}
{include file="inc/ui.dialog.tpl"}
{include file="inc/ui.tabs.tpl"}

{* Include the WYSIWYG Javascripts *}
{foreach from=$wysiwygJS item=js}
	<script type="text/javascript" src="{$url.theme.shared}../wysiwyg/{$js}"></script>
{/foreach}
{/capture}
{include file="inc/admin.header.tpl" sidebar='off'}

<h2><img src="{$url.theme.shared}images/icons/send_mail.png" alt="send mail icon" class="navimage left" />{t}Send Email{/t}</h2>
{t}Create and send mailings to the entire list or to a subset of subscribers. Templates may also be created in this section.{/t}
<br /><br />
{t escape=no 1="<span class=\"required\">" 2="</span>"}%1Bold%2 fields are required.{/t}

{include file="inc/messages.tpl"}

<br class="clear" />

<div id="tabs">
	<ul>
	    <li><a href="mailing/setup.php"><span>{t}Setup{/t}</span></a></li>
	    <li><a href="mailing/templates.php"><span>{t}Templates{/t}</span></a></li>
	    <li><a href="mailing/compose.php"><span>{t}Compose{/t}</span></a></li>
	    <li><a href="mailing/preview.php"><span>{t}Preview{/t}</span></a></li>
	</ul>
</div>

<br class="clear" />
<br class="clear" />&nbsp;

{literal}
<script type="text/javascript">
$().ready(function(){ 
	// initialize tabs
	poMMo.tabs = PommoTabs.init('#tabs');
	
	// initialize dialog(s)
	PommoDialog.init();
});
</script>
{/literal}

{capture name=dialogs}
{include file="inc/dialog.tpl" id=dialog wide=true tall=true}
{/capture}

{include file="inc/admin.footer.tpl"}