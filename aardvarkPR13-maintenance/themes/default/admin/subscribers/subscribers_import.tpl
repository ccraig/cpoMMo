{include file="admin/inc.header.tpl"}
{include file="admin/inc.sidebar.tpl"}

<div id="mainbar">

<h1>{t}Import Subscribers{/t}</h1>

<img src="{$url.theme.shared}images/icons/cells.png" class="articleimg">

<p>
{t escape=no 1='<strong>' 2='</strong>'}poMMo supports importing subscribers from %1 CSV %2 files. Your CSV file should have one subscriber(email) per line with field information seperated by commas(,).{/t}
</p>

<br><br>
<p>
{t escape=no 1='<a href="http://www.openoffice.org">' 2='</a>'}Popular programs such as Microsoft Excel and %1 Open Office %2 support saving files in Comma-Seperated-Value format.{/t} 
</p>


<br>

<form enctype="multipart/form-data" action="" method="POST">

	<input type="hidden" name="MAX_FILE_SIZE" value="{$maxSize}" />
	
	<div align="center">
	 {t}Your CSV file:{/t}<input name="csvfile" type="file" />
	 &nbsp;&nbsp; <input type="submit" value="{t}Upload{/t}" />

	</div>

</form>

{if $messages}
    <div class="msgdisplay">
    {foreach from=$messages item=msg}
   	 <div>* {$msg}</div>
    {/foreach}
    </div>
 {/if}
 
	 
 
 </div>
<!-- end mainbar -->

{include file="admin/inc.footer.tpl"}