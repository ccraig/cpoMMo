<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>

	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>{$title}</title>
	
	<link href="{$url.theme.this}inc/user.css" type="text/css" rel="STYLESHEET">

	{* If $head has been captured, print its contents here. Capture $head via templates
		using {capture name=head}..content..{/capture} before including this header file. 
		Useful for properly including javascripts and CSS in the HTML <head> *}
	{$smarty.capture.head}

	{* Include HTML FORM styling and javascript from shared theme directory when template
		is prepared to include a form from the parent PHP script *}    
	{if $isForm}
	<link href="{$url.theme.shared}css/bform.css" type="text/css" rel="STYLESHEET">
	<script src="{$url.theme.shared}js/bform.js" type="text/javascript"></script>
	{/if}

{* The following fixes transparent PNG issues in IE < 7 *}
	<!--[if lt IE 7.]>
		<script defer type="text/javascript" src="{$url.theme.shared}js/pngfix.js"></script>
	<![endif]-->
	
</head>

<body>

<a name="top" id="top"></a>
<center>

<div id="menu"><a href="{$config.site_url}">{$config.site_name}</a></div>

<div id="content">