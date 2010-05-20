{include file="inc/admin.header.tpl"}

<h2>{t}Import Subscribers{/t}</h2>

<fieldset>
<legend>{t}Import{/t}</legend>

<div>
{t 1=$tally 2=$dupes}%1 non-duplicate subscribers will be imported. %2 were ignored as duplicates.{/t}

{if $flag}
<p class="warn">{t}Notice: Imported subscribers will be flagged to update their records{/t}</p>
{/if}

</div>

<div class="buttons" id="buttons">
{t}Are you sure?{/t}
<a href="#" id="import"><button>{t}Yes{/t}</button></a>
<a href="subscribers_import.php"><button>{t}No{/t}</button></a>
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