{capture name=head}{* used to inject content into the HTML <head> *}
{include file="inc/ui.dialog.tpl"}
{/capture}{include file="inc/admin.header.tpl"}

<h2>{t}Embedded Subscription Forms{/t}</h2>

<ul class="inpage_menu">
<li><a href="setup_form.php">{t}Return to Subscription Forms{/t}</a></li>
</ul>

<p><img src="{$url.theme.shared}images/icons/embed.png" alt="embed" class="navimage right" /> {t}Subscription forms can easily be added to your website using a line of code. You can use the PHP include listed below, or embed the actual HTML. Remember, you can also direct subscribers to the {/t}<a href="{$url.base}user/subscribe.php">{t}Default Subscribe Form{/t}</a>.</p>

<h3>{t}Mini Subscription Form{/t}</h3>

<p>{t}This prints a form which prompts for a user's email address. If the inputted email exists as a registered subscriber, it redirects to the subscriber update page. If not, it redirects to the the default subscription form.{/t}</p>

<ul>
<li><a href="#" class="miniPreview">{t}Preview{/t}</a></li>
<li><a href="{$url.base}embed.miniform.php">{t}URL{/t}</a></li>
</ul>

<p>PHP: <tt style="color: green;">include('{$config.app.path}embed.miniform.php');</tt></p>


<h3>{t}Full Subscription Form{/t}</h3>

<p>{t}This prints the entire subscription form.{/t}</p>

<ul>
<li><a href="#" class="fullPreview">{t}Preview{/t}</a></li>
<li><a href="{$url.base}embed.form.php">{t}URL{/t}</a></li>
</ul>

<p>PHP: <tt style="color: green;">include('{$config.app.path}embed.form.php');</tt></p>

{literal}
<script type="text/javascript">
$().ready(function(){
	$('#miniPreview').jqm({
		trigger: 'a.miniPreview'
	});
	
	$('#fullPreview').jqm({
		trigger: 'a.fullPreview'
	});
});
</script>
{/literal}

{capture name=mini}
	<h4>{t}Mini Subscription Form{/t} {t}Preview{/t}</h4>
	
	{include file="subscribe/form.mini.tpl"}
	
	<hr />
	
	<h4>HTML Source</h4>
	
	<textarea cols="60" rows="11">{include file="subscribe/form.mini.tpl"}</textarea>

	<br /><br />&nbsp;
{/capture}


{capture name=full}
	<h4>{t}Subscription Form{/t} {t}Preview{/t}</h4>
	
	{include file="subscribe/form.subscribe.tpl"}
	
	<hr />
	
	<h4>HTML Source</h4>
	
	<textarea cols="60" rows="11">{include file="subscribe/form.subscribe.tpl"}</textarea>

	<br /><br />&nbsp;
{/capture}

{capture name=dialogs}
{include file="inc/dialog.tpl" id="miniPreview" content=$smarty.capture.mini wide=true tall=true}
{include file="inc/dialog.tpl" id="fullPreview" content=$smarty.capture.full wide=true tall=true}
{/capture}

{include file="inc/admin.footer.tpl"}