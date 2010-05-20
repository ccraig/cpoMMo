{include file="inc/user.header.tpl"}

<h2>{t}Update Activation{/t}</h2>

{include file="inc/messages.tpl"}

{if $sent}

	<p>
	{t escape=no 1="<strong>`$email`</strong>"}We have sent an email to %1. Inside is a link allowing access to your records. Please check your inbox, as the letter will arrive shortly.{/t}
	</p>
	
{else}

	<p>
	{t}We require that you verify your email address before unsubscribing or updating your records. This extra step is necessary to maintain your privacy, and to protect you against fraudulent activity.{/t}
	</p>
	
	<p>
	{t escape=no 1="<a href='`$smarty.server.PHP_SELF`?send=true&email=`$email`'>" 2="</a>" 3="<strong>`$email`</strong>"}%1Click here%2 to send a verification email to %3.{/t}
	</p>
	
{/if}

{include file="inc/user.footer.tpl"}