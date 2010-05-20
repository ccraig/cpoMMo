{include file="admin/inc.header.tpl"}
{include file="admin/inc.sidebar.tpl"}

<div id="mainbar">

 {if $messages}
    <div class="msgdisplay">
    {foreach from=$messages item=msg}
   	 <div>* {$msg}</div>
    {/foreach}
    </div>
 {/if}

<form action="" method="POST">

  <fieldset>
    <legend>{t}Mailing Parameters{/t}</legend>

		<div class="field">
			<div class="error">{validate id="fromname" message=$formError.fromname}</div>
			<label for="fromname"><span class="required">{t}From Name:{/t}</span></label>
			<input type="text" class="text" size="32" maxlength="60"
			  name="fromname" value="{$fromname|escape}" id="fromname" />
			<div class="notes">{t}(maximum of 60 characters){/t}</div>
		</div>

		<div class="field">
			<div class="error">{validate id="fromemail" message=$formError.fromemail}</div>
			<label for="fromemail"><span class="required">{t}From Email:{/t}</span></label>
			<input type="text" class="text" size="32" maxlength="60"
			  name="fromemail" value="{$fromemail|escape}" id="fromemail" />
			<div class="notes">{t}(maximum of 60 characters){/t}</div>
		</div>

		<div class="field">
			<div class="error">{validate id="frombounce" message=$formError.frombounce}</div>
			<label for="frombounce"><span class="required">{t}Bounce/Return:{/t}</span></label>
			<input type="text" class="text" size="32" maxlength="60"
			  name="frombounce" value="{$frombounce|escape}" id="frombounce" />
			<div class="notes">{t}(maximum of 60 characters){/t}</div>
		</div>

		<div class="field">
			<div class="error">{validate id="subject" message=$formError.subject}</div>
			<label for="subject"><span class="required">{t}Mailing Subject:{/t}</span></label>
			<input type="text" class="text" size="32" maxlength="60"
			  name="subject" value="{$subject|escape}" id="subject" />
			<div class="notes">{t}(maximum of 60 characters){/t}</div>
		</div>

		<div class="field">
			<div class="error">{validate id="ishtml" message=$formError.ishtml}</div>
			<label for="ishtml"><span class="required">{t}Mail Format:{/t}</span></label>
			<select name="ishtml" id="ishtml">
				<option value="plain" {if $ishtml == 'plain'}SELECTED{/if}>{t}Plain Text Mailing{/t}</option>
				<option value="html" {if $ishtml == 'html'}SELECTED{/if}>{t}HTML Mailing{/t}</option>
			</select>
			<div class="notes">{t}(Select the format of this mailing){/t}</div>
		</div>

		<div class="field">
			<div class="error">{validate id="mailgroup" message=$formError.mailgroup}</div>
			<label for="mailgroup"><span class="required">{t}Send Mail To:{/t}</span></label>
			<select name="mailgroup" id="mailgroup">
				<option value="all" {if $mailgroup == 'all'}SELECTED{/if}>{t}All subscribers{/t}</option>
				{foreach from=$groups item=group_name key=key}
					<option value="{$key}" {if $mailgroup == $key}SELECTED{/if}>{$group_name}</option>
				{/foreach}
			</select>
			<div class="notes">{t}(Select who should recieve the mailing){/t}</div>
		</div>

		<div class="field">
			<div class="error">{validate id="charset" message=$formError.charset}</div>
			<label for="charset"><span class="required">{t}Character Set:{/t}</span> </label>
			<select name="charset" id="charset">
				<option value="UTF-8" {if $charset == 'UTF-8'}SELECTED{/if}>{t}UTF-8 (recommended){/t}</option>
				<option value="ISO-8859-1" {if $charset == 'ISO-8859-1'}SELECTED{/if}>{t}western (ISO-8859-1){/t}</option>
				<option value="ISO-8859-2" {if $charset == 'ISO-8859-2'}SELECTED{/if}>{t}Central/Eastern European (ISO-8859-2){/t}</option>
				<option value="ISO-8859-7" {if $charset == 'ISO-8859-7'}SELECTED{/if}>{t}Greek (ISO-8859-7){/t}</option>
				<option value="ISO-8859-15" {if $charset == 'ISO-8859-15'}SELECTED{/if}>{t}western (ISO-8859-15){/t}</option>
				<option value="cp1251" {if $charset == 'cp1251'}SELECTED{/if}>{t}cyrillic (Windows-1251){/t}</option>
				<option value="KOI8-R" {if $charset == 'KOI8-R'}SELECTED{/if}>{t}cyrillic (KOI8-R){/t}</option>
				<option value="GB2312" {if $charset == 'GB2312'}SELECTED{/if}>{t}Simplified Chinese (GB2312){/t}</option>
				<option value="EUC-JP" {if $charset == 'EUC-JP'}SELECTED{/if}>{t}Japanese (EUC-JP){/t}</option>
			</select>
			<div class="notes">{t}(Select Character Set of Mailings){/t}</div>
		</div>

	</fieldset>

<div>
	<input  type="submit" class="button" id="submit" name="submit" value="Continue" />
</div>
<div style="margin-left: 5%; margin-top: 5px;">
	{t escape=no 1="<span class=\"required\">" 2="</span>"}Fields in %1bold%2 are required{/t}
</div>
</form>


</div>
<!-- end mainbar -->

{include file="admin/inc.footer.tpl"}
