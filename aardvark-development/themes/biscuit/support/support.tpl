{include file="inc/admin.header.tpl"}

<h2>{t}Support{/t}</h2>

<ul id="sec_nav">
	<li><a href="{$url.base}support/support.notes.php"><img src="{$url.theme.shared}images/icons/notes.png" alt="notes icon" class="navimage" />
	<h3>{t}Support Notes{/t}</h3>
	<span>{t}Find current poMMo version and notes from the developer.{/t}</span><br />
    <br />
    <span><strong>{t}poMMo version:{/t}</strong></span><br />
     <span>{t}{$version} +{$revision}{/t}</span></a>
	</li>
    
	<li><a href="{$url.base}support/support.lib.php"><img src="{$url.theme.shared}images/icons/library.png" alt="library icon" class="navimage" />
	<h3>{t}Support Library{/t}</h3>
	<span>{t}Perform tasks such as Testing Mailing Processor, Terminate Current Mailing, Reset Database, as well as, other maintenance actions.{/t}</span></a>
	</li>

	<li>&nbsp;
	<h3>&nbsp;</h3>
    <span>&nbsp;</span>
	</li>
</ul>

{include file="inc/admin.footer.tpl"}