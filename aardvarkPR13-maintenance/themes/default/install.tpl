<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>{t}poMMo Installation{/t}</title>
	<link href="{$url.theme.this}inc/admin.css" type="text/css" rel="STYLESHEET">
	<link href="{$url.theme.shared}css/bform.css" type="text/css" rel="STYLESHEET">
	<script src="{$url.theme.shared}js/bform.js" type="text/javascript"></script>
{* The following fixes transparent PNG issues in IE < 7 *}
	<!--[if lt IE 7.]>
		<script defer type="text/javascript" src="{$url.theme.shared}js/pngfix.js"></script>
	<![endif]-->
</head>
<body>

<a name="top" id="top"></a>
<center>

<div id="menu">
	{$config.app.weblink}
	<a href="{$url.base}admin/admin.php">{t}Admin Page{/t}</a>
</div>

<div id="header">
	<h1>{t}poMMo Installation{/t}</h1>
	<h2>
		{t}Online install{/t}
	</h2>
</div>

<div id="content">


<div style="font-size: 155%; ">
<img src="{$url.theme.shared}images/icons/alert.png" style="float: left;" border='0'>
{t}Welcome to the poMMo online installation process. We have connected to the database and set your language successfully. Fill in the values below, and you'll be on your way!{/t}
</div>

	{if $messages}
    	<div class="msgdisplay">
    	{foreach from=$messages item=msg}
    	<div>* {$msg}</div>
    	{/foreach}
    	</div>
 	{/if}
 	
 	{if $errors}
 		<br>
    	<div class="errdisplay">
    	{foreach from=$errors item=msg}
    	<div>* {$msg}</div>
    	{/foreach}
    	</div>
 	{/if}
 
 {if !$installed}
 <form action="" method="POST">
 {if $debug}
 <input type="hidden" name="debugInstall" value="true">
 <div style="float: right; text-align: right;">
 <strong>
 	{t}To disable debugging{/t}
 	<br><input type="submit" id="disableDebug" name="disableDebug" value="{t}Click Here{/t}">
 </strong>
</div>
 {else}
 <div style="float: right; text-align: right;">
 <strong>
 	{t}To enable debugging{/t}
 	<br><input type="submit" id="debugInstall" name="debugInstall" value="{t}Click Here{/t}">
 </strong>
</div>
 {/if}


	<fieldset>
		<legend>{t}Configuration Options{/t}</legend>
	
		<div class="field">
			<div class="error">{validate id="list_name" message=$formError.list_name}</div>
			<label for="list_name"><span class="required">{t}Name of Mailing List:{/t} </span></label>
			<input type="text" class="text" size="32" maxlength="60"
			  name="list_name" value="{$list_name|escape}" id="list_name" />
			 <div class="notes">{t}(ie. Brice's Mailing List){/t}</div>
		</div>
		
		<div class="field">
			<div class="error">{validate id="site_name" message=$formError.site_name}</div>
			<label for="site_name"><span class="required">{t}Name of Website:{/t} </span></label>
			<input type="text" class="text" size="32" maxlength="60"
			  name="site_name" value="{$site_name|escape}" id="site_name" />
			  <div class="notes">{t}(ie. The poMMo Website){/t}</div>
		</div>
		
		<div class="field">
			<div class="error">{validate id="site_url" message=$formError.site_url}</div>
			<label for="site_url"><span class="required">{t}Website URL:{/t} </span></label>
			<input type="text" class="text" size="32" maxlength="60"
			  name="site_url" value="{$site_url|escape}" id="site_url" />
			  <div class="notes">{t}(ie. http://www.pommo-rocks.com/){/t}</div>
		</div>
		
		<div class="field">
			<div class="error">{validate id="admin_password" message=$formError.admin_password}</div>
			<label for="admin_password"><span class="required">{t}Administrator Password:{/t} </span></label>
			<input type="text" class="text" size="32" maxlength="60"
			  name="admin_password" value="{$admin_password|escape}" id="admin_password" />
			  <div class="notes">{t}(you will use this to login){/t}</div>
		</div>
		
		<div class="field">
			<div class="error">{validate id="admin_password2" message=$formError.admin_password2}</div>
			<label for="admin_password2"><span class="required">{t}Verify Password:{/t} </span></label>
			<input type="text" class="text" size="32" maxlength="60"
			  name="admin_password2" value="{$admin_password2|escape}" id="admin_password2" />
			  <div class="notes">{t}(enter password again){/t}</div>
		</div>
		
		<div class="field">
			<div class="error">{validate id="admin_email" message=$formError.admin_email}</div>
			<label for="admin_email"><span class="required">{t}Administrator Email:{/t} </span></label>
			<input type="text" class="text" size="32" maxlength="60"
			  name="admin_email" value="{$admin_email|escape}" id="admin_email" />
			  <div class="notes">{t}(enter your valid email address){/t}</div>
		</div>
		
	</fieldset>
	
<div style="margin-left: 15%; margin-top: 15px;">
	<input class="button" type="submit" id="installerooni" name="installerooni" value="{t}Install{/t}" />
</div>

</form>
{/if}

<br><br>
<a href="{$url.base}index.php">
		<img src="{$url.theme.shared}images/icons/back.png" align="middle" class="navimage" border='0'>
		{t}Continue to login page{/t}</a>
<br>
 
</div>
<!-- end content -->

<p class="clearer"></p>

<div id="footer">
&nbsp;<br />
 {t escape="no" url='<a href="http://pommo.sourceforge.net/">poMMo</a>'}Page fueled by %1 mailing management software.{/t}
</div>
<!-- end footer -->

</center>

</body>
</html>
