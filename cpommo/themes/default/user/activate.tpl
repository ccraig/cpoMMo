{include file="inc/user.header.tpl"}

<h2>{t}Update Activation{/t}</h2>

<p>
{t}We require that you verify your email address before unsubscribing or updating your records. This extra step is necessary to maintain your privacy, and to protect you against fraudulent activity.{/t}
</p>

{include file="inc/messages.tpl"}

{if !$sent}
	<p>
	{t escape=no 1="<strong>`$email`</strong>"}An activation email has recently been sent to %1. Please check your inbox.{/t}
	</p>
{/if}

{include file="inc/user.footer.tpl"}