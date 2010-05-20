{include file="inc/admin.header.tpl"}

<h2>{t}Support Page{/t}</h2>

<p><a href="support.lib.php">poMMo Support Library</a></p>

<p>poMMo version: {$version} +{$revision}</p>

<p><i>Coming to a theatre near you</i></p>

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