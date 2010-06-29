{include file="inc/configure.header.tpl" sidebar='off'}

{include file="inc/messages.tpl"}

<h3>{t}Configure{/t}</h3>

<form method="post" action="">
<fieldset id="login">
<legend>You need to configure pommo before being able to use it</legend>

<div>
<label for="dbhost">{t}Database Host{/t}</label>
<input type="text" name="dbhost" id="dbhost" value="{$dbhost}" />
</div>

<div>
<label for="dbname">{t}Database Name{/t}</label>
<input type="text" name="dbname" id="dbname" value="{$dbname}" />
</div>

<div>
<label for="dbuser">{t}Database User{/t}</label>
<input type="text" name="dbuser" id="dbuser" value="{$dbuser}" />
</div>

<div>
<label for="dbpass">{t}Database Password{/t}</label>
<input type="password" name="dbpass" id="dbpass" />
</div>

</fieldset> 

<div class="buttons">

<input type="hidden" name="configure" value="1">

<input type="submit" name="submit" value="{t}Continue{/t}" />

</div>

</form>

{include file="inc/admin.footer.tpl"}
