{include file="inc/admin.header.tpl" sidebar='off'}

{include file="inc/messages.tpl"}

{if $captcha}

<img src="{$url.theme.shared}images/main_image.jpg" width="960" height="200" alt="pommo image" />

<h2>{t}Administrative Login{/t}</h2>
<div>
<p>{t}You have requested to reset your password. If you are sure you want to do this, please fill in the captcha with the red text listed in the box below.{/t}</p>

<p>{t}Captcha Text:{/t}&nbsp;&nbsp;<span class="captcha">{$captcha}</span></p>

<form method="post" action="">
<input type="hidden"  name="realdeal" value="{$captcha}" />

<p>{t}Enter Captcha: {/t}<input name="captcha" type="text" size="9" maxlength="10" /></p>

<div class="buttons">
<button type="submit" name="resetPassword" value="{t}Reset Password{/t}" class="positive"><img src="{$url.theme.shared}images/icons/textfield_key.png" alt="reset password"/>{t}Reset Password{/t}</button>
<a href="{$config.site_url}/pommo/index.php" title="{t}Back to Login{/t}"><img src="{$url.theme.shared}images/icons/reload-small.png" alt="text field button"/>{t}Back to Login{/t}</a>
</div>
</form>
</div>

{else}

<img src="{$url.theme.shared}images/main_image.jpg" width="960" height="200" alt="pommo image" />

<ul id="sec_nav">
	<li><img src="{$url.theme.shared}images/icons/mailing.png" alt="mailings" />
	<h3>{t}Mailings{/t}</h3>
	<span>{t}Designed to be easy to use and powerful, poMMo provides flexible Mailing Management for everyone. It is written in PHP and freely provided under the [GPL].{/t}</span>
	</li>
    
	<li><img src="{$url.theme.shared}images/icons/subscribers.png" alt="subscribers" />
	<h3>{t}Subscribers{/t}</h3>
	<span>{t}Have a list of 100,000? No Problem. From the ground up poMMo has been designed with flexibility and scalable optimization in mind.{/t}</span>
	</li>

	<li><img src="{$url.theme.shared}images/icons/login.png" alt="login" />
	<h3>{t}Administrative Login{/t}</h3>
    <span>
    	<form method="post" action="">
		<input type="hidden" name="referer" value="{$referer}" />

		<div>
		<label for="username">{t}Username:{/t}&nbsp;</label>
		<input name="username" type="text" id="username" class="autoTab" tabindex="1" size="15" maxlength="60" />
		</div>

		<div>
		<label for="password">{t}Password:{/t}&nbsp;&nbsp;</label>
		<input name="password" type="password" id="password" class="autoTab" tabindex="2"size="15" maxlength="60" />
		</div>

		<div class="buttons">
		<button type="submit" name="submit" value="{t}Log In{/t}" class="positive"><img src="{$url.theme.shared}images/icons/tick.png" alt="login"/>{t}Login{/t}</button>
		<button type="submit" name="resetPassword" id="resetPassword" value="{t}Forgot your password?{/t}">{t}Password?{/t}</button>
		</div>

		</form>
	</span>
	</li>
</ul>

{/if}

{include file="inc/admin.footer.tpl"}