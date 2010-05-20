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
<a href="http://www.iceburg.net/pommo/">{t}The poMMo Website{/t}</a>
</div>
<!-- end menu -->
		
<div id="content">

<div id="sidebar">

<img src="{$url.theme.shared}images/pommo.png" alt="poMMo Logo" class="logo" />
<h1>{t}Links{/t}</h1>
<div class="submenu"><a href="http://www.iceburg.net/pommo/">{t}The poMMo Website{/t}</a> 
</div>

<!-- end submenu -->	

<p><img src="{$url.theme.shared}images/icons/security.png" class="sideimg"></p>								
</div>
<!-- end sidebar -->		


<div id="mainbar">
<h1>{t}Administrative Login{/t}</h1>


{if $messages}
   	<div class="msgdisplay">
   	{foreach from=$messages item=msg}
   	<div>* {$msg}</div>
   	{/foreach}
   	</div>
{/if}
 	
{if $captcha}
	
<p>
{t}You have requested to reset your password. If you are sure you want to do this, please fill in the captcha below. Enter the text you see between the brackets ([]).{/t}
</p>

<b>{t}Captcha{/t}</b> - [ {$captcha} ] <br><br>

<form method="post" action="">
<input type="hidden"  name="realdeal" value="{$captcha}">
 
{t}Captcha Text:{/t} <input type="text" name="captcha"><br><br>
 
<input type="submit" name="resetPassword" value="{t}Reset Password{/t}">
</form>

{else}
	<div class="errMsg">
	{t}You must first login before accessing this area.{/t}
	</div>
	
	<form method="post" action="">
	
	<input type="hidden" name="referer" value="{$referer}">
	
	{t}USERNAME{/t}  <input type="text" name="username"><br>
	{t}PASSWORD{/t}  <input type="password" name="password"><br><br>
	 
	<input type="submit" name="submit" value="{t}Log In{/t}">
	
	</form>
	 
	<br>
	
	<form method="post" action="">
	<div class="errMsg">
	{t}Forgot your password?{/t}  <input style="margin-left: 30px;" type="submit" name="resetPassword" value="{t}Click Here{/t}" /> {t}to reset it{/t}
	</div>
	</form>
{/if}



</div>
<!-- end mainbar -->


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