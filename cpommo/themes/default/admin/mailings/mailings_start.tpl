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

<ul class="inpage_menu">
<li><a href="admin_mailings.php" title="{t}Return to Subscribers Page{/t}">{t}Return to Mailings Page{/t}</a></li>
</ul>

{include file="inc/messages.tpl"}

<hr />
        
<div id="tabs">
	<ul>
	    <li><a href="mailing/setup.php"><span>{t}Setup{/t}</span></a></li>
	    <li><a href="mailing/templates.php"><span>{t}Templates{/t}</span></a></li>
	    <li><a href="mailing/compose.php"><span>{t}Compose{/t}</span></a></li>
	    <li><a href="mailing/preview.php"><span>{t}Preview{/t}</span></a></li>
	</ul>
</div>

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