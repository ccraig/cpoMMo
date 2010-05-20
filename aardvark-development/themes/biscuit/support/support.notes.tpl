{include file="inc/admin.header.tpl"}

<h2><img src="{$url.theme.shared}images/icons/notes.png" alt="notes icon" class="navimage left" />{t}Support Notes{/t}</h2>

{t}poMMo version: {$version} +{$revision}{/t}

<p><i>{t}Coming to a theatre near you{/t}</i></p>

<br class="clear"/>

<h3>My NOTES:</h3>

<pre>
+ Enhanced support library
+ PHPInfo()  (or specifically mysql, php, gettext, safemode, webserver, etc. versions)
+ Database dump (allow selection of tables.. provide a dump of them)
+ Link to README.HTML  +  local documentation
+ Link to WIKI documentation
	+ Make a user-contributed open WIKI documentation system
	+ When support page is clicked, show specific support topics for that page
+ Clear All Subscribers
+ Reset Database
+ Backup Database
+ Ensure max run time is 30 seconds if safe mode is enabled
</pre>

{include file="inc/admin.footer.tpl"}