{include file="inc/admin.header.tpl"}

<h2><img src="{$url.theme.shared}images/icons/import.png" class="navimage left" alt="import subscribers"/>{t}Import Subscribers{/t}</h2>

{t escape=no 1='<tt>' 2='</tt>'}Welcome to Subscriber Import! You can import subscribers from a list of email addresses or from a full fledged CSV file containing subscriber field values. CSV files should have one subscriber (email) per line with field information seperated by commas(%1,%2).{/t} {t escape=no 1='<a href="http://www.openoffice.org/">' 2='</a>'} Popular programs like Microsoft Excel and %1 Open Office%2 support saving files in CSV (Comma-Seperated-Value) format.{/t}

<br class="clear"/>

<fieldset>
<h3>{t}Import{/t}</h3>

<div>
{t 1=$tally 2=$dupes}%1 non-duplicate subscribers will be imported. %2 were ignored as duplicates.{/t}

{if $flag}
<p class="warn">{t}Notice: Imported subscribers will be flagged to update their records{/t}</p>
{/if}

</div>


{t}Are you sure?{/t}<br /><br />
<div class="buttons">
<a href="#" id="import" class="positive"><img src="{$url.theme.shared}images/icons/tick.png" alt="yes"/>{t}Yes{/t}</a>
<a href="subscribers_import.php" class="negative"><img src="{$url.theme.shared}images/icons/cross.png" alt="no"/>{t}No{/t}</a>
</div>
</fieldset>

{* Vs. all the style="display: none;" can we have a "hide" class?  -- e.g. <div class="hide"></div> || <p class="hide"></p> etc? *}
<div id="ajax" class="warn hidden">
<img src="{$url.theme.shared}images/loader.gif" alt="Importing..." />... {t}Processing{/t}
</div>


{literal}
<script type="text/javascript">
$().ready(function(){
	$('#import').click(function() {
		
		$('#buttons').hide();
		
		$('#ajax').show().load('import_txt.php?continue=true',{}, function() {
			$('#ajax').removeClass('warn').addClass('error');
		});
		
		return false;
	
	});
});
</script>
{/literal}
{include file="inc/admin.footer.tpl"}