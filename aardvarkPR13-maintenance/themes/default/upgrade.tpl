<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>{t}poMMo Installation{/t}</title>
	<link href="{$url.theme.this}inc/admin.css" type="text/css" rel="STYLESHEET">
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
	<h1>{t}poMMo Upgrader{/t}</h1>
	<h2>
		{t}Online Upgrade Script{/t}
	</h2>
</div>

<div id="content">


<div style="font-size: 155%; ">
<img src="{$url.theme.shared}images/icons/alert.png" style="float: left;" border='0'>
{t}Welcome to the poMMo online upgrade process. This script will automatically upgrade your old version of poMMo.{/t}
</div>

	{if $errors}
 		<br>
    	<div class="errdisplay">
    	{foreach from=$errors item=msg}
    	<div>* {$msg}</div>
    	{/foreach}
    	</div>
 	{/if}
 	

	{if $messages}
    	<div>
    	{foreach from=$messages item=msg}
    	<li>{$msg}</li>
    	{/foreach}
    	</div>
 	{/if}
 	
 <br>
 
 {if !$upgraded}
	<form action="" method="POST">
	<input type="hidden" name="debugInstall" value="true">
	<input type="hidden" name="continue" value="true">
 
		 {if $debug}
		 	<div style="float: right; text-align: right;">
		 		<strong>
		 			{t}To disable debugging{/t}
		 			<br><input type="submit" name="disableDebug" value="{t}Click Here{/t}">
		 		</strong>
			</div>
		 {else}
		 	<div style="float: right; text-align: right;">
		 		<strong>
		 			{t}To enable debugging{/t}
		 			<br><input type="submit" name="debugInstall" value="{t}Click Here{/t}">
		 		</strong>
			</div>
		 {/if}
 
 	</form>
 {/if}
 
{* Print Release Notes *}
{if $attempt && $upgraded} 
<pre>
{$notes}
</pre>
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