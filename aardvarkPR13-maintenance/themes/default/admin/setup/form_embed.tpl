{capture name=head}{* used to inject content into the HTML <head> *}
<script src="{$url.theme.shared}js/scriptaculous/prototype.js" type="text/javascript"></script>
<script src="{$url.theme.shared}js/lightbox.js" type="text/javascript"></script>
{/capture}

{include file="admin/inc.header.tpl"}
{include file="admin/inc.sidebar.tpl"}

<div id="mainbar">

	<h1>{t}Embedded Subscription Forms{/t}</h1>
	<img src="{$url.theme.shared}images/icons/embed.png" class="articleimg">

	<p>
		{t}Subscription forms can easily be added to your website using a line of code. You can use the PHP include listed below, or embed the actual HTML. Remember, you can also direct subscribers to the {/t}
		<a href="{$url.base}user/subscribe.php">{t}Default Subscribe Form{/t}</a>.
	</p>
	

<a href="setup_form.php">
		<img src="{$url.theme.shared}images/icons/back.png" align="middle" border='0'>
		{t}Return to Subscription Forms{/t}</a>

<br><br>

{literal}
<style>
	.bgEven {
		background-color: #E5F6F2;
	}
	.bgOdd {
		background-color: #F3FBF9;
	}
	.bgHeader {
		color: #FFFFFF;
		background-color:#94AE62;
	}
	
.lightbox {
	color: #333;
	display: none;
	position: absolute;
	top: 15%;
	left: 15%;
	width: 70%;
	height: 65%;
	padding: 1em;
	border: 1em solid #B8B8B8;
	background-color: white;
	text-align: left;
	z-index:1001;
	overflow: auto;	
}

#overlay{
	display:none;
	position:absolute;
	top:0;
	left:0;
	width:100%;
	height:100%;
	z-index:1000;
	background-color:#333;
	-moz-opacity: 0.8;
	opacity:.80;
	filter: alpha(opacity=80);
}
</style>
{/literal}

<table width="100%" cellspacing="0" cellpadding="4">
<tr class="bgHeader">
	<td><strong>{t}Mini Subscription Form{/t}</strong></td>
</tr>
<tr class="bgOdd">
	<td style="text-align: right;">
		<strong>
			<span style="margin-right: 20px;">
				<a href="#miniPreview" rel="miniPreview" class="lbOn">{t}Preview{/t}</a>
			</span>
			<span style="margin-right: 20px;">
				<a href="{$url.base}embed.miniform.php">{t}URL{/t}</a>
			</span> 
		</strong>
	</td>
</tr>
<tr class="bgEven">
	<td>{t}This prints a form which prompts for a user's email address. If the inputted email exists as a registered subscriber, it redirects to the subscriber update page. If not, it redirects to the the default subscription form.{/t}</td>
</tr>
<tr class="bgOdd">
	<td><strong>PHP</strong> -> include('{$url.base}embed.miniform.php');</td>
</tr>
<tr>
<td style="height: 20px"></td>
</tr>
<tr class="bgHeader">
	<td><strong>{t}Full Subscription Form{/t}</strong></td>
</tr>
<tr class="bgOdd">
	<td style="text-align: right;">
		<strong>
			<span style="margin-right: 20px;">
				<a href="#fullPreview" rel="fullPreview" class="lbOn">{t}Preview{/t}</a>
			</span> 
			<span style="margin-right: 20px;">
				<a href="{$url.base}embed.form.php">{t}URL{/t}</a>
			</span> 
		</strong>
	</td>
</tr>
<tr class="bgEven">
	<td>{t}This prints the entire subscription form.{/t}</td>
</tr>
<tr class="bgOdd">
	<td><strong>PHP</strong> -> include('{$url.base}embed.form.php');</td>
</tr>
</table>

<div id="miniPreview" class="lightbox">

	<h1>{t}Mini Subscription Form{/t} {t}Preview{/t}</h1>
	
	{include file="subscribe/form.mini.tpl"}
	
	<br>
	<strong>PHP Include</strong>
	<br>
	"include('{$url.base}embed.mini.php');"
	
	<br><br>
	<strong>HTML Source</strong>
	<br>
	<textarea cols="70" rows="15">
	{include file="subscribe/form.mini.tpl"}
	</textarea>
	
	<p class="footer">
		<a href="#" class="lbAction" rel="deactivate">Close</a>
	</p>
	
</div>

<div id="fullPreview" class="lightbox">
	
	<a href="#" style="float: right;" class="lbAction" rel="deactivate">Close</a>
	
	<h1>{t}Subscription Form{/t} {t}Preview{/t}</h1>
	
	
	{include file="subscribe/form.subscribe.tpl"}
	
	<br>
	<strong>PHP Include</strong>
	<br>
	"include('{$url.base}embed.form.php');"
	
	<br><br>
	<strong>HTML Source</strong>
	<br>
	<textarea cols="70" rows="15">
	{include file="subscribe/form.subscribe.tpl"}
	</textarea>
	
	<p class="footer">
		<a href="#" class="lbAction" rel="deactivate">Close</a>
	</p>
	
</div>


</div>
<!-- end mainbar -->

{include file="admin/inc.footer.tpl"}