{include file="inc/admin.header.tpl" sidebar='off'}

{include file="inc/messages.tpl"}

{if $captcha}

<h2>{t}Administrative Login{/t}</h2>

<p>{t escape='no' 1='<tt>' 2='</tt>'}You have requested to reset your password. If you are sure you want to do this, please fill in the captcha below. Enter the text you see between the brackets (%1[]%2).{/t}</p>

<p><strong>{t}Captcha{/t}</strong> - <tt>[ {$captcha} ]</tt></p>

<form method="post" action="">
<input type="hidden"  name="realdeal" value="{$captcha}" />

<p>{t}Captcha Text:{/t} <input type="text" name="captcha" /></p>

<input type="submit" name="resetPassword" value="{t}Reset Password{/t}" />
</form>

{else}

<h3>{t}Administrative Login{/t}</h3>

<form method="post" action="">
<fieldset id="login">
<legend>Login</legend>

<input type="hidden" name="referer" value="{$referer}" />

<div>
<label for="username">{t}Username{/t}</label>
<input type="text" name="username" id="username" />
</div>

<div>
<label for="password">{t}Password{/t}</label>
<input type="password" name="password" id="password" />
</div>

</fieldset> 

<div class="buttons">

<input type="submit" name="submit" value="{t}Log In{/t}" />

<input type="submit" name="resetPassword" class="green" id="resetPassword" value="{t}Forgot your password?{/t}" />

</div>

</form>
{/if}

{include file="inc/admin.footer.tpl"}